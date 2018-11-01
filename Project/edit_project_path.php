<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$projecttype = filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING);
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
}
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];
?>
<script>

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
        url: "../Tasks_Updated/task_ajax_all.php?fill=mark_done&taskid="+task_id+'&status='+status,
        dataType: "html",
        success: function(response){
            window.location.reload();
        }
    });
}

function resizeProjectPath() {
	$('.double-scroller div').width($('.dashboard-container').get(0).scrollWidth);
	$('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
	$('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
	if($(window).width() > 767 && $('ul.dashboard-list').length > 0 && ($('.default_screen,.standard-dashboard-body').outerHeight() + $('.default_screen,.standard-dashboard-body').offset().top) - ($($('ul.dashboard-list').first()).offset().top + ($('.dashboard-container').outerHeight() - $('.dashboard-container').prop('clientHeight'))) - 2 > 250) {
		$('ul.dashboard-list').outerHeight(($('.default_screen,.standard-dashboard-body').outerHeight() + $('.default_screen,.standard-dashboard-body').offset().top) - ($($('ul.dashboard-list').first()).offset().top + ($('.dashboard-container').outerHeight() - $('.dashboard-container').prop('clientHeight'))) - 2);
	} else {
		var height = 0;
		$('ul.dashboard-list').each(function() {
			height = $(this).height() > height ? $(this).height() : height;
		});
		$('ul.dashboard-list').outerHeight(height);
	}
}
function doubleScroll() {
	$('.dashboard-container').scrollLeft(this.scrollLeft).scroll();
}
function setDoubleScroll() {
	$('.double-scroller').scrollLeft(this.scrollLeft);
	// if(this.scrollLeft < 25) {
		// $('.left_jump').hide();
	// } else {
		// $('.left_jump').show();
	// }
	// if(this.scrollLeft > this.scrollWidth - this.clientWidth - 25) {
		// $('.right_jump').hide();
	// } else {
		// $('.right_jump').show();
	// }
}
var keep_scrolling = '';
function setActions() {
	$('input,select,textarea').filter('[data-table]').off('change',saveField).change(saveField);

	$('.reply-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		var id = $(item).data('id');
        var table = $(item).data('table');
        var tile = '';
        if ( table=='tasklist' ) {
            tile = 'tasks';
        } else if ( table=='tickets' ) {
            tile = 'tickets';
        }
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile='+tile+'&id='+id,'auto',false,true);
        /* Function before reply slider
        var item = $(this).closest('.dashboard-item');
		item.find('[name=reply]').off('change').off('blur').show().focus().blur(function() {
			$(this).off('blur');
			$.ajax({
				url: 'projects_ajax.php?action=project_actions',
				method: 'POST',
				data: {
					field: $(this).data('name'),
					value: this.value,
					table: item.data('table'),
					id: item.data('id'),
					id_field: item.data('id-field')
				},
				success: function(response) {
					//item.find('h4').append(response);
                    item.find('.action_notifications').prepend('<hr style="border-color:#ddd; margin:10px 0;" />'+response);
				}
			});
			$(this).hide().val('');
		}).keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			} else if(e.which == 27) {
				$(this).off('blur').hide();
			}
		}); */
	});
	$('.archive-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: '../Project/projects_ajax.php?action=project_fields',
			method: 'POST',
			data: {
				field: 'deleted',
				value: 1,
				table: item.data('table'),
				id: item.data('id'),
				id_field: item.data('id-field')
			}
		});
		item.hide();
	});
	$('.flag-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: '../Project/projects_ajax.php?action=project_actions',
			method: 'POST',
			data: {
				field: 'flag_colour',
				value: item.data('colour'),
				table: item.data('table'),
				id: item.data('id'),
				id_field: item.data('id-field')
			},
			success: function(response) {
				item.data('colour',response.substr(0,6));
				item.css('background-color','#'+response.substr(0,6));
				item.find('.flag-label').html(response.substr(6));
			}
		});
	});
	$('.manual-flag-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$('.flag_target').removeClass('flag_target');
		$(item).addClass('flag_target');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile='+item.data('table')+'&id='+item.data('id'), 'auto', false, true);
	});
	$('.assign-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('.assign_milestone').show().find('select').off('change').change(function() {
			item.find('.assign_milestone').hide();
			$.ajax({
				url: '../Project/projects_ajax.php?action=project_actions',
				method: 'POST',
				data: {
					field: 'external',
					value: this.value,
					table: item.data('table'),
					id: item.data('id'),
					id_field: item.data('id-field')
				},
				success: function(response) {
					//item.find('h4').append(response);
                    item.find('.action_notifications').prepend('<hr style="border-color:#ddd; margin:10px 0;" />'+response);
				}
			});
		});
	});
	$('.time-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('.time-field').timepicker('option','onClose',function() {
			if(this.value != '') {
				$.ajax({
					url: '../Project/projects_ajax.php?action=project_actions',
					method: 'POST',
					data: {
						id: item.data('id'),
                        field: this.name,
						value: this.value,
						table: $(this).data('table'),
						ref: item.data('table'),
						ref_id: item.data('id'),
						ref_id_field: item.data('id-field'),
                        projectid: '<?= $_GET['edit'] ?>'
					},
					success: function(response) {
						//item.find('h4').append(response);
                        item.find('.action_notifications').prepend('<hr style="border-color:#ddd; margin:10px 0;" />'+response);
					}
				});
				$(this).hide().val('');
			}
		}).focus();
	});
	$('.timer-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		taskid = item.data('id');
		item.find('.timer_block_'+taskid).toggle();
	});
	$('.attach-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table',$(this).data('table'));
			fileData.append('folder',$(this).data('folder'));
			fileData.append('id',item.data('id'));
			fileData.append('id_field',item.data('id-field'));
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "../Project/projects_ajax.php?action=project_actions",
				data: fileData,
				success: function(response) {
					var target = item.find('h4,p').last().after(response);
				}
			});
		}).click();
	});
	$('.reminder-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile='+item.data('table')+'&id='+item.data('id'), 'auto', false, true);
	});
	$('.alert-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		var select = item.find('.select_users');
		$(this).data('users').split(',').forEach(function(user) {
			if(user > 0) {
				select.find('option[value='+user+']').attr('selected',true);
			}
		});
		select.find('.cancel_button').off('click').click(function() {
			select.find('option:selected').removeAttr('selected');
			select.find('select').trigger('change.select2');
			select.hide();
			return false;
		});
		select.find('.submit_button').off('click').click(function() {
			if(select.find('select').val() != '' && confirm('Are you sure you want to activate alerts for the selected user(s)?')) {
				var users = [];
				select.find('select option:selected').each(function() {
					users.push(this.value);
					$(this).removeAttr('selected');
				});
				$.ajax({
					method: 'POST',
					url: '../Project/projects_ajax.php?action=project_actions',
					data: {
						id: item.data('id'),
						id_field: item.data('id-field'),
						table: item.data('table'),
						field: 'alert',
						value: users
					},
					success: function(result) {
						select.hide();
						//item.find('h4').append(result);
                        item.find('.action_notifications').prepend('<hr style="border-color:#ddd; margin:10px 0;" />'+response);
					}
				});
			}
			return false;
		});
		select.find('select').trigger('change.select2');
		select.show();
	});
	$('.email-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		var id = $(item).data('id');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=projects&from_task=task&id='+id,'auto',false,true);
	});
	$('.new_task').off('keyup').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('.milestone_name').off('click').click(function() {
		$(this).closest('h4').hide().nextAll('input[name=milestone_name]').show().focus().keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			}
		}).blur(function() {
			$(this).hide().prevAll('h4').show().find('a,span').first().text(this.value);
			$.post('../Project/projects_ajax.php?action=milestone_edit', { id: $(this).data('id'), field: 'label', value: this.value });
		});
	});
	$('.milestone_add').off('click').click(function() {
		var list = $(this).closest('.dashboard-list');
		var clone = list.clone();
		clone.find('.dashboard-item').not('.add_block').remove();
		clone.find('.info-block-header h4 a').text('New Milestone');
		clone.find('.info-block-header input[name=milestone_name]').val('');
		clone.find('.info-block-header [name=sort]').val('');
		$.post('../Project/projects_ajax.php?action=milestone_edit', { id: 0, field: 'sort', value: list.find('.info-block-header [name=sort]').val(), pathid: '<?= $pathid ?>', projectid: '<?= $projectid ?>' }, function(response) {
			clone.find('.info-block-header input[name=milestone_name]').data('id',response);
			clone.find('[data-milestone]').data('milestone','new milestone.'+response);
			var i = 0;
			$('.info-block-header [name=sort]').each(function() {
				$(this).val(i++).change();
			});
		});
		list.after(clone);
		setActions();
		$(window).resize();
	});
	$('.milestone_rem').off('click').click(function() {
		$(this).closest('.dashboard-list').remove();
		$(window).resize();
		$.post('../Project/projects_ajax.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), field: 'deleted', value: 1 });
	});
	$('.info-block-header [name=sort').off('change').change(function() {
		$.post('../Project/projects_ajax.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), field: 'sort', value: this.value });
	});

    $('.dashboard-list.item_list').each(function() {
        var count = $(this).find('li.dashboard-item').length;
        var add_block = $(this).find('li.dashboard-item.add_block');
        if ( count > 1 ) {
            $(add_block).hide();
        }
    });
    $('.milestone_options').off('click').click(function() {
        $(this).closest('.dashboard-list').find('.dashboard-list .add_block').toggle();
    });

    /* Timer */
    $('.start-timer-btn').on('click', function() {
        $(this).closest('div').find('.timer').timer({
            editable: true
        });
        $(this).addClass('hidden');
        $(this).next('.stop-timer-btn').removeClass('hidden');
    });
    $('.stop-timer-btn').on('click', function() {
        var item = $(this).closest('.dashboard-item');
        $(this).closest('div').find('.timer').timer('stop');
        $(this).addClass('hidden');
        $(this).prev('.start-timer-btn').removeClass('hidden');
        var taskid = $(this).data('id');
        var timer_value = $(this).closest('div').find('#timer_value').val();
        var contactid = '<?= $_SESSION['contactid'] ?>';
        $(this).closest('div').find('.timer').timer('remove');
        $('.timer_block_'+taskid).toggle();
        if ( timer_value != '' ) {
            $.ajax({
                type: "GET",
                url: "../Tasks_Updated/task_ajax_all.php?fill=stop_timer&taskid="+taskid+"&timer_value="+timer_value+"&contactid="+contactid,
                dataType: "html",
                success: function(response) {
                    $.ajax({
                        method: 'POST',
                        url: '../Tasks_Updated/task_ajax_all.php?fill=taskreply',
                        data: { taskid: taskid, reply: 'Tracked time: '+timer_value },
                        success: function(result) {
                        }
                    });
                    $.ajax({
                        url: '../Project/projects_ajax.php?action=project_actions',
                        method: 'POST',
                        data: {
                            field: 'track_time',
                            value: timer_value
                        },
                        success: function(response) {
                            item.find('.action_notifications').prepend('<hr style="border-color:#ddd; margin:10px 0;" />'+response);
                        }
                    });
                }
            });
        }
    });

    $('[name="status"]').change(function() {
        var item = $(this).closest('.dashboard-item');
        item.find('h4 div, .action_notifications p span').toggleClass('strikethrough');
    });

	initDragging();
}
function checklistChange(input) {
	var checklistnameid = $(input).val();
    if($(input).is(':checked')){
        var checked = 1;
    } else {
        var checked = 0;
    }
    $.ajax({
        type: "GET",
        url: "../Checklist/checklist_ajax.php?fill=checklist&checklistid="+checklistnameid+"&checked="+checked,
        dataType: "html",
        success: function(response){
        }
    });
}

function send_task_alert(task) {
       task_id = $(task).parents('span').data('task');
       if(task_id.toString().substring(0,5) == 'BOARD') {
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_alert.php?tile=tasks&id='+task_id, 'auto', false, false);
}

function send_task_reminder(task) {
       task_id = $(task).parents('span').data('task');
       if(task_id.toString().substring(0,5) == 'BOARD') {
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=tasks&id='+task_id, 'auto', false, false);
}

function track_time(task) {
    var task_id = $(task).parents('span').data('task');
   if(task_id.toString().substring(0,5) == 'BOARD') {
           task_id = task_id.substring(5);
   }
   overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_timer.php?tile=tasks&id='+task_id, 'auto', false, false);
}

//Add Intake
function addIntakeForm(btn) {
	$('.dialog_addintake').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 600 ? $(window).width() : 600),
		modal: true,
		buttons: {
			'Add': function() {
				var formid = $('[name="add_intakeform"]').val();
				var salesid = '<?= $_GET['id'] ?>';
				var sales_milestone = $(btn).data('milestone');
				window.location.href = '<?= WEBSITE_URL ?>/Intake/add_form.php?formid='+formid+'&salesid='+salesid+'&sales_milestone='+sales_milestone;
				$(this).dialog('close');
			},
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
	    }
	});
}
</script>
<?php $quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];
$ticket_security = get_security($dbc, 'ticket');
$ticket_field_config = array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets_dashboard` FROM field_config"))['tickets_dashboard']));

if(isset($_POST['clear']) || isset($_POST['clear_x']) || isset($_POST['clear_y'])) {
	$editid = $_GET['edit'];
    $date_of_archival = date('Y-m-d');
	$select_query = "SELECT count(*) as clear_count FROM tasklist WHERE projectid=`$editid` AND status='Done' AND deleted = 0";
	$count_query = mysqli_fetch_assoc(mysqli_query($dbc, $select_query));
	$query = "UPDATE tasklist SET deleted=1, `date_of_archival` = '$date_of_archival' WHERE projectid=`$editid` AND status='Done' AND deleted = 0";
	$update_clear_completed = mysqli_query($dbc, $query);
	echo '<script>alert("Archived '. $count_query['clear_count'] .' Tasks, which were completed."); window.location.reload();</script>';
}

if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
if($project['project_path'] == '') {
	$project['project_path'] = '0';
}
$project['project_path'] = trim($project['project_path'],',');
if($project['external_path'] == '') {
	$project['external_path'] = '0';
}
$project['external_path'] = trim($project['external_path'],',');
$pathid = explode('|',filter_var($_GET['pathid'],FILTER_SANITIZE_STRING));
$path_name = '';
$path_i = 0;
$project_paths = explode(',',$project['project_path']);
$path_names = explode('#*#',$project['project_path_name']);
$external_paths = explode(',',$project['external_path']);
$external_names = explode('#*#',$project['external_path_name']);
$path_type = $pathid[0];
if($path_type == 'I' && !in_array($pathid[1],$project_paths)) {
	$pathid[1] = array_values(array_filter($project_paths))[0];
} else if($path_type == 'E' && !in_array($pathid[1],$external_paths)) {
	$pathid[1] = array_values(array_filter($external_paths))[0];
}
if($pathid[1] > 0 && $pathid[0] == 'I') {
	$pathid = $pathid[1];
	foreach($project_paths as $i => $project_path_id) {
		if($pathid == $project_path_id) {
			$path_name = $path_names[$i];
			$path_i = $i;
		}
	}
} else if($pathid[1] > 0 && $pathid[0] == 'E') {
	$pathid = $pathid[1];
	foreach($external_paths as $i => $project_path_id) {
		if($pathid == $project_path_id) {
			$path_name = $external_names[$i];
			$path_i = $i;
		}
	}
	$_GET['tab'] = 'path_external_path';
} else if($pathid[0] == 'AllSB') {
	$pathid = 'AllSB';
	include('edit_project_path_scrum.php');
} else if($pathid[0] == 'MS') {
	$pathid = 'MS';
} else if($pathid[0] == 'SB' || count(array_filter(explode(',',$project['project_path'].','.$project['external']))) == '' || ($_GET['tab'] == 'path' && $pathid[0] == '' && in_array('Scrum Board',$tab_config))) {
	$_GET['tab'] = 'scrum_board';
	include('edit_project_path_scrum.php');
} else if($_GET['tab'] == 'path' || empty($_GET['tab'])) {
	$pathid = explode(',',$project['project_path'])[0];
	$_GET['tab'] = 'path';
}
if($_GET['tab'] != 'scrum_board' && !in_array($pathid,['AllSB','SB'])) {
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0"));
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$summary_tickets = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tickets, SUM(IF(`tickets`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_ticket_time, SUM(`ticket_timer`) spent_ticket_time FROM `tickets` LEFT JOIN (SELECT `ticketid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `ticket_timer` FROM `ticket_timer` GROUP BY `ticketid`) timers ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `projectid`='$projectid' AND `tickets`.`deleted`=0 AND `ticket_timer`.`deleted`=0"));
	$summary_workorders = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) workorders, SUM(IF(`workorder`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_workorder_time, SUM(`workorder_timer`) spent_workorder_time FROM `workorder` LEFT JOIN (SELECT `workorderid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `workorder_timer` FROM `workorder_timer` GROUP BY `workorderid`) timers ON `workorder`.`workorderid`=`timers`.`workorderid` WHERE `projectid`='$projectid'"));
	$summary_tasks = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tasks, SUM(IF(`tasklist`.`status`='".$status_complete."',1,0)) complete, SUM(TIME_TO_SEC(`work_time`)) task_time FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0")); ?>
	<script>
	$(document).ready(function() {
        //$('.dashboard-item:first-of-type [name=task],.dashboard-item:first-of-type .btn.brand-btn').closest('.dashboard-item.add_block').prepend('<div class="empty-list text-center">Nothing to do.</div>');
		setActions();
		resizeProjectPath();
		$(window).resize(function() {
			resizeProjectPath();
		}).resize();
		$('select.path_select_onchange').change(function() {
			window.location.replace('?edit=<?= $_GET['edit'] ?>&tab=path&pathid='+this.value);
		});
		// $('.left_jump').off('click').click(function() {
			// $('.dashboard-container').scrollLeft($('div.dashboard-list').filter(function() { return $(this).position().left < 0 }).last().get(0).offsetLeft - 10);
		// });
		// $('.right_jump').off('click').click(function() {
			// $('.dashboard-container').scrollLeft($('div.dashboard-list').filter(function() { return $(this).position().left > 15 }).first().get(0).offsetLeft - 10);
		// });
	});
	function initDragging() {
		// Dragging Milestones
		$('.has-dashboard').sortable({
			sort: function(event) {
				var end_distance = window.innerWidth - event.clientX;
				var start_distance = event.clientX - $('.dashboard-container').offset().left;
				clearInterval(keep_scrolling);
				if(end_distance < 20) {
					keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() + 10); }, 10);
				} else if(start_distance < 20) {
					keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() - 10); }, 10);
				}
			},
			handle: '.milestone_drag',
			items: '.item_list',
			update: function(event, element) {
				var i = 0;
				$('.info-block-header [name=sort]').each(function() {
					$(this).val(i++).change();
				});
			}
		});

		// Dragging to other Milestones
		$('.dashboard-list').sortable({
			connectWith: '.dashboard-list',
			sort: function(event) {
				var end_distance = window.innerWidth - event.clientX;
				var start_distance = event.clientX - $('.dashboard-container').offset().left;
				clearInterval(keep_scrolling);
				if(end_distance < 20) {
					keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() + 10); }, 10);
				} else if(start_distance < 20) {
					keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() - 10); }, 10);
				}
			},
			handle: '.milestone-handle',
			items: '.dashboard-item',
			update: function(event, element) {
				$.ajax({
					url: '../Project/projects_ajax.php?action=project_fields',
					method: 'POST',
					data: {
						field: element.item.data('name'),
						value: element.item.closest('.dashboard-list').data('milestone'),
						table: element.item.data('table'),
						id: element.item.data('id'),
						id_field: element.item.data('id-field')
					},
					success: function(response) {
						$('.empty-list').remove();
						$('.dashboard-item:first-of-type [name=task],.dashboard-item:first-of-type .btn.brand-btn').closest('.dashboard-item.add_block').prepend('<div class="empty-list text-center">Nothing to do.</div>');
					}
				});
			}
		});
	}
	function addTask(textbox) {
		var text = textbox.value;
		if(text != '') {
			var milestone = $(textbox).closest('ul').data('milestone');
			$(textbox).val('');
			$.ajax({
				url: '../Project/projects_ajax.php?action=project_fields',
				method: 'POST',
				data: {
					project: '<?= $projectid ?>',
					id: '',
					id_field: 'tasklistid',
					table: 'tasklist',
					field: 'heading',
                    task_path: '<?= $_GET["pathid"] ?>',
					value: text
				},
				success: function(response) {
					$.ajax({
						url: '../Project/projects_ajax.php?action=project_fields',
						method: 'POST',
						data: {
							project: '<?= $projectid ?>',
							id: response,
							id_field: 'tasklistid',
							table: 'tasklist',
							field: 'project_milestone',
							value: milestone
						}
					});
					$.ajax({
						url: '../Project/scrum_card_load.php?tab=path&taskid='+response,
						method: 'GET',
						success: function(response) {
							$(textbox).closest('ul').find('.empty-list').remove();
							$(textbox).closest('.dashboard-item').before(response);
							$('.dashboard-list').sortable('destroy');
							destroyInputs($('.new-items'));
							initInputs('.new-items');
							setActions();
						}
					});
				}
			});
		}
	}
	function apply_template() {
		var template = $('[name=path_templates]');
		if(!confirm("Are you sure you want to apply a new template to this project?")) {
			$(template).val($(template).data('template')).trigger('change.select2');
			return false;
		}
		$.ajax({
			url: '../Project/projects_ajax.php?action=apply_template',
			method: 'POST',
			data: {
				project: '<?= $projectid ?>',
				template: $(template).val()
			},
			success: function() {
				window.location.reload();
			}
		});
	}
	function addIntakeForm(btn) {
		$('.dialog_addintake').dialog({
			resizable: true,
			height: "auto",
			width: ($(window).width() <= 600 ? $(window).width() : 600),
			modal: true,
			buttons: {
				'Add': function() {
					var formid = $('[name="add_intakeform"]').val();
					var projectid = '<?= $_GET['edit'] ?>';
					var project_milestone = $(btn).data('milestone');
					window.location.href = '<?= WEBSITE_URL ?>/Intake/add_form.php?formid='+formid+'&projectid='+projectid+'&project_milestone='+project_milestone;
					$(this).dialog('close');
					$('.main_full_screen').css('float', 'left');
					loadingOverlayShow('.main_full_screen', $('.main_full_screen').height() + 20);
				},
		        Cancel: function() {
		        	$(this).dialog('close');
		        }
		    }
		});
	}
	function checklist_attach_file(checklist) {
		checklistid = $(checklist).data('checklist');
		var type = 'checklist_board';
		var file_id = 'attach_checklist_board_'+checklistid;
		$('[name='+file_id+']').change(function() {
			var fileData = new FormData();
			fileData.append('file',$('[name='+file_id+']')[0].files[0]);
			$.ajax({
				contentType: false,
				processData: false,
				type: "POST",
				url: "../Checklist/checklist_ajax.php?fill=checklist_upload&type="+type+"&id="+checklistid,
				data: fileData,
				complete: function(result) {
					//console.log(result.responseText);
					reloadChecklistScreen($(checklist).closest('li').find('.checklist_screen'));
				}
			});
		});
		$('[name='+file_id+']').click();
	}
	</script>
	<?php $external_path = [];
	if(substr($_GET['tab'],0,18) != 'path_external_path') {
		$external_path = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_path_milestone` WHERE `project_path_milestone`='".$project['external_path']."'"))['milestone']);
	}
	$all_milestones = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`milestone` SEPARATOR '#*#') `milestone`, GROUP_CONCAT(`timeline` SEPARATOR '#*#') `timeline` FROM `project_path_milestone`"));
	$label = '';
	if(substr($_GET['tab'],0,18) == 'path_external_path') {
		$label = 'External ';
	}
	$label .= PROJECT_NOUN.' Path: ';
	$path_name_edit = '';
	if($pathid > 0 && !empty($path_name)) {
		$path_name_edit = $path_name;
		$label .= '<span>'.$path_name_edit.'</span>';
	} else if($pathid > 0) {
		$project_path = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_path_milestone` WHERE `project_path_milestone`='".$pathid."'"));
		$path_name_edit = get_project_path_milestone($dbc, $pathid, 'project_path');
		$label .= '<span>'.$path_name_edit.'</span>';
	} else {
		$project_path = $all_milestones;
	}
	$timelines = explode('#*#',$project_path['timeline']);
	$milestones = explode('#*#',$project_path['milestone']);
	$html_milestones = explode('#*#',htmlentities($project_path['milestone']));
	$assigned_milestones = array_merge(explode('#*#',$all_milestones['milestone']),explode('#*#',htmlentities($all_milestones['milestone']))); ?>
	<div class="dialog_addintake" title="Select an Intake Form" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Intake Form:</label>
			<div class="col-sm-8">
				<select name="add_intakeform" class="chosen-select-deselect form-control">
					<option></option>
					<?php $form_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($form_types as $form_type) {
						echo '<option value="'.$form_type['intakeformid'].'">'.$form_type['form_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
	</div>
	<div class="standard-body-title">
		<form action="" method="post"><h3 class="pad-horizontal"><span class="pull-left"><?= $label.(!empty($path_name_edit) ? '<img class="inline-img cursor-hand small no-toggle" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).hide();$(this).next(\'span\').show().find(\'input\').focus();" title="Edit"><span class="col-sm-4 pull-right" style="display:none;"><input onblur="savePathName('.($path_type == 'E' ? "'external_path_name'" : "'project_path_name'").', this.value, '.$path_i.', '.$projectid.'); $(this).parent().hide().prev().show().prev().text(this.value);" type="text" value="'.$path_name_edit.'" class="form-control"></span>' : '') ?></span>
		<?php if($security['edit'] > 0 && $_GET['pathid'] != 'MS') { ?>
			<?php if(in_array($_GET['tab'],['path','path_external_path'])) { ?>
				<img class="inline-img pull-right no-toggle black-color small" src="../img/icons/ROOK-add-icon.png" title="Add / Remove Path" onclick="overlayIFrameSlider('../Project/edit_project_path_select.php?projectid=<?= $projectid ?>','75%',true)">
				<div class="col-sm-4 pull-right path_select smaller" style="display:none;"><select class="chosen-select-deselect path_select_onchange" data-placeholder="Select <?= PROJECT_NOUN ?> Path">
					<option></option>
					<?php if(in_array('Scrum Board',$tab_config)) { ?><option <?= $_GET['tab'] == 'scrum_board' ? 'selected' : '' ?> value="SB">Scrum Board</option><?php } ?>
					<?php foreach(explode(',',$project['project_path']) as $i => $project_path_id) {
                        if($project_path_id > 0) { ?>
                            <option <?= $project_path_id == $pathid && $_GET['tab'] == 'path' ? 'selected' : '' ?> value="I|<?= $project_path_id ?>"><?= empty(explode('#*#',$project['project_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['project_path_name'])[$i] ?></option>
                        <?php }
                    }
                    foreach(explode(',',$project['external_path']) as $i => $project_path_id) {
                        if($project_path_id > 0) { ?>
                            <option <?= $project_path_id == $pathid && $_GET['tab'] == 'path_external_path' ? 'selected' : '' ?> value="E|<?= $project_path_id ?>">External: <?= empty(explode('#*#',$project['project_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['project_path_name'])[$i] ?></option>
                        <?php }
                    } ?>
				</select></div>
				<img class="inline-img pull-right no-toggle black-color small" src="../img/project-path.png" title="Select the <?= PROJECT_NOUN ?> Path" onclick="$('.path_select').show(); $(this).hide();">
			<?php } ?>
			<input type="image" src="../img/clear-checklist.png" onclick="clearCompletedProjectTask(this);" name="clear1" title="Clear Completed Tasks" value="<?php echo $projectid; ?>" class="no-toggle inline-img black-color pull-right small" alt="Submit"/>
		<?php } ?>
        <div class="clearfix"></div>
		</h3></form>
	</div>
	<div class="clearfix"></div>
	<?php if($_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' || $_GET['tab'] == 'scrum_board') { ?>
		<div class="double-scroller"><div></div></div>
	<?php } ?>
	<div class="has-dashboard form-horizontal dashboard-container" style="<?= $pathid == 'MS' ? 'overflow-y:hidden;' : '' ?>">
		<?php /*if(in_array($_GET['tab'],['path','path_external_path']) && $pathid != 'MS') { ?>
			<img class="black-color clockwise inline-img stick-left text-lg left_jump" src="../img/icons/dropdown-arrow.png" style="display:none;">
			<img class="black-color counterclockwise inline-img stick-right text-lg right_jump" src="../img/icons/dropdown-arrow.png">
		<?php }*/ ?>
		<?php $ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
		if((substr($_GET['tab'],0,18) != 'path_external_path') && empty($_GET['category'])) {


		/*$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
				SELECT 'Task', `tasklistid` FROM tasklist WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(project_milestone, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(task_tododate,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
			$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
				SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))";
                */

$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
				SELECT 'Task', `tasklistid` FROM tasklist WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND archived_date IS NULL AND (task_tododate < CURRENT_DATE() OR IFNULL(task_tododate,'0000-00-00') = '0000-00-00') UNION
				SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))";

            /*
			$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
				SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))";
                */
				if(mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM ($unassigned_sql) count"))[0] > 0) {
				$unassigned_milestone_sql = "SELECT 0 `id`, 'Unassigned' `milestone`, 'Unassigned' `label`, 0 `sort` UNION ";
			}
		}
		$ticket_flag_names = [''=>''];
		$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
		foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
			$ticket_flag_names[$colour] = $flag_names[$i];
		}
		$task_flag_names = [''=>''];
		$flag_names = explode('#*#', get_config($dbc, 'task_colour_flag_names'));
		foreach(explode(',',get_config($dbc, 'task_colour_flags')) as $i => $colour) {
			$task_flag_names[$colour] = $flag_names[$i];
		}

		// What action will happen when adding to a milestone?
		$add_action = '';
		if((in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config)) && !in_array('Tickets',$tab_config) && !in_array('Work Orders',$tab_config)) {
			$add_action = "overlayIFrameSlider('../Tasks_Updated/add_task.php?projectid=".$projectid."&project_milestone=MILESTONE','75%',true);";
		} else if(in_array('Work Orders',$tab_config) && !in_array('Tickets',$tab_config) && !in_array('Checklists',$tab_config) && !in_array('Tasks',$tab_config)) {
			$add_action = "window.location.href='../Work Order/add_workorder.php?projectid=".$projectid."&milestone_timeline=".urlencode($milestone)."&from=".urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI'])."';";
		} else if(!in_array('Work Orders',$tab_config) && in_array('Tickets',$tab_config) && !in_array('Checklists',$tab_config) && !in_array('Tasks',$tab_config)) {
			$add_action = "window.location.href='../Ticket/index.php?edit=0&projectid=".$projectid."&milestone_timeline=".urlencode($milestone)."&from=".urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI'])."';";
		} else {
			$add_action = "$(this).closest('.item_list').find('.add_block').get(0).scrollIntoView();";
		}

		$milestone_list = $dbc->query($unassigned_milestone_sql."SELECT `id`, `milestone`, `label`, `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND '".(substr($_GET['tab'],0,18) != 'path_external_path' ? 'I' : 'E')."'=`path_type` AND '$pathid' IN (`pathid`,'MS') AND `deleted`=0 ORDER BY `sort`,`id`");
		while($milestone_row = $milestone_list->fetch_assoc()) {
			$milestone = $milestone_row['milestone'];
			$i = array_search($milestone, $milestones);

			$tab_id = (substr($_GET['tab'],0,18) == 'path_external_path' ? 'path_external_path_' : 'path_').config_safe_str($milestone);
			$tab_id = $tab_id == 'path_unassigned' ? 'unassigned' : $tab_id;
			if($_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' || $_GET['tab'] == $tab_id) {
				$timeline = $timelines[$i];
				if($milestone == 'Unassigned') {
                    $timeline = '';
					$sql = $unassigned_sql;
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM (SELECT COUNT(*) `tickets` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')) `tickets` LEFT JOIN
						(SELECT COUNT(*) `intake` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))) `intake` ON 1=1"));
				} else if(substr($_GET['tab'],0,18) == 'path_external_path') {
					$html_milestone = htmlentities($milestone);
					$sql = "SELECT 'Task', `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND `external` IN ('$milestone','$html_milestone')";
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `tasks`, 0 `tickets`, 0 `items`, 0 `workorders` FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND `external` IN ('$milestone','$html_milestone')"));
				} else {
					$html_milestone = htmlentities($milestone);
                    if (strpos($_SERVER['REQUEST_URI'],'/Tasks_Updated/') !== false) {

                        $sql = "SELECT 'Task', `tasklistid` FROM tasklist WHERE projectid='$projectid' AND `deleted`=0 AND archived_date IS NULL AND project_milestone IN ('$milestone','$html_milestone') ORDER BY tasklistid DESC";

                        $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `tasks` FROM tasklist WHERE projectid='$projectid' AND `deleted`=0 AND archived_date IS NULL AND project_milestone IN ('$milestone','$html_milestone')"));
                        //echo $count['tasks'];

                    } else {
                        $sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND milestone_timeline IN ('$milestone','$html_milestone') UNION
                            SELECT 'Work Order', `workorderid` FROM workorder WHERE projectid='$projectid' AND `status` != 'Archive' AND milestone IN ('$milestone','$html_milestone') UNION
                            SELECT 'Task', `tasklistid` FROM tasklist WHERE projectid='$projectid' AND `deleted`=0 AND archived_date IS NULL AND project_milestone IN ('$milestone','$html_milestone') UNION
                            SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `deleted`=0 AND project_milestone IN ('$milestone','$html_milestone') UNION
                            SELECT 'Checklist', `checklistid` FROM `checklist` WHERE `projectid` = '$projectid' AND `projectid` > 0 AND `deleted` = 0 AND `project_milestone` IN ('$milestone','$html_milestone')";
                        $count = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets`,`workorders`,`tasks`,`intake`,`checklists`,(IFNULL(`ticket_spent`,0) + IFNULL(`task_time`,0) + IFNULL(`checklist_time`,0)) `time`,(IFNULL(`ticket_est`,0) + IFNULL(`task_est`,0)) `eta` FROM (SELECT COUNT(DISTINCT `tickets`.`ticketid`) `tickets`, SUM(TIME_TO_SEC(`time_list`.`time`)) `ticket_spent`, SUM(TIME_TO_SEC(`time_list`.`est`)) `ticket_est` FROM `tickets` LEFT JOIN (SELECT 0 `est`, `timer` `time`, `ticketid` FROM `ticket_timer` WHERE `deleted`=0 AND `timer_type`='Work' UNION SELECT 0 `est`, `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `time_length` `est`, 0 `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('Completion Estimate','QA Estimate')) `time_list` ON `tickets`.`ticketid`=`time_list`.`ticketid` WHERE projectid='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND milestone_timeline IN ('$milestone','$html_milestone')) tickets LEFT JOIN
                            (SELECT COUNT(*) `workorders` FROM workorder WHERE projectid='$projectid' AND `status` != 'Archive' AND milestone IN ('$milestone','$html_milestone')) workorders ON 1=1 LEFT JOIN
                            (SELECT COUNT(DISTINCT `tasklist`.`tasklistid`) `tasks`, SUM(TIME_TO_SEC(`tasklist`.`estimated_time`)) `task_est`, SUM(`task_time`.`time`) `task_time` FROM `tasklist` LEFT JOIN (SELECT `tasklistid`, SUM(TIME_TO_SEC(`work_time`)) `time` FROM `tasklist_time` GROUP BY `tasklistid`) `task_time` ON `tasklist`.`tasklistid`=`task_time`.`tasklistid` WHERE `projectid`='$projectid' AND `deleted`=0 AND `archived_date` IS NULL AND `project_milestone` IN ('$milestone','$html_milestone')) `tasks` ON 1=1 LEFT JOIN 
                            (SELECT COUNT(*) `intake` FROM intake WHERE projectid='$projectid' AND `deleted`=0 AND project_milestone IN('$milestone','$html_milestone')) intake ON 1=1 LEFT JOIN
                            (SELECT COUNT(DISTINCT `checklist`.`checklistid`) `checklists`, SUM(TIME_TO_SEC(`checklist_name_time`.`work_time`)) `checklist_time` FROM `checklist` LEFT JOIN `checklist_name` ON `checklist`.`checklistid`=`checklist_name`.`checklistid` AND `checklist_name`.`deleted`=0 LEFT JOIN `checklist_name_time` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid` WHERE `checklist`.`projectid`='$projectid' AND `checklist`.`deleted` = 0 AND `checklist`.`projectid` > 0 AND `checklist`.`project_milestone` IN ('$milestone','$html_milestone')) checklist ON 1=1"));
                    }
				}
				$milestone_items = mysqli_query($dbc, $sql); ?>
				<div class="<?= ($_GET['tab'] == 'path' && $_GET['pathid'] != 'MS') || $_GET['tab'] == 'path_external_path' ? 'dashboard-list' : '' ?> item_list" style="margin-bottom: -10px;">
					<div class="info-block-header"><h4><div class="pull-left"><?= in_array($_GET['tab'],['path','path_external_path']) && $pathid != 'MS' ? '<a target="_parent" href="?edit='.$projectid.'&tab='.$tab_id.'&pathid='.$_GET['pathid'].'">'.$milestone_row['label'].'</a>' : '<span>'.$milestone_row['label'].'</span>' ?>
						<?= $milestone != 'Unassigned' && $security['edit'] > 0 && $pathid != 'MS' ? '<img class="small no-gap-top milestone_name cursor-hand inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit">' : '' ?></div>
						<?= $milestone != 'Unassigned' && in_array($_GET['tab'],['path','path_external_path']) && $security['edit'] > 0 && $pathid != 'MS' ? '<div class="pull-right"><img class="small no-gap-top milestone_drag cursor-hand inline-img pull-right no-toggle" src="../img/icons/drag_handle.png" title="Drag">
							<img class="small milestone_rem cursor-hand no-gap-top inline-img pull-right" src="../img/remove.png">
							<img class="small milestone_add cursor-hand no-gap-top inline-img pull-right" src="../img/icons/ROOK-add-icon.png">
							<input type="hidden" name="sort" value="'.$milestone_row['sort'].'"></div>' : '' ?></h4>
						<input type="text" name="milestone_name" data-milestone="<?= $milestone ?>" data-id="<?= $milestone_row['id'] ?>" value="<?= $milestone_row['label'] ?>" style="display:none;" class="form-control">
					<div class="clearfix"></div>
                    <div class="pull-left">
                        <span class="small"><a target="_parent" href="?edit=<?= $projectid ?>&tab=<?= $tab_id ?>&pathid=<?= $_GET['pathid'] ?>" <?= $pathid == 'MS' ? 'onclick="return false;"' : '' ?>>
                            <?= ($count['tickets'] > 0 ? substr(TICKET_NOUN,0,1).': '.$count['tickets'] : ' ').($count['tasks'] > 0 ? ' TASKS: '.$count['tasks'] : ' ').($count['workorders'] > 0 ? ' WO: '.$count['workorders'] : ' ').($count['items'] > 0 ? ' C: '.$count['items'] : ' ').($count['intake'] > 0 ? ' INTAKE: '.$count['intake'] : ' ').($count['checklist'] > 0 ? ' CHECKLIST: '.$count['checklist'] : ' ').(in_array('Path ETA',$value_config) && $count['eta'] > 0 ? ' ETA: '.time_decimal2time($count['eta'] / 3600) : '').(in_array('Path Time',$value_config) && $count['time'] > 0 ? ' TT: '.time_decimal2time($count['time'] / 3600) : '') ?>
                        </a></span>
                        <span class="pad-horizontal"><?= $timeline != '' ? $timeline : '&nbsp;' ?></span>
                    </div>
                    <div class="pull-right"><img class="small milestone_options cursor-hand no-gap-top inline-img pull-right no-toggle" src="../img/icons/ROOK-3dot-icon.png" title="Show/Hide Options"></div>
                    <div class="clearfix"></div>
                    </div>
                    <ul class="<?= ($_GET['tab'] == 'path' && $_GET['pathid'] != 'MS') || $_GET['tab'] == 'path_external_path' ? 'dashboard-list' : 'connectedChecklist no-margin full-width' ?>" data-milestone="<?= $milestone ?>">

						<?php if($milestone != 'Unassigned' && $security['edit'] > 0) { ?>
							<li class="dashboard-item add_block">
								<?php if($tab_id != 'path' && $_GET['tab'] != 'path_external_path') { ?>
									<?php if(in_array('Tasks',$tab_config) || in_array('Checklists',$tab_config)) { ?><input type="text" placeholder="Quick Add Task" name="task" onblur="addTask(this);" class="new_task form-control"><br /><?php } ?>
									<?php if(in_array('Intake',$tab_config)) { ?><a target="_parent" href="" onclick="addIntakeForm(this); return false;" data-milestone="<?= $milestone ?>" class="btn brand-btn pull-right">Intake +</a><?php } ?>
									<?php if(in_array('Tickets',$tab_config)) { ?><a target="_parent" href="../Ticket/index.php?&edit=0&projectid=<?= $projectid ?>&milestone_timeline=<?= urlencode($milestone) ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,false,'auto'); return false;" class="btn brand-btn pull-right"><?= TICKET_NOUN ?> +</a><?php } ?>

									<?php
                                    $add_milestone = str_replace("&","FFMEND",$milestone);
                                    $add_milestone = str_replace(" ","FFMSPACE",$add_milestone);
                                    $add_milestone = str_replace("#","FFMHASH",$add_milestone);

                                    if(in_array('Tasks',$tab_config) || in_array('Checklists',$tab_config)) { ?>

									<!-- <a href="" onclick="addIntakeForm(this); return false;" data-milestone="<?= $milestone_row['milestone'] ?>" class="btn brand-btn pull-right">Intake +</a> -->
                                    <?php
                                    $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

                                    if($slider_layout == 'accordion') {
                                    ?>
                                    <a href="../Tasks_Updated/add_task.php?tab=path&projectid=<?= $projectid ?>&project_milestone=<?= $add_milestone?>&task_path=<?=$path_name_edit?>&task_milestone_timeline=<?=$add_milestone?>" onclick="overlayIFrameSlider(this.href,'50%',true); return false;" class="btn brand-btn pull-right">Task +</a>
                                    <?php } else { ?>
                                    <a href="../Tasks_Updated/add_task_full_view.php?tab=path&projectid=<?= $projectid ?>&project_milestone=<?= $add_milestone?>&task_path=<?=$path_name_edit?>&task_milestone_timeline=<?=$add_milestone?>" class="btn brand-btn pull-right">Task +</a>
                                    <?php } ?>

                                    <?php } ?>

									<?php if(in_array('Checklists In Path',$tab_config)) { ?><a target="_parent" href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Checklist/edit_checklist.php?edit=NEW&projectid=<?= $projectid ?>&project_milestone=<?= urlencode($milestone) ?>'); return false;" class="btn brand-btn pull-right">Checklist +</a><?php } ?>
								<?php } ?>
								<div class="clearfix"></div>
							</li>
						<?php } ?>

						<?php while($item = mysqli_fetch_array($milestone_items)) {
							include('scrum_card_load.php');
						} ?>

						<?php if($_GET['tab'] != 'path' && $_GET['tab'] != 'path_external_path') {
							include('next_buttons.php');
						} ?>
					</ul>
				</div>
			<?php }
		} ?>
	</div>
	<div class="clearfix"></div>

	<div class="dialog_addintake" title="Select an Intake Form" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Intake Form:</label>
			<div class="col-sm-8">
				<select name="add_intakeform" class="chosen-select-deselect form-control">
					<option></option>
					<?php $form_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($form_types as $form_type) {
						echo '<option value="'.$form_type['intakeformid'].'">'.$form_type['form_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
	</div>
<?php } ?>
