<?php
/*
 * Tasks Milestones
 * Display tasks in individual milestones
 * Included on index.php
 */
include_once('../include.php');
checkAuthorised('tasks');
$contactide = $_SESSION['contactid'];
$taskboardid = preg_replace('/[^0-9]/', '', $_GET['category']);
$task_milestone = filter_var(trim($_GET['milestone']), FILTER_SANITIZE_STRING);
$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];
?>

<script type="text/javascript" src="tasks.js"></script>
<script>
setTimeout(function() {
    var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
        return $( this ).outerWidth( true );
    }).get() );

    var maxHeight = -1;

    $('.ui-sortable').each(function() {
        maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
    });

    $(function() {
        $(".connectedSortable").width(maxWidth).height(maxHeight);
    });

    $( '.connectedSortable' ).each(function () {
        this.style.setProperty( 'height', maxHeight, 'important' );
        this.style.setProperty( 'width', maxWidth, 'important' );
    });
}, 200);

function jump_to(i) {
	$('#scrum_tickets').scrollLeft(0);
	$('#scrum_tickets').scrollLeft($('#sortable'+i).position().left - 40);
}

$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
	$('.milestone_name').off('click').click(function() {
		$(this).closest('h4').hide().nextAll('input[name=milestone_name]').show().focus().keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			}
		}).blur(function() {
			$(this).hide().prevAll('h4').show().find('span').text(this.value);
			$.post('task_ajax_all.php?action=milestone_edit', { id: $(this).data('id'), table: $(this).data('table'), field: 'label', value: this.value });
		});
	});
});

function choose_user(target, type, id, date) {
	var title	= 'Select a Staff';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({type: type, id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
                if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskalert',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: id, user: $(this).closest('body').find('select').val(), reply: 'Alert added for ' },
                        complete: function(result) { console.log(result.responseText); }
                    });
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskemail',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: id, user: $(this).closest('body').find('select').val(), reply: 'Email sent to ' },
                        complete: function(result) { console.log(result.responseText); }
                    });
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskreminder',
						data: { id: id, type: type, schedule: date, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: id, user: $(this).closest('body').find('select').val(), reply: 'Reminder added for ' },
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

function sync_task(task) {
	var item = $(task).parents('li');
	item.find('.assign_milestone').show().find('select').off('change').change(function() {
		item.find('.assign_milestone').hide();
		$.ajax({
			url: 'task_ajax_all.php?fill=taskexternal',
			method: 'POST',
			data: {
				field: 'external',
				value: this.value,
				id: item.attr('id'),
			},
			success: function(response) {
				item.find('h4').after(response);
			}
		});
	});
}

function send_alert(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	choose_user('alert', type, task_id);
}

function send_email(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tasks&id='+task_id+'&type='+type, 'auto', false, true);
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
	$('[name=reminder_'+name_id+task_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', type, task_id, date);
		}
	});

}

function send_reply(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	$('[name=reply_'+task_id+']').show().focus();
	$('[name=reply_'+task_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+task_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply; //+ " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=taskreply',
				data: { taskid: task_id, reply: save_reply },
				complete: function(result) {
                    console.log(result.responseText);
                    //$('.updates_'+task_id).append(result);
                    window.location.reload();
                }
			})
		}
	});
}

function quick_add_time(task) {
	task_id = $(task).parents('span').data('task');
	$('[name=task_time_'+task_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=task_quick_time',
				data: { id: task_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); window.location.reload(); }
			});
            $.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=taskreply',
				data: { taskid: task_id, reply: 'Time added '+time+':00' },
				complete: function(result) { console.log(result.responseText); window.location.reload(); }
			});
		}
	});
	$('[name=task_time_'+task_id+']').timepicker('show');
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
			url: "task_ajax_all.php?fill=task_upload&type="+type+"&id="+task_id,
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
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	$.ajax({
		method: "POST",
		url: "task_ajax_all.php?fill=taskflag",
		data: { type: type, id: task_id },
		complete: function(result) {
			console.log(result.responseText);
			if(type == 'task') {
				$(task).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			} else {
				$(task).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			}
		}
	});
}

