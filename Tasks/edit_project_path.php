<?php include_once('../include.php');
$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];
$ticket_security = get_security($dbc, 'ticket');
$ticket_field_config = array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets_dashboard` FROM field_config"))['tickets_dashboard']));
$editid = $_GET['edit'];
$projectid = $_GET['edit'];
$project = $dbc->query("SELECT * FROM `project` WHERE `projectid`='$projectid'")->fetch_assoc();
if(isset($_POST['clear']) || isset($_POST['clear_x']) || isset($_POST['clear_y'])) {
	$editid = $_GET['edit'];
        $date_of_archival = date('Y-m-d');
	$select_query = "select count(*) as clear_count from tasklist where projectid=$editid and status='".$status_complete."'";
	$count_query = mysqli_fetch_assoc(mysqli_query($dbc, $select_query));
	$query = "update tasklist set deleted=1, `date_of_archival` = '$date_of_archival' where projectid=$editid and status='".$status_complete."'";
	$update_clear_completed = mysqli_query($dbc, $query);
	echo '<script>alert("Archived '. $count_query['clear_count'] .' Tasks, which were completed.")</script>';
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
} else if($pathid[0] == 'SB' || count(array_filter(explode(',',$project['project_path'].','.$project['external']))) == '' || ($_GET['tab'] == 'path' && $pathid[0] == '' && in_array('Scrum Board',$tab_config))) {
	$_GET['tab'] = 'scrum_board';
	include('edit_project_path_scrum.php');
} else if($_GET['tab'] == 'path' || empty($_GET['tab'])) {
	$pathid = explode(',',$project['project_path'])[0];
	$_GET['tab'] = 'path';
}
if($_GET['tab'] != 'scrum_board') {
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0"));
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$summary_tickets = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tickets, SUM(IF(`tickets`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_ticket_time, SUM(`ticket_timer`) spent_ticket_time FROM `tickets` LEFT JOIN (SELECT `ticketid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `ticket_timer` FROM `ticket_timer` WHERE `ticket_timer`.`deleted` = 0 GROUP BY `ticketid`) timers ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `projectid`='$projectid' AND `tickets`.`deleted`=0"));
	$summary_workorders = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) workorders, SUM(IF(`workorder`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_workorder_time, SUM(`workorder_timer`) spent_workorder_time FROM `workorder` LEFT JOIN (SELECT `workorderid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `workorder_timer` FROM `workorder_timer` GROUP BY `workorderid`) timers ON `workorder`.`workorderid`=`timers`.`workorderid` WHERE `projectid`='$projectid'"));
	$summary_tasks = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tasks, SUM(IF(`tasklist`.`status`='".$status_complete."',1,0)) complete, SUM(TIME_TO_SEC(`work_time`)) task_time FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0")); ?>
	<script>
	$(document).ready(function() {
		$('.dashboard-item:first-of-type [name=task],.dashboard-item:first-of-type .btn.brand-btn').closest('.dashboard-item.add_block').prepend('<div class="empty-list text-center">Nothing to do.</div>');
		setActions();
		resizeProjectPath();
		$(window).resize(function() {
			resizeProjectPath();
		}).resize();
		$('select.path_select_onchange').change(function() {
			window.location.replace('?edit=<?= $_GET['edit'] ?>&tab=path&pathid='+this.value);
		});
		$('.left_jump').off('click').click(function() {
			$('.dashboard-container').scrollLeft($('div.dashboard-list').filter(function() { return $(this).position().left < 0 }).last().get(0).offsetLeft - 10);
		});
		$('.right_jump').off('click').click(function() {
			$('.dashboard-container').scrollLeft($('div.dashboard-list').filter(function() { return $(this).position().left > 15 }).first().get(0).offsetLeft - 10);
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
                    url: "../Tasks/task_ajax_all.php?fill=stop_timer&taskid="+taskid+"&timer_value="+timer_value+"&contactid="+contactid,
                    dataType: "html",
                    success: function(response) {
                        $.ajax({
                            method: 'POST',
                            url: 'task_ajax_all.php?fill=taskreply',
                            data: { taskid: taskid, reply: 'Time added '+timer_value },
                            success: function(result) {}
                        });
                    }
                });
            }
        });
	});
	function resizeProjectPath() {
		$('.double-scroller div').width($('.dashboard-container').get(0).scrollWidth);
		$('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
		$('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
		if($(window).width() > 767 && $('ul.dashboard-list').length > 0 && $(window).innerHeight() - $($('ul.dashboard-list').first()).offset().top - 68 - ($('.dashboard-container').innerHeight() - $('.dashboard-container').prop('clientHeight')) > 250) {
			$('ul.dashboard-list').outerHeight($(window).innerHeight() - $($('ul.dashboard-list').first()).offset().top - 68 - ($('.dashboard-container').innerHeight() - $('.dashboard-container').prop('clientHeight')));
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
		if(this.scrollLeft < 25) {
			$('.left_jump').hide();
		} else {
			$('.left_jump').show();
		}
		if(this.scrollLeft > this.scrollWidth - this.clientWidth - 25) {
			$('.right_jump').hide();
		} else {
			$('.right_jump').show();
		}
	}
	var keep_scrolling = '';
	function setActions() {
		$('input,select,textarea').filter('[data-table]').off('change',saveField).change(saveField);
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

		$('.reply-icon').off('click').click(function() {
			var item = $(this).closest('.dashboard-item');
			item.find('[name=reply]').off('change').off('blur').show().focus().blur(function() {
				$(this).off('blur');
				$.ajax({
					url: '../Project/projects_ajax.php?action=project_actions',
					method: 'POST',
					data: {
						field: $(this).data('name'),
						value: this.value,
						table: item.data('table'),
						id: item.data('id'),
						id_field: item.data('id-field')
					},
					success: function(response) {
						item.find('h4').append(response);
					}
				});
				$(this).hide().val('');
			}).keyup(function(e) {
				if(e.which == 13) {
					$(this).blur();
				} else if(e.which == 27) {
					$(this).off('blur').hide();
				}
			});
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
						item.find('h4').append(response);
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
							field: this.name,
							value: this.value,
							table: $(this).data('table'),
							ref: item.data('table'),
							ref_id: item.data('id'),
							ref_id_field: item.data('id-field')
						},
						success: function(response) {
							item.find('h4').append(response);
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
					url: "projects_ajax.php?action=project_actions",
					data: fileData,
					success: function(response) {
						var target = item.find('h4,p').last().after(response);
					}
				});
			}).click();
		});
		$('.reminder-icon').off('click').click(function() {
			var item = $(this).closest('.dashboard-item');
			item.find('[name=reminder]').change(function() {
				var reminder = $(this).val();
				var select = item.find('.select_users');
				select.find('.cancel_button').off('click').click(function() {
					select.find('select option:selected').removeAttr('selected');
					select.find('select').trigger('change.select2');
					select.hide();
					return false;
				});
				select.find('.submit_button').off('click').click(function() {
					if(select.find('select').val() != '' && confirm('Are you sure you want to schedule reminders for the selected user(s)?')) {
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
								field: 'reminder',
								value: reminder,
								users: users,
								ref_id: item.data('id'),
								ref_id_field: item.data('id-field')
							},
							success: function(result) {
								select.hide();
								select.find('select').trigger('change.select2');
								item.find('h4').append(result);
							}
						});
					}
					return false;
				});
				select.show();
			}).focus();
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
							item.find('h4').append(result);
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
			overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=projects&id='+id,'auto',false,true);
		});
		$('.new_task').off('keyup').keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			}
		});
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
					$(textbox).closest('ul').find('.empty-list').remove();
					$(textbox).closest('.dashboard-item').before('<li class="dashboard-item new-items" data-id="'+response+'" data-table="tasklist" data-name="project_milestone" data-id-field="tasklistid" data-colour="F2F2F2" style="background-color: #F2F2F2">'+
							'<div class="action-icons">'+
								'<img src="../img/icons/ROOK-edit-icon.png" class="inline-img" title="Edit" onclick="overlayIFrameSlider(\'../Tasks/add_task.php?tasklistid='+response+'\');">'+
								<?php if(in_array('flag',$quick_actions)) { ?>'<img src="../img/icons/ROOK-flag-icon.png" class="inline-img flag-icon" title="Flag This!">'+<?php } ?>
								<?php if(in_array('sync',$quick_actions)) { ?>'<img src="../img/icons/ROOK-sync-icon.png" class="inline-img sync-icon" title="Assign to External Path">'+<?php } ?>
								<?php if(in_array('alert',$quick_actions)) { ?>'<img src="../img/icons/ROOK-alert-icon.png" class="inline-img alert-icon" title="Activate Alerts &amp; Get Notified">'+<?php } ?>
								<?php if(in_array('email',$quick_actions)) { ?>'<img src="../img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email">'+<?php } ?>
								<?php if(in_array('reminder',$quick_actions)) { ?>'<img src="../img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon" title="Schedule Reminder">'+<?php } ?>
								<?php if(in_array('attach',$quick_actions)) { ?>'<img src="../img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File">'+<?php } ?>
								<?php if(in_array('reply',$quick_actions)) { ?>'<img src="../img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Reply">'+<?php } ?>
								<?php if(in_array('time',$quick_actions)) { ?>'<img src="../img/icons/ROOK-timer-icon.png" class="inline-img time-icon" title="Add Time">'+<?php } ?>
                                <?php if(in_array('timer',$quick_actions)) { ?>'<img src="../img/icons/ROOK-timer2-icon.png" class="inline-img timer-icon" title="Track Time">'+<?php } ?>
								<?php if(in_array('archive',$quick_actions)) { ?>'<img src="../img/icons/ROOK-trash-icon.png" class="inline-img archive-icon" title="Archive">'+<?php } ?>
								'<img class="pull-right milestone-handle" src="../img/icons/drag_handle.png" style="height: 1em; margin-top: 0.5em;"></div>'+
							'<h4><input type="checkbox" name="status" data-table="tasklist" data-id="'+response+'" data-id-field="tasklistid" data-incomplete="<?= $status_incomplete ?>" value="<?= $status_complete ?>" class="form-checkbox no-margin small"> Task #'+response+': '+text+'</h4>'+
							'<input type="text" name="reply" value="" data-name="task" class="form-control" style="display:none;">'+
							'<div class="select_users" style="display:none;"><select class="chosen-select-deselect"><option></option><?php foreach($staff_list as $staff) {
								echo '<option value="'.$staff['contactid'].'">'.filter_var($staff['first_name'], FILTER_SANITIZE_STRING).' '.filter_var($staff['last_name'], FILTER_SANITIZE_STRING).'</option>';
							} ?></select></div>'+
							'<input type="hidden" name="comment" value="" data-name="comment" data-table="taskcomments" data-id-field="taskcommid" data-id="" data-type="'+response+'" data-type-field="tasklistid">'+
							'<input type="text" name="work_time" value="" data-table="tasklist_time" class="timepicker time-field" style="border:0;height:0;margin:0;padding:0;width:0;">'+
							'<input type="text" name="reminder" value="" class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;">'+
							'<input type="file" name="document" value="" data-table="project_milestone_document" data-folder="../Tasks/download/" style="display:none;">'+
							'<div class="clearfix"></div>'+
						'</li>');
					$('.dashboard-list').sortable('destroy');
					destroyInputs($('.new-items'));
					initInputs('.new-items');
					setActions();
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
	function saveProjectPathName(type, name, i, projectid) {
		$.post('../Project/projects_ajax.php?action=set_path_names', {type:type,name:name,key:i,project:projectid});
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
		<form action="" method="post"><h3 class="pad-horizontal action-icons"><span class="pull-left"><?= $label.(!empty($path_name_edit) ? '<img class="inline-img cursor-hand small" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).hide();$(this).next(\'span\').show().find(\'input\').focus();"><span class="col-sm-4 pull-right" style="display:none;"><input onblur="saveProjectPathName('.($path_type == 'E' ? "'external_path_name'" : "'project_path_name'").', this.value, '.$path_i.', '.$projectid.'); $(this).parent().hide().prev().show().prev().text(this.value);" type="text" value="'.$path_name_edit.'" class="form-control"></span>' : '') ?></span>
		<?php if(in_array($_GET['tab'],['path','path_external_path'])) { ?>
			<div class="col-sm-4 pull-right path_select smaller" style="display:none;"><select class="chosen-select-deselect path_select_onchange" data-placeholder="Select <?= PROJECT_NOUN ?> Path">
				<option></option>
				<?php if(in_array('Scrum Board',$tab_config)) { ?><option <?= $_GET['tab'] == 'scrum_board' ? 'selected' : '' ?> value="SB">Scrum Board</option><?php } ?>
				<?php $paths = mysqli_query($dbc, "SELECT `project_path`, `project_path_milestone` FROM `project_path_milestone` WHERE `project_path` != '' AND `project_path_milestone` IN (".$project['project_path'].") ORDER BY `project_path`");
				while($path = mysqli_fetch_array($paths)) { ?>
					<option <?= $path['project_path_milestone'] == $pathid && $_GET['tab'] == 'path' ? 'selected' : '' ?> value="I|<?= $path['project_path_milestone'] ?>"><?= $path['project_path'] ?></option>
				<?php }
				$external_paths = mysqli_query($dbc, "SELECT `project_path`, `project_path_milestone` FROM `project_path_milestone` WHERE `project_path` != '' AND `project_path_milestone` IN (".$project['external_path'].") ORDER BY `project_path`");
				while($path = mysqli_fetch_array($external_paths)) { ?>
					<option <?= $path['project_path_milestone'] == $pathid && $_GET['tab'] == 'path_external_path' ? 'selected' : '' ?> value="E|<?= $path['project_path_milestone'] ?>">External: <?= $path['project_path'] ?></option>
				<?php } ?>
			</select></div>
		<?php } ?>
		</h3></form>
	</div>
	<div class="clearfix"></div>
	<?php if($_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' || $_GET['tab'] == 'scrum_board') { ?>
		<div class="double-scroller"><div></div></div>
	<?php } ?>
	<div class="has-dashboard form-horizontal dashboard-container">
		<?php if(in_array($_GET['tab'],['path','path_external_path'])) { ?>
		<?php } ?>
		<?php $ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
		if(substr($_GET['tab'],0,18) != 'path_external_path') {
			$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
				SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))";
				if(mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM ($unassigned_sql) count"))[0] > 0) {
				//$unassigned_milestone_sql = "SELECT 0 `id`, 'Unassigned' `milestone`, 'Unassigned' `label`, 0 `sort` UNION ";
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
			$add_action = "overlayIFrameSlider('../Tasks/add_task.php?projectid=".$projectid."&project_milestone=MILESTONE','75%',true);";
		} else if(in_array('Work Orders',$tab_config) && !in_array('Tickets',$tab_config) && !in_array('Checklists',$tab_config) && !in_array('Tasks',$tab_config)) {
			$add_action = "window.location.href='../Work Order/add_workorder.php?projectid=".$projectid."&milestone_timeline=".urlencode($milestone)."&from=".urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI'])."';";
		} else if(!in_array('Work Orders',$tab_config) && in_array('Tickets',$tab_config) && !in_array('Checklists',$tab_config) && !in_array('Tasks',$tab_config)) {
			$add_action = "window.location.href='../Ticket/index.php?edit=0&projectid=".$projectid."&milestone_timeline=".urlencode($milestone)."&from=".urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI'])."';";
		} else {
			$add_action = "$(this).closest('.item_list').find('.add_block').get(0).scrollIntoView();";
		}

		$milestone_list = $dbc->query($unassigned_milestone_sql."SELECT `id`, `milestone`, `label`, `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND '$pathid'=`pathid` AND `deleted`=0 ORDER BY `sort`,`id`");
		while($milestone_row = $milestone_list->fetch_assoc()) {
			$milestone = $milestone_row['milestone'];
			$i = array_search($milestone, $milestones);

			$tab_id = (substr($_GET['tab'],0,18) == 'path_external_path' ? 'path_external_path_' : 'path_').config_safe_str($milestone);
			$tab_id = $tab_id == 'path_unassigned' ? 'unassigned' : $tab_id;
			if($_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' || $_GET['tab'] == $tab_id) {
				$timeline = $timelines[$i];
				if($milestone == 'Unassigned') {
					$sql = $unassigned_sql;
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM (SELECT COUNT(*) `tickets` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(milestone_timeline,'') NOT IN ('".implode("','",$assigned_milestones)."') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')) tickets LEFT JOIN
						(SELECT COUNT(*) `workorders` FROM workorder WHERE projectid='$projectid' AND `projectid` > 0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(milestone,'') NOT IN ('".implode("','",$assigned_milestones)."') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')) workorders ON 1=1 LEFT JOIN
						(SELECT COUNT(*) `tasks` FROM tasklist WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`status` = '' OR IFNULL(project_milestone,'') NOT IN ('".implode("','",$assigned_milestones)."') OR IFNULL(task_tododate,'0000-00-00') = '0000-00-00' OR IFNULL(created_by,'') = '')) tasks ON 1=1 LEFT JOIN
						(SELECT COUNT(*) `intake` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(`project_milestone`,'') NOT IN ('".implode("','",$assigned_milestones)."'))) intake ON 1=1"));
				} else if(substr($_GET['tab'],0,18) == 'path_external_path') {
					$html_milestone = htmlentities($milestone);
					$sql = "SELECT 'Task', `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND heading != '' AND `external` IN ('$milestone','$html_milestone')";
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `tasks`, 0 `tickets`, 0 `items`, 0 `workorders` FROM `tasklist` WHERE `projectid`='$projectid' AND heading != ''  AND `deleted`=0 AND `external` IN ('$milestone','$html_milestone')"));
				} else {
					$html_milestone = htmlentities($milestone);
					$sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND milestone_timeline IN ('$milestone','$html_milestone') UNION
						SELECT 'Work Order', `workorderid` FROM workorder WHERE projectid='$projectid' AND `status` != 'Archive' AND milestone IN ('$milestone','$html_milestone') UNION
						SELECT 'Task', `tasklistid` FROM tasklist WHERE projectid='$projectid' AND `deleted`=0 AND heading != '' AND `status` != '".$status_complete."' AND project_milestone IN ('$milestone','$html_milestone') UNION
						SELECT 'Intake', `intakeid` FROM intake WHERE projectid='$projectid' AND `deleted`=0 AND project_milestone IN ('$milestone','$html_milestone')";
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM (SELECT COUNT(*) `tickets` FROM tickets WHERE projectid='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND milestone_timeline IN ('$milestone','$html_milestone')) tickets LEFT JOIN
						(SELECT COUNT(*) `workorders` FROM workorder WHERE projectid='$projectid' AND `status` != 'Archive' AND milestone IN ('$milestone','$html_milestone')) workorders ON 1=1 LEFT JOIN
						(SELECT COUNT(*) `tasks` FROM tasklist WHERE projectid='$projectid' AND `deleted`=0 AND project_milestone IN ('$milestone','$html_milestone')) tasks ON 1=1 LEFT JOIN
						(SELECT COUNT(*) `intake` FROM intake WHERE projectid='$projectid' AND `deleted`=0 AND project_milestone IN('$milestone','$html_milestone')) intake ON 1=1"));
				}
				$milestone_items = mysqli_query($dbc, $sql); ?>
				<div class="<?= $_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' ? 'dashboard-list' : '' ?> item_list" style="margin-bottom: -10px;">
					<div class="info-block-header"><h4><?= in_array($_GET['tab'],['path','path_external_path']) ? '<a href="?edit='.$projectid.'&tab='.$tab_id.'&pathid='.$_GET['pathid'].'">'.$milestone_row['label'].'</a>' : '<span>'.$milestone_row['label'].'</span>' ?>
						<?= $milestone != 'Unassigned' ? '<img class="small no-gap-top milestone_name cursor-hand inline-img pull-left" src="../img/icons/ROOK-edit-icon.png">' : '' ?>
						<?= $milestone != 'Unassigned' && in_array($_GET['tab'],['path','path_external_path']) ? '<img class="small no-gap-top milestone_drag cursor-hand inline-img pull-right" src="../img/icons/drag_handle.png">
							<img class="small milestone_add cursor-hand no-gap-top inline-img pull-right" src="../img/icons/ROOK-add-icon.png">
							<img class="small milestone_rem cursor-hand no-gap-top inline-img pull-right" src="../img/remove.png">
							<input type="hidden" name="sort" value="'.$milestone_row['sort'].'">' : '' ?></h4>
						<input type="text" name="milestone_name" data-milestone="<?= $milestone ?>" data-id="<?= $milestone_row['id'] ?>" value="<?= $milestone_row['label'] ?>" style="display:none;" class="form-control">
					<a href="?edit=<?= $projectid ?>&tab=<?= $tab_id ?>"><div class="small"><?= ($count['tasks'] > 0 ? ' TASK: '.$count['tasks'] : ' ').($count['workorders'] > 0 ? ' WO: '.$count['workorders'] : ' ').($count['items'] > 0 ? ' C: '.$count['items'] : ' ').($count['intake'] > 0 ? ' INTAKE: '.$count['intake'] : ' ') ?><span class="pull-right"><?= $timeline != '' ? $timeline : '&nbsp;' ?></span></div><div class="clearfix"></div></a></div>
					<ul class="<?= $_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path' ? 'dashboard-list' : 'connectedChecklist no-margin full-width' ?>" data-milestone="<?= $milestone ?>">

						<?php while($item = mysqli_fetch_array($milestone_items)) {
							$border_colour = '';
							$type = $item[0];
							$label = $date = $link = $contents = $li_class = $flag_label = $item_external = '';
                            if($item[0] == 'Task') {
								$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='".$item[1]."'"));
								$item_external = $item['external'];
								if($item['status'] == $status_complete) {
									$li_class = 'strikethrough';
								}
								$data = 'data-id="'.$item['tasklistid'].'" data-table="tasklist" data-name="project_milestone" data-id-field="tasklistid"';
								$colour = $item['flag_colour'];
								if($colour == 'FFFFFF' || $colour == '') {
									$colour = 'F2F2F2';
								}
								$flag_label = $ticket_flag_names[$colour];
								$doc_table = "project_milestone_document";
								$doc_folder = "../Tasks/download/";
								$actions = '<img src="../img/icons/ROOK-edit-icon.png" class="inline-img" title="Edit" onclick="overlayIFrameSlider(\'../Tasks/add_task.php?type='.$item['status'].'&tasklistid='.$item['tasklistid'].'\');">'.
									(in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon" title="Flag This!">' : '').
									(!in_array('sync',$quick_actions) || substr($_GET['tab'],0,18) == 'path_external_path' ? '' : '<img src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" data-assigned="'.$item['assign_client'].'" class="inline-img assign-icon" title="Assign to External Path">').
									(in_array('alert',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" class="inline-img alert-icon" title="Activate Alerts &amp; Get Notified" data-users="'.$item['alerts_enabled'].'">' : '').
									(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email">' : '').
									(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon" title="Schedule Reminder">' : '').
									(in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File">' : '').
									(in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Reply">' : '').
									(in_array('time',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" class="inline-img time-icon" title="Add Time">' : '').
									(in_array('timer',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-timer2-icon.png" class="inline-img timer-icon" title="Track Time">' : '').
									(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img archive-icon" title="Archive">' : '');

								$label = '<input type="checkbox" name="status" data-table="tasklist" data-id="'.$item['tasklistid'].'" data-id-field="tasklistid" '.($item['status'] == $status_complete ? 'checked' : '').' data-incomplete="'.$status_incomplete.'" value="'.$status_complete.'" class="form-checkbox no-margin small">
                                    <div style="display:inline-block; width:calc(100% - 2em);" class="pull-right double-pad-bottom">
                                    <a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks/add_task.php?type='.$item['status'].'&tasklistid='.$item['tasklistid'].'\', \'50%\', false, false, $(\'.iframe_overlay\').closest(\'.container\').outerHeight() + 20); return false;">Task #'.$item['tasklistid'].'</a>: '.html_entity_decode($item['heading']).'</div>
                                    <div class="clearfix"></div>
									<input type="hidden" name="comment" value="" data-name="comment" data-table="taskcomments" data-id-field="taskcommid" data-id="" data-type="'.$item['tasklistid'].'" data-type-field="tasklistid">';

								$item_comments = mysqli_query($dbc, "SELECT * FROM `task_comments` WHERE `tasklistid`='".$item['tasklistid']."' AND `comment` != ''");
								while($item_comment = mysqli_fetch_assoc($item_comments)) {
									$comment = explode(':',$item_comment['comment']);
									if($comment[0] == 'document' && $comment[1] > 0 && count($comment) == 2) {
										$document = $dbc->query("SELECT * FROM `task_document` WHERE `taskdocid`='".$comment[1]."'")->fetch_assoc();
										$label .= '<p><small>'.profile_id($dbc, $item_comment['created_by'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right"><a href="../Tasks/download/'.$document['document'].'">'.$document['document'].'</a>';
										$label .= '<em class="block-top-5">Added by '.get_contact($dbc, $document['created_by']).' at '.$document['created_date'].'</em></span></small></p>';
									} else {
										$label .= '<p><small>'.profile_id($dbc, $item_comment['created_by'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($item_comment['comment']));
										$label .= '<em class="block-top-5">Added by '.get_contact($dbc, $item_comment['created_by']).' at '.$item_comment['created_date'].'</em></span></small></p>';
									}
								}
								$contents = '';
								foreach(explode(',',$item['alerts_enabled']) as $alertid) {
									if($alertid > 0) {
										if($border_colour == '') {
											$user_colour = get_contact($dbc, $alertid, 'calendar_color');
											if($user_colour != '') {
												$border_colour = 'border-style:solid;border-color:'.$user_colour.';';
											}
										}
										$contents .= '<span class="pull-left small col-sm-12">';
										$contents .= profile_id($dbc, $alertid, false).' Assigned to '.get_contact($dbc, $alertid);
										$contents .= '</span>';
									}
								}
								$contents .= '';
							} ?>
							<li class="dashboard-item <?= $li_class ?>" <?= $data ?> data-colour="<?= $colour ?>" style="<?= $colour != '' ? 'background-color: #'.$colour.';' : '' ?><?= $border_colour ?>"><span class="flag-label"><?= $flag_label ?></span>
                                <h4><?= $label ?></h4>
								<div class="action-icons"><?= $actions ?><?php if($_GET['tab'] == 'path' || $_GET['tab'] == 'path_external_path') { ?><img class="pull-right milestone-handle" src="../img/icons/drag_handle.png" style="height: 1em; margin-top: 0.5em;"><?php } ?></div>
								<?= $contents ?>
								<input type='text' name='reply' value='' data-table='<?= $type == 'Task' ? 'task_comments' : '' ?>' data-name='<?= $type == 'Task' ? 'comment' : '' ?>' class="form-control" style="display:none;">
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
						<?php } ?>
						<?php if($milestone != 'Unassigned') { ?>
							<li class="dashboard-item add_block">
								<?php if($tab_id != 'path' && $_GET['tab'] != 'path_external_path') { ?>
									<?php if(in_array('Intake',$tab_config)) { ?><a href="" onclick="addIntakeForm(this); return false;" data-milestone="<?= $milestone ?>" class="btn brand-btn pull-right">New Intake</a><?php } ?>
									<?php if(in_array('Work Orders',$tab_config)) { ?><a href="../Work Order/add_workorder.php?projectid=<?= $projectid ?>&milestone_timeline=<?= urlencode($milestone) ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" class="btn brand-btn pull-right">New Work Order</a><?php } ?>
									<?php if(in_array('Tickets',$tab_config)) { ?><a href="../Ticket/index.php?&edit=0&projectid=<?= $projectid ?>&milestone_timeline=<?= urlencode($milestone) ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,false,'auto',true); return false;" class="btn brand-btn pull-right">New <?= TICKET_NOUN ?></a><?php } ?>
									<?php if(in_array('Tasks',$tab_config) || in_array('Checklists',$tab_config)) { ?><a href="../Tasks/add_task.php?projectid=<?= $projectid ?>&contactid=<?= $_SESSION['contactid'] ?>&project_milestone=<?= urlencode($milestone) ?>" onclick="overlayIFrameSlider(this.href,'75%',true); return false;" class="btn brand-btn pull-right">New Task</a><?php } ?>
									<?php  ?><input type="text" placeholder="Add Task" name="task" onblur="addTask(this);" class="new_task form-control"><?php  ?>
								<?php } ?>
								<div class="clearfix"></div>
							</li>
						<?php } ?>
						<?php if($_GET['tab'] != 'path' && $_GET['tab'] != 'path_external_path') {
							include('next_buttons.php');
						} ?>
					</ul>
				</div>
			<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
<?php } ?>
