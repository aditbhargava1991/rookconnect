<?php include_once('../include.php'); ?>

<style>
.new_task_box { border:1px solid #ACA9A9; margin:6px !important; padding:10px !important; }
.flag_color_box{background-color: #fff;padding: 10px;min-width: 250px;position: absolute;left: 20px;top: 110px;z-index: 1;border: 2px solid #878787;}
</style>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.css" rel="stylesheet"> -->
<script>
//$(document).ready(function(){
	//$('.demo_cpicker').colorpicker();
//});
</script>
<script src="../Project/project.js"></script>
<script>
$(document).ready(function(){
	//$('.demo_cpicker').colorpicker();
});


function flag_item_box(taskid){
	//$('#flag_color_box_'+taskid).show();
	$('#colorpickerbtn_'+taskid)[0].click();
}

function flag_item(task,flag_colour) {
	//task_id = $(task).parents('span').data('task');
	//task_id = $('#flag_color_box_'+task).next('span').data('task');
	task_id = task;
	//flag_colour = $('#demo_'+task_id).val();
	flag_colour = flag_colour. substring(1, flag_colour. length);
	$.ajax({
		method: "POST",
		//url: "task_ajax_all.php?fill=taskflag",
		url: "../Project/projects_ajax.php?action=project_actions",
		data: { table:'tasklist', id_field:'tasklistid', field:'flag_colour', id: task_id, new_colour:flag_colour },
		complete: function(result) {
			$('li[data-id="'+task_id+'"]').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			//$('#flag_color_box_'+task_id).hide();
		}
	});
}

function highlight_item(task) {
    $('.color_picker').click();
}

function choose_color(sel) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    var task_id = arr[1];
    var taskcolor = sel.value;
	var taskcolor = taskcolor.replace("#", "");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=task_highlight&tasklistid="+arr[1]+'&taskcolor='+taskcolor,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
<?php $border_colour = '';
$label = $date = $link = $contents = $li_class = $flag_label = $item_external = '';
if(!isset($item)) {
	$item = ['Task',filter_var($_GET['taskid'],FILTER_SANITIZE_STRING)];
	$li_class = 'new-items ';
	$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
	$task_statuses = explode(',',get_config($dbc, 'task_status'));
	$status_complete = $task_statuses[count($task_statuses) - 1];
	$status_incomplete = $task_statuses[0];
	$ticket_security = get_security($dbc, 'ticket');
	$security = get_security($dbc, 'project');
	$ticket_field_config = array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets_dashboard` FROM field_config"))['tickets_dashboard']));
}

if($fromTasks == 1) {
	$type = 'Task';
}
else {
	$type = $item[0];
}
$team = [];
if($type == 'Ticket') {
	$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='".$item[1]."'"));
	$data = 'data-id="'.$item['ticketid'].'" data-table="tickets" data-name="milestone_timeline" data-id-field="ticketid"';
    $team = explode(',',$item['contactid'].','.$item['internal_qa_contactid'].','.$item['deliverable_contactid']);
	$colour = $item['flag_colour'];
	$flag_label = $ticket_flag_names[$colour];
    if(!empty($item['flag_label'])) {
        $flag_label = $item['flag_label'];
    }
	$flag_colours = explode(',',get_config($dbc,'ticket_colour_flags'));
	$doc_table = "ticket_document";
	$doc_folder = "../Ticket/download/";
	$actions = (in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">' : '').
		(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">' : '').
		(in_array('alert',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" class="inline-img alert-icon no-toggle" title="Activate Alerts &amp; Get Notified">' : '').
		(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon no-toggle" title="Send Email">' : '').
		(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '').
		(in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon no-toggle" title="Attach File">' : '').
		(in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon no-toggle" title="Add Note">' : '').
		(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" class="inline-img archive-icon no-toggle" title="Archive">' : '');
	$status_icon = get_ticket_status_icon($dbc, $item['status']);
	if(!empty($status_icon)) {
		if($status_icon == 'initials') {
			$icon_img = '<span class="id-circle-large pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($item['status']).'</span>';
		} else {
			$icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 30px; margin-bottom: 5px;">';
		}
	} else {
		$icon_img = '';
	}
	$label = $icon_img.'<a target="_parent" href="../Ticket/index.php?edit='.$item['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).
		'" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$item['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;">'.get_ticket_label($dbc, $item).'</a>';
	$date = $item['to_do_date'];
	$date_name = 'to_do_date';
	if($item['status'] == 'Internal QA') {
		$date = $item['internal_qa_date'];
		$date_name = 'internal_qa_date';
	} else if($item['status'] == 'Customer QA') {
		$date = $item['deliverable_date'];
		$date_name = 'deliverable_date';
	}

	if($_GET['tab'] != 'path' && $_GET['tab'] != 'scrum_board') {
		$div_width = 'col-sm-6';
	} else {
		$div_width = '';
	}

	/* $contents = '<span class="pull-right small">';
	if(!in_array('Staff',$ticket_field_config) && count($ticket_field_config) > 0) {
		foreach(array_unique(explode(',',$item['contactid'].','.$item['internal_qa_contactid'].','.$item['deliverable_contactid'])) as $assignid) {
			if($assignid > 0) {
				$contents .= profile_id($dbc, $assignid, false);
			}
		}
	}*/

	$contents .= '</span><div class="clearfix"></div></h3>';
	if(in_array('Business',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">'.BUSINESS_CAT.' :</label>
			<div class="col-sm-8">'.get_client($dbc, $item['businessid']).'</div>
		</div>';
	}
	if(in_array('Contact',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Contact:</label>
			<div class="col-sm-8">';
				foreach(array_filter(explode(',',$item['clientid'])) as $clientid) {
					$contents .= get_contact($dbc, $clientid).'<br />';
				}
		$contents .= '</div>
		</div>';
	}
	if(in_array('Services',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Services:</label>
			<div class="col-sm-8">';
				foreach(array_filter(explode(',',$item['serviceid'])) as $service) {
					$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
					$contents .= ($service['category'] == '' ? '' : $service['category'].': ').$service['heading'].'<br />';
				}
		$contents .= '</div>
		</div>';
	}
	if(in_array('Heading',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">'.TICKET_NOUN.' Heading:</label>
			<div class="col-sm-8">'.$item['heading'].'</div>
		</div>';
	}
	if(in_array('Status',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Status:</label>
			<div class="col-sm-8">';
				if($ticket_security['edit'] > 0) {
					$contents .= '<select name="status" data-table="tickets" data-id-field="ticketid" data-id="'.$item['ticketid'].'" class="chosen-select-deselect">
						<option></option>';
						foreach($ticket_status_list as $status) {
							$contents .= '<option '.($item['status'] == $status ? 'selected' : '').' value="'.$status.'">'.$status.'</option>';
						}
					$contents .= '</select>';
				} else {
					$contents .= $item['status'];
				}
			$contents .= '</div>
		</div>';
	}
	if(in_array('Staff',$field_config) || count($field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8 '.(!($security['edit'] > 0) ? 'readonly-block' : '').'">
			<select name="contactid[]" multiple data-concat="," data-table="tickets" data-id="'.$item['ticketid'].'" data-id-field="ticketid" class="chosen-select-deselect" data-placeholder="Select Staff">
				';
				foreach($staff_list as $staff) {
					$contents .= '<option '.(in_array($staff['contactid'],explode(',',$item['contactid'])) ? 'selected' : '').' value="'.$staff['contactid'].'">'.$staff['first_name'].' '.$staff['last_name'].'</option>';
				}
			$contents .= '</select></div>
		</div>';
	}
	if(in_array('Members',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Members:</label>
			<div class="col-sm-8">';
				$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='members' AND `ticketid`='{$item['ticketid']}' AND `deleted`=0");
				while($member = mysqli_fetch_assoc($member_list)['item_id']) {
					$contents .= '<a target="_parent" href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
				}
			$contents .= '</div>
		</div>';
	}
	if(in_array('Clients',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Clients:</label>
			<div class="col-sm-8">';
				$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='clients' AND `ticketid`='{$item['ticketid']}' AND `deleted`=0");
				while($member = mysqli_fetch_assoc($member_list)['item_id']) {
					$contents .= '<a target="_parent" href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
				}
			$contents .= '</div>
		</div>';
	}
	if(in_array('Create Date',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Date Created:</label>
			<div class="col-sm-8">'.$item['heading'].'</div>
		</div>';
	}
	if(in_array('Ticket Date',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">'.TICKET_NOUN.' Date:</label>
			<div class="col-sm-8">';
				$dates = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE IFNULL(`to_do_date`,'0000-00-00')!='0000-00-00' AND `ticketid`='".$item['ticketid']."'");
				if($dates->num_rows > 0) {
					while($date_row = $dates->fetch_assoc()) {
						switch($date_row['type']) {
							case 'origin': $contents .= 'Shipment Date: '; break;
							case 'destination': $contents .= 'Delivery Date: '; break;
							case '': break;
							default: $contents .= $date_row['type'].': '; break;
						}
						$contents .= $date_row['to_do_date']."<br />\n";
					}
				} else {
					$contents .= $item['to_do_date'];
				}
			$contents .= '</div>
		</div>';
	}
	if(in_array('Deliverable Date',$ticket_field_config) || count($ticket_field_config) == 0) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">To Do Date:</label>
			<div class="col-sm-8">';
				$contents .= ($item['to_do_date'] == '' ? '' : $item['to_do_date'].'<br />');
				foreach(array_filter(explode(',', $item['contactid'])) as $staff) {
					$contents .= get_contact($dbc, $staff).'<br />';
				}
				$contents .= '('.$item['max_time'].')';
			$contents .= '</div>
		</div>';
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Internal QA Date:</label>
			<div class="col-sm-8">';
				$contents .= ($item['internal_qa_date'] == '' ? '' : $item['internal_qa_date'].'<br />');
				foreach(array_filter(explode(',', $item['internal_qa_contactid'])) as $staff) {
					$contents .= get_contact($dbc, $staff).'<br />';
				}
				$contents .= '('.$item['max_qa_time'].')';
			$contents .= '</div>
		</div>';
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Deliverable Date:</label>
			<div class="col-sm-8">';
				$contents .= ($item['deliverable_date'] == '' ? '' : $item['deliverable_date'].'<br />');
				foreach(array_filter(explode(',', $item['deliverable_contactid'])) as $staff) {
					$contents .= get_contact($dbc, $staff).'<br />';
				}
			$contents .= '</div>
		</div>';
	}
	if(in_array('Documents',$ticket_field_config)) {
		$contents .= '<label class="col-sm-4">Documents:</label>
			<div class="col-sm-8">';
				$documents = mysqli_query($dbc, "SELECT CONCAT('".TICKET_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`type`,'Link'),''),': '),''),IFNULL(NULLIF(`label`,''),`document`)) `label`, CONCAT('download/',`document`) `link` FROM `ticket_document` WHERE `ticketid`='".$item['ticketid']."' AND `deleted`=0 AND IFNULL(`document`,'') != '' UNION
					SELECT CONCAT('".PROJECT_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`category`,''),'undefined'),': '),''),IFNULL(NULLIF(`label`,''),`upload`)) `label`, CONCAT('../Project/download/',`upload`) `link` FROM `project_document` WHERE `projectid`='".$item['projectid']."' AND `deleted`=0 AND IFNULL(`upload`,'') != ''");
				while($document = $documents->fetch_assoc()) {
					$contents .= '<a target="_parent" href="'.$document['link'].'">'.$document['label']."</a><br />\n";
				}
			$contents .= '</div>';
	}
	if(in_array('Invoiced',$ticket_field_config)) {
		$contents .= '<div class="col-sm-6">
			<label class="col-sm-4">Invoiced:</label>
			<div class="col-sm-8">'.($item['invoiced'] > 0 ? 'Yes' : 'No').'</div>
		</div>';
	}
	$contents .= '<div class="clearfix"></div>';
} else if($type == 'Task') {
	$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='".$item[1]."'"));
    $team = explode(',',$item['contactid']);
	$item_external = $item['external'];
	if($item['status'] == $status_complete) {
		$li_class = 'strikethrough';
	}
	$data = 'data-id="'.$item['tasklistid'].'" data-table="tasklist" data-name="project_milestone" data-id-field="tasklistid"';
	$colour = $item['flag_colour'];
	if($colour == 'FFFFFF' || $colour == '') {
		$colour = 'F2F2F2';
	}
	$flag_colours = explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `flag_colours` FROM task_dashboard"))['flag_colours']);
	$flag_label = $ticket_flag_names[$colour];
    if(!empty($item['flag_label'])) {
        $flag_label = $item['flag_label'];
    }
	$flag_text = $item['flag_label'];
	$doc_table = "project_milestone_document";
	$doc_folder = "../Tasks_Updated/download/";
    ?>

    <div style="position: relative; display:none" id="flag_color_box_<?php echo $item['tasklistid']?>">
    	<div class="form-group flag_color_box">
    		<label class="col-sm-5 control-label" style="text-align: left;">Flag Colour:</label>
    		<div class="col-sm-7">
                <input id="demo_<?php echo $item['tasklistid']?>" type="text" class="form-control demo_cpicker" value="<?php echo $item['flag_colour']?>" />
            </div>
            <div class="col-sm-12">
            	<input style="margin-top:20px" type="button" value="Done" class="btn brand-btn pull-right" onclick="flag_item('<?php echo $item['tasklistid']?>');">
            	<input style="margin-top:20px" type="button" class="btn brand-btn pull-right" value="Close" onclick="$('#flag_color_box_<?php echo $item['tasklistid']?>').hide()">
            </div>
		</div>
	</div>

	<input type="color" style="display:none" id="colorpickerbtn_<?php echo $item['tasklistid']?>" value="<?php echo '#'.$item['flag_colour']?>">

	<script>
	var theInput = document.getElementById("colorpickerbtn_<?php echo $item['tasklistid']?>");
	theInput.addEventListener("input", function() {
		flag_item('<?php echo $item['tasklistid']?>',theInput.value);
	}, false);
	</script>

    <?php
    $task_statuses = explode(',',get_config($dbc, 'task_status'));
    $status_complete = $task_statuses[count($task_statuses) - 1];
    if ( $item['status']==$status_complete ) {
        $style_strikethrough = 'text-decoration:line-through; filter: gray; -webkit-filter: grayscale(1); filter: grayscale(1);';
    } else {
        $style_strikethrough = '';
    }

	$actions = '<span class="pull-right action-icons double-gap-bottom gap-top" style="width: 100%; '.$style_strikethrough.'" data-task="'.$item['tasklistid'].'"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img" title="Edit" onclick="overlayIFrameSlider(\'../Tasks_Updated/add_task.php?type='.$item['status'].'&tasklistid='.$item['tasklistid'].'\');">'.
		(in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon" title="Flag This!">' : '').

    	(in_array('flag',$quick_actions) ? '<span title="Highlight" onclick="highlight_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/color-wheel.png" class="inline-img" title="Highlight"></span>' : '').
		(!in_array('sync',$quick_actions) || substr($_GET['tab'],0,18) == 'path_external_path' ? '' : '<img src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" data-assigned="'.$item['assign_client'].'" class="inline-img assign-icon" title="Assign to External Path">').
		(in_array('alert',$quick_actions) ? '<span title="Send Alert" onclick="send_task_alert(this); return false;"><img src="../img/icons/ROOK-alert-icon.png" title="Send Alert" class="inline-img no-toggle" onclick="return false;"></span>' : '').
		(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email">' : '').
		(in_array('reminder',$quick_actions) ? '<span title="Schedule Reminder" onclick="send_task_reminder(this); return false;"><img title="Schedule Reminder" src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '').
		(in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File">' : '').
		(in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Reply">' : '').
		(in_array('time',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" class="inline-img time-icon" title="Add Time">' : '').
		(in_array('timer',$quick_actions) ? '<span title="Track Time" onclick="track_time(this); return false;"><img src="../img/icons/ROOK-timer2-icon.png" title="Track Time" class="inline-img no-toggle" onclick="return false;"></span>' : '').
		(in_array('scrum_sync',$quick_actions) ? '<span onclick="sync(this); return false;" data-sync="'.($item['is_sync'] > 0 ? 0 : 1).'"><img src="../img/icons/ROOK-sync-icon.png" title="'.($item['is_sync'] > 0 ? 'Synced To Customer' : 'Not Synced To Customer').' Scrum Board" class="inline-img no-toggle" onclick="return false;"></span>' : '').
		(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" class="inline-img archive-icon" title="Archive">' : '').
		'</span>';

    $actions .= '<input type="color" onchange="choose_color(this); return false;" class="color_picker" id="color_'.$item['tasklistid'].'"" name="color_'.$item['tasklistid'].'" style="display:none;" value="#f6b73c" />';

	$label = '<input type="checkbox" name="status" onchange="mark_done(this);" '.($item['status'] == 'Complete' || $item['status'] == 'Done' || $item['status'] == 'Finish' ? 'checked' : '').' value="'.$item['tasklistid'].'" class="form-checkbox no-margin small pull-left" '.(!($security['edit'] > 0) ? 'readonly disabled' : '').'>

		<div class="pull-left" style="max-width: calc(100% - 4em);margin:0 0.5em;">';

        $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

        if($slider_layout == 'accordion') {
            $label .= '<a style='.$style_strikethrough.' href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?type='.$item['status'].'&projectid='.$item['projectid'].'&tasklistid='.$item['tasklistid'].'\', \'50%\', false, false, $(\'.iframe_overlay\').closest(\'.container\').outerHeight() + 20); return false;">Task #'.$item['tasklistid'].'</a>';
        } else {
            $label .= '<a style='.$style_strikethrough.' href="../Tasks_Updated/add_task_full_view.php?type='.$item['status'].'&projectid='.$item['projectid'].'&tasklistid='.$item['tasklistid'].'">Task #'.$item['tasklistid'].'</a>';
        }

       $label .= '<br><span style="'.$style_strikethrough.'">'.html_entity_decode($item['heading']).'</span></div>

		<input type="hidden" name="comment" value="" data-name="comment" data-table="taskcomments" data-id-field="taskcommid" data-id="" data-type="'.$item['tasklistid'].'" data-type-field="tasklistid">
        <img src="../img/icons/ROOK-sync-icon.png" class="inline-img no-toggle pull-right sync_visible_icon" title="Synced to Customer Support Scrum Board" style="'.($item['is_sync'] > 0 ? '' : 'display:none;').'">';

	$contents = '<div class="action_notifications">';
	$item_comments = mysqli_query($dbc, "SELECT * FROM `task_comments` WHERE `tasklistid`='".$item['tasklistid']."' AND `comment` != '' ORDER BY `taskcommid` DESC");
	$odd_even = 0;
    while($item_comment = mysqli_fetch_assoc($item_comments)) {
		$bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
        $comment = explode(':',$item_comment['comment']);
		if($comment[0] == 'document' && $comment[1] > 0 && count($comment) == 2) {
			$document = $dbc->query("SELECT * FROM `task_document` WHERE `taskdocid`='".$comment[1]."'")->fetch_assoc();
			$contents .= '<div style="'.$style_strikethrough.'" class="'.$bg_class.'"><small>'.profile_id($dbc, $item_comment['created_by'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right"><a target="_parent" style="'.$style_strikethrough.'" href="../Tasks_Updated/download/'.$document['document'].'">'.$document['document'].'</a>';
			//$contents .= '<em class="block-top-5" style="'.$style_strikethrough.'">Added by '.get_contact($dbc, $document['created_by']).' at '.$document['created_date'].'</em>';
            $contents .= '</span></small><span class="clearfix"></span></div>';
		} else {
			$contents .= '<div style="'.$style_strikethrough.'" class="'.$bg_class.'"><small>'.profile_id($dbc, $item_comment['created_by'], false).'<span style="display:inline-block; width:calc(100% - 3em); '.$style_strikethrough.'" class="pull-right">'.preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($item_comment['comment']));
			//$contents .= '<em class="block-top-5" style="'.$style_strikethrough.'">Added by '.get_contact($dbc, $item_comment['created_by']).' at '.$item_comment['created_date'].'</em>';
            $contents .= '</span></small><span class="clearfix"></span></div>';
		}
        $odd_even++;
	}
	$contents .= '</div>';
} else if($type == 'Intake') {
	$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='".$item[1]."'"));
	$intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$item['intakeformid']."'"));
	$data = 'data-id="'.$item['intakeid'].'" data-table="intake" data-name="project_milestone" data-id-field="intakeid"';
	$colour = $item['flag_colour'];
	if($colour == 'FFFFFF' || $colour == '') {
		$colour = 'FFFFFF';
	}
	$flag_colours = explode(',',get_config($dbc,'ticket_colour_flags'));
	$flag_label = $ticket_flag_names[$colour];
    if(!empty($item['flag_label'])) {
        $flag_label = $item['flag_label'];
    }
	$flag_text = $item['flag_label'];
	$doc_table = "project_milestone_document";
	$doc_folder = "../Intake/download/";
	$actions = '<a target="_parent" href="'.WEBSITE_URL.'/Intake/add_form.php?intakeid='.$item['intakeid'].'&projectid='.$_GET['edit'].'"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit"></a>'.
		(in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">' : '').
		(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">' : '').
		(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email">' : '').
		(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '').
		(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" class="inline-img archive-icon no-toggle" title="Archive">' : '');
	$label = '<div style="display:inline-block; width:calc(100% - 2em);" class="double-pad-bottom">Intake #'.$item['intakeid'].': '.html_entity_decode($intake_form['form_name']).'</div>
		<input type="hidden" name="comment" value="" data-name="comment" data-table="intake_comments" data-id-field="intakecommid" data-id="" data-type="'.$item['intakeid'].'" data-type-field="intakeid">';

	if($_GET['tab'] != 'path') {
		$div_width = 'col-sm-6';
	} else {
		$div_width = '';
	}
	$contents = '';
	if(!empty($item['contactid'])) {
		$contents .= '<div class="'.$div_width.' form-group">
			<label class="col-sm-4">Contact:</label>
			<div class="col-sm-8">'.(!empty(get_client($dbc, $item['contactid'])) ? get_client($dbc, $item['contactid']) : get_contact($dbc, $item['contactid'])).'</div>
		</div>';
	}

	$contents .= '<div class="'.$div_width.' form-group">
		<label class="col-sm-4">PDF:</label>
		<div class="col-sm-8"><a target="_parent" href="'.WEBSITE_URL.'/Intake/'.$item['intake_file'].'" target="_blank">View PDF <img class="inline-img" src="../img/pdf.png"></a></div>
	</div>';

	$contents .= '<div class="'.$div_width.' form-group">
		<label class="col-sm-4">Last Updated Date:</label>
		<div class="col-sm-8">'.$item['received_date'].'</div>
	</div>';
} else if($type == 'Checklist') {
	$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '".$item[1]."'"));
    $team = explode(',',$item['assign_staff']);
	$data = 'data-id="'.$item['checklistid'].'" data-table="checklist" data-name="project_milestone" data-id-field="checklistid"';
	$colour = $item['flag_colour'];
	$flag_label = $ticket_flag_names[$colour];
    if(!empty($item['flag_label'])) {
        $flag_label = $item['flag_label'];
    }
	$flag_colours = explode(',',get_config($dbc,'ticket_colour_flags'));
	$actions = '<a target="_parent" href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/edit_checklist.php?edit='.$item['checklistid'].'\'); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit"></a>'.
		'<input type="file" name="attach_checklist_board_'.$item['checklistid'].'" style="display:none;" />'.
		(in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">': '').
		(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">': '').
		(in_array('alert',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" class="inline-img alert-icon no-toggle" title="Activate Alerts &amp; Get Notified">' : '').
		(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon no-toggle" title="Send Email">' : '').
		(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '').
		(in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle" data-checklist="'.$item['checklistid'].'" onclick="checklist_attach_file(this); return false;" title="Attach File">' : '').
		(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" class="inline-img archive-icon no-toggle" title="Archive">' : '');
	$label = '<a target="_blank" href="../Checklist/checklist.php?view='.$item['checklistid'].'" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/checklist.php?view='.$item['checklistid'].'&iframe_slider=1\'); return false;">'.$item['checklist_name'].'</a>';
    $contents = 'INCLUDE_CHECKLIST#*#'.$item['checklistid'];
} ?>
<li class="dashboard-item <?= $li_class ?>" <?= $data ?> data-colour="<?= $colour ?>" style="<?= $colour != '' ? 'background-color: #'.$colour.';' : '' ?><?= $border_colour ?>"><span class="flag-label"><?= $flag_label ?></span>
	<h4>
        <span class="pull-right">
            <?php foreach(array_unique($team) as $team_id) {
                if($team_id > 0) {
                    echo '<div class="" style="margin-top: -0.5em; font-size: 0.8em; display:inline; '.$style_strikethrough.'">'.profile_id($dbc, $team_id, false).'</div>';
                }
            } ?>
            <?= ((($_GET['tab'] == 'path' && $_GET['pathid'] != 'MS') || $_GET['tab'] == 'path_external_path' || $_GET['tab'] == 'scrum_board') ? '<img class="milestone-handle cursor-hand no-toggle" src="../img/icons/drag_handle.png" style="height:1em; '.$style_strikethrough.'" title="Drag">' : '') ?>
        </span>
        <div class="scale-to-fill no-overflow-y" style=""><?= $label ?></div>
    <div class="clearfix"></div></h4>
	<?php if($security['edit'] > 0) { ?>
		<div class="action-icons pad-bottom"><?= $actions ?></div>
	<?php } ?>

    <?php
    if($type == 'Task') {
        include('../Tasks_Updated/dashboard_fields.php');
    }
    ?>
    <br>

	<?php if(explode('#*#', $contents)[0] == 'INCLUDE_CHECKLIST') {
		$checklistid = explode('#*#', $contents)[1];
		$_GET['view']  = $checklistid;
		$_GET['override_block'] = 'true';
		$_GET['hide_header'] = 'true';
		$_GET['different_function_name'] = 'true';
        echo '<div class="checklist_screen" data-querystring="view='.$checklistid.'&override_block=true&hide_header=true&different_function_name=true">';
		include('../Checklist/view_checklist.php');
		echo '</div>';
	} else {
		echo $contents;
	} ?>
	<?php if(in_array('flag_manual',$quick_actions)) { ?>
		<span class="col-sm-3 text-center flag_field_labels" style="display:none;">Label</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Colour</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Start Date</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">End Date</span>
		<div class="col-sm-3"><input type='text' name='label' value='<?= $flag_text ?>' class="form-control" style="display:none;"></div>
		<div class="col-sm-3"><select name='colour' class="form-control" style="display:none;background-color:#<?= $item['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
				<option value="FFFFFF" style="background-color:#FFFFFF;">No Flag</option>
				<?php foreach($flag_colours as $flag_colour) { ?>
					<option <?= $item['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"></option>
				<?php } ?>
			</select></div>
		<div class="col-sm-3"><input type='text' name='flag_start' value='<?= $item['flag_start'] ?>' class="form-control datepicker" style="display:none;"></div>
		<div class="col-sm-3"><input type='text' name='flag_end' value='<?= $item['flag_end'] ?>' class="form-control datepicker" style="display:none;"></div>
		<button class="btn brand-btn pull-right" name="flag_it" onclick="return false;" style="display:none;">Flag This</button>
		<button class="btn brand-btn pull-right" name="flag_cancel" onclick="return false;" style="display:none;">Cancel</button>
		<button class="btn brand-btn pull-right" name="flag_off" onclick="return false;" style="display:none;">Remove Flag</button>
	<?php } ?>

	<input type='text' name='reply' value='' data-table='<?= $type == 'Task' ? 'task_comments' : '' ?>' data-name='<?= $type == 'Task' ? 'comment' : 'comment' ?>' class="form-control" style="display:none;">
	<input type='text' name='work_time' value='' data-table='<?= $type == 'Task' ? 'tasklist_time' : 'tasklist' ?>' class="form-control timepicker time-field" style="border:0;height:0;margin:0;padding:0;width:0;">
	<input type='text' name='reminder' value='' class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;">
	<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect"><option value="unassign">Unassigned</option>
		<?php foreach($external_path as $external_milestone) { ?>
			<option <?= $external_milestone == $item_external ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
		<?php } ?></select></div>
	<div class="select_users" style="display:none;">
		<select data-placeholder="Select Staff" multiple class="chosen-select-deselect">
		<?php foreach($staff_list as $staff) { ?>
			<option value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
		<?php } ?>
		</select>
		<button class="submit_button btn brand-btn pull-right">Submit</button>
		<button class="cancel_button btn brand-btn pull-right">Cancel</button>
	</div>
	<input type='file' name='document' value='' data-table="<?= $doc_table ?>" data-folder="<?= $doc_folder ?>" style="display:none;">
	<div class="timer_block_<?= $item['tasklistid'] ?>" style="display:none; margin-top:2.2em;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Timer:</label>
			<div class="col-sm-8">
				<input type="text" name="timer_<?= $item['tasklistid'] ?>" id="timer_value" style="float:left;" class="form-control timer" placeholder="0 sec" />&nbsp;&nbsp;
				<a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
				<a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $item['tasklistid'] ?>">Stop</a>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</li>
<script>
function sync(task) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Tasks_Updated/task_ajax_all.php?fill=is_sync&sync="+$(task).data('sync')+"&tasklistid="+$(task).closest('[data-task]').data('task'),
		dataType: "html",   //expect html to be returned
		success: function(response){
            // $(task).hide();
            $(task).data('sync',$(task).data('sync') > 0 ? 0 : 1);
            $(task).closest('.dashboard-item').find('.sync_visible_icon').toggle()
            $(task).find('img').prop('title',($(task).data('sync') > 0 ? 'Not Synced To Customer Scrum Board' : 'Synced To Customer Scrum Board'));
            try {
                $(task).find('img').tooltip('destroy');
            } catch (err) { }
            initTooltips();
			// location.reload();
		}
	});
}
</script>