function archive(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	if(type == 'task' && confirm("Are you sure you want to archive this task?")) {
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=delete_task&taskid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				window.location.reload();
				console.log(response.responseText);
			}
		});
	}
	else if(confirm("Are you sure you want to archive this task board?")) {
		window.location = "<?= WEBSITE_URL; ?>/Tasks/add_task_board.php?deleteid=" + task_id;
	}
}

function mark_done(sel) {
    var task_id = sel.value;
    var status = '';
    if ( $(sel).is(':checked') ) {
        status = '<?= $status_complete ?>';
    } else {
        status = '<?= $status_incomplete ?>';
    }

    $.ajax({
        type: "GET",
        url: "task_ajax_all.php?fill=mark_done&taskid="+task_id+'&status='+status,
        dataType: "html",
        success: function(response){
            console.log(response);
            window.location.reload();
        }
    });
}

function clearCompleted(task) {
	task_board_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_board_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_board_id = task_board_id.substring(5);
	}

	if(type == 'task board') { //&& confirm("Are you sure you want to clear all the completed tasks on this board?")) {
        $.ajax({
            type: "GET",
            url: "task_ajax_all.php?fill=clear_completed&task_board_id="+task_board_id+"&status=<?= $status_complete ?>",
            dataType: "html",   //expect html to be returned
            success: function(response){
                window.location.reload();
                //window.parent.location.href = "<?= WEBSITE_URL; ?>/Tasks/index.php?category="+task_board_id+"tab=<?= trim($_GET['tab']) ?>";
            }
        });
        window.location.reload();
	}
}
</script>

<div class="container">
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>

	<div class="row hide_on_iframe">
        <div class="pull-left tab double-gap-top">
			<span class="popover-examples list-inline">
				<a data-toggle="tooltip" data-placement="top" title="Unassigned tasks appear in this task board."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a>
				<img class="" src="<?= WEBSITE_URL ?>/img/alert.png" border="0" alt="" />
			</span>
		</div>

        <input type='hidden' value='<?php echo $contactide; ?>' class='contacterid' /><?php
        if($_GET['category'] != 'All') {
			$query_check_credentials = "SELECT * FROM task_board_document WHERE taskboardid='".$_GET['category']."' ORDER BY taskboarddocid DESC";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				echo "<table class='table table-bordered' style='width:100%;'>
				<tr class='hidden-xs hidden-sm'>
				<th>Document</th>
				<th>Date</th>
				<th>Uploaded By</th>
				</tr>";
				while($row = mysqli_fetch_array($result)) {
					echo '<tr>';
					$by = $row['created_by'];
					echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
					echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
					echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
					//echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}

        if($_GET['category'] !== 'All'):
            $task_flag = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM `task_board` WHERE `taskboardid`='{$_GET['category']}'"))['flag_colour']; ?>
            <form name="form_sites" method="post" action="" class="form-inline" role="form" <?php echo ($task_flag == '' ? '' : 'style="background-color: #'.$task_flag.';"'); ?>>
                <!--
                <span class="pull-right double-gap-top" style="cursor: pointer;" data-task="BOARD<?php echo $_GET['category']; ?>">
                    <?php if(in_array('flag', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('alert', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Activate Alerts and Get Notified" onclick="send_alert(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('email', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('reminder', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('attach', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('archive', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Archive Task Board" onclick="archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2em;"></span><?php } ?>
                    <br /><input type="text" name="reminder_board_<?php echo $_GET['category']; ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
                </span>
                -->


                <span class="pull-right double-gap-top" style="cursor: pointer;" data-task="BOARD<?php echo $_GET['category']; ?>">
                    <span class="theme-color-icon" style="cursor:pointer; padding: 0.25em 0.5em 0.25em 0;" title="Task Board History" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/task_history.php?taskboardid=<?=$taskboardid?>');"><img src="<?php echo WEBSITE_URL; ?>/img/time-machine.png" style="height:2em;"></span>
                    <a href=""><img src="../img/clear-checklists.png" alt="Clear Completed Tasks" title="Clear Completed Tasks" style="height:2em;" onclick="clearCompleted(this);" /></a>
                    <?php if(in_array('archive', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Archive Task Board" onclick="archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2em;"></span><?php } ?>
                </span>
                <input type="file" name="attach_board_<?php echo $_GET['category']; ?>" style="display:none;" />

                <div class="clearfix"></div>

                <div id="scrum_tickets" class="scrum_tickets"><?php
                    if($_GET['category'] != 'All') {
                        $taskboardid = $_GET['category'];
                        $task_path = get_task_board($dbc, $taskboardid, 'task_path');
						$milestone = $dbc->query("SELECT `id`, `milestone`, `label`, `sort`, 'taskboard_path_custom_milestones' `table` FROM `taskboard_path_custom_milestones` WHERE `deleted`=0 AND `taskboard`='$taskboardid' AND `milestone`='$task_milestone'")->fetch_assoc();

						if(!($milestone['id'] > 0)) {
							$each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
							$timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));
							$milestone = ['id'=>'','milestone'=>$task_milestone,'label'=>$task_milestone];
						}

						$cat_tab = $milestone['milestone'];
						if ( $url_tab == 'Private' ) {
							$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE (task_path='$task_path' OR '$task_path' = '') AND (task_milestone_timeline='$cat_tab' OR ('$cat_tab' = '' AND task_milestone_timeline NOT IN ('".implode("','",$each_tab)."'))) AND task_board = '$taskboardid' AND (DATE(`archived_date`) >= (DATE(NOW() - INTERVAL 3 DAY)) OR archived_date IS NULL OR archived_date = '0000-00-00') AND `deleted`=0 ORDER BY task_path ASC, tasklistid DESC");
						} else {
							$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_board = '$taskboardid' AND task_milestone_timeline='$cat_tab' ORDER BY task_path ASC, tasklistid DESC");
						}

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

						$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(tasklistid) AS total_unread FROM tasklist WHERE task_path='$task_path' AND task_milestone_timeline='$cat_tab' AND task_board = '$taskboardid' AND (DATE(`archived_date`) >= (DATE(NOW() - INTERVAL 3 DAY)) OR archived_date IS NULL OR archived_date = '0000-00-00') AND (task_tododate IS NULL OR task_tododate = '0000-00-00' OR (task_tododate< DATE(NOW()) AND status != '".$status_complete."')) AND `deleted`=0"));
						$alert = '';
						if($get_config['total_unread'] > 0) {
							$alert = '&nbsp;<img src="'.WEBSITE_URL.'/img/alert.png" border="0" alt="">';
						}

						echo '<ul id="sortable" class="connectedSortable '.$status.' '.$class_on.'" style="padding-top:0; max-width:100%;">'; ?>

						<div class="info-block-header">
							<h4><span><?= $milestone['label'] ?></span>
								<img class="small no-gap-top milestone_name cursor-hand inline-img pull-left" src="../img/icons/ROOK-edit-icon.png">
								<a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img class="no-margin black-color inline-img pull-right" src="../img/icons/ROOK-add-icon.png" /></a></h4>
								<input type="text" name="milestone_name" data-milestone="<?= $milestone['milestone'] ?>" data-id="<?= $milestone['id'] ?>" data-table="taskboard_path_custom_milestones" value="<?= $milestone['label'] ?>" style="display:none;" class="form-control">
							<!--
							<a href=""> -->
								<div class="small">TASKS: <?= $get_config['total_unread'] ?></div>
								<div class="clearfix"></div>
							<!--
							</a>
							-->
							<div class="clearfix"></div>
						</div><?php

						echo '<li class="new_task_box no-sort"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to quickly add a task and then hit Enter."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>
							<input onChange="changeEndAme(this)" name="add_task" placeholder="Quick Add" id="add_new_task '.$status.' '.$task_path.' '.$taskboardid.'" type="text" class="form-control" style="max-width:96%;" /></li>';

						while($row = mysqli_fetch_array( $result )) {
							if ( $row['status']==$status_complete ) {
								$style_strikethrough = 'text-decoration:line-through;';
							} else {
								$style_strikethrough = '';
							}
							$border_colour = '';
							foreach(explode(',',$row['contactid'].','.$row['alerts_enabled']) as $userid) {
								if($userid > 0 && $border_colour == '') {
									$border_colour = get_contact($dbc, $userid, 'calendar_color');
								}
							}
							echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'" style="'.($row['flag_colour'] == '' ? '' : 'background-color: '.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'">';

							$past = 0;

							$date = new DateTime($row['task_tododate']);
							$now = new DateTime();

							if($date < $now && $row['status'] != $status_complete) {
								$past = 1;
							}

							echo '<span class="pull-right action-icons" style="width: 100%;" data-task="'.$row['tasklistid'].'">';
							$mobile_url_tab = trim($_GET['tab']);
							if ( $url_tab=='Project' || $mobile_url_tab=='Project' ) { ?>
								<span style="display:inline-block; text-align:center; width:11%"><a href="../Project/projects.php?edit=<?= $row['projectid'] ?>" title="View Project" style="background-color:#fff; border:1px solid #3ac4f2; border-radius:50%; color:#3ac4f2 !important; display:inline-block; height:1.5em; width:1.5em;">►</a></span><?php
							}
							if (in_array('edit', $quick_actions)) { ?>
								<span title="Edit Task" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?=WEBSITE_URL?>/img/icons/ROOK-edit-icon.png" class="inline-img" onclick="return false;"></span><?php
							}
							echo in_array('flag', $quick_actions) ? '<span title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo $row['projectid'] > 0 && in_array('sync', $quick_actions) ? '<span title="Sync to External Path" onclick="sync_task(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('alert', $quick_actions) ? '<span title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('email', $quick_actions) ? '<span title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('reminder', $quick_actions) ? '<span title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('attach', $quick_actions) ? '<span title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('reply', $quick_actions) ? '<span title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('time', $quick_actions) ? '<span title="Add Time" onclick="quick_add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo in_array('archive', $quick_actions) ? '<span title="Archive Task" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img" onclick="return false;"></span>' : '';
							echo '<img class="drag_handle pull-right inline-img" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" />';
							echo '</span>';
							echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
							echo '<input type="text" name="task_time_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
							echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
							echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
							echo '<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect" data-id="'.$row['tasklistid'].'"><option value="unassign">Unassigned</option>';
							foreach(array_unique(array_filter(explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`project_path_milestone`.`milestone` SEPARATOR '#*#') `milestones` FROM `project` LEFT JOIN `project_path_milestone` ON CONCAT(',',`project`.`external_path`,',') LIKE CONCAT('%,',`project_path_milestone`.`project_path_milestone`,',%') WHERE `projectid`='".$row['projectid']."'"))['milestones']))) as $external_milestone) { ?>
									<option <?= $external_milestone == $row['external'] ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
							<?php }
							echo '</select></div><div class="clearfix"></div>';
							//echo '<a href="add_tasklist.php?type='.$row['status'].'&tasklistid='.$row['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">';
							//echo limit_text($row['heading'], 5 ).'</a><img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /><span class="pull-right">'; ?>
							<div class="row">

								<h4 style="<?= $style_strikethrough ?>"><input type="checkbox" name="status" value="<?= $row['tasklistid'] ?>" class="form-checkbox no-margin" onchange="mark_done(this);" <?= ( $row['status'] == $status_complete ) ? 'checked' : '' ?> />
									<a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $row['tasklistid'] ?></a>: <?=limit_text($row['heading'], 5 )?>
							<?php
							echo '<span class="pull-right small">';
							profile_id($dbc, $row['contactid']);
							echo '</span></h4></span></div>';

							echo '<div class="clearfix"></div>';
							$comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='{$row['tasklistid']}' AND `deleted`=0 ORDER BY `taskcommid` DESC");
							if ( $comments->num_rows > 0 ) { ?>
								<div class="form-group clearfix">
									<div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
										while ( $row_comment=mysqli_fetch_assoc($comments) ) { ?>
											<div class="note_block row">
												<div class="col-xs-2"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
												<div class="col-xs-10" style="<?= $style_strikethrough ?>">
													<div><?= html_entity_decode($row_comment['comment']); ?></div>
													<div><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
												</div>
												<div class="clearfix"></div>
											</div>
											<hr class="margin-vertical" /><?php
										} ?>
									</div>
									<div class="clearfix"></div>
								</div><?php
							}

							echo '</li>';
						} ?>

						<li class="no-sort"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$taskboardid?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right">Add Task</a></li><?php

						echo '</ul>';
                    } ?>
                </div><!-- #scrum_tickets -->
            </form><?php
        endif; ?>
	</div><!-- .hide_on_iframe -->
</div><!-- .container -->
