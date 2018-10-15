<?php
/*
 * Tasks Dashboard
 * Included Files:
 *  - index.php
 */
include_once('../include.php');
checkAuthorised('tasks');
$contactide = $_SESSION['contactid'];
$taskboardid = preg_replace('/[^0-9]/', '', $_GET['category']);
$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$task_colours = explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `flag_colours` FROM task_dashboard"))['flag_colours']);
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];
if(empty($url_tab)) {
    $url_tab = $_GET['tab'];
}
$dbc->query("INSERT INTO `taskboard_seen` (`taskboardid`, `tab`, `contactid`) SELECT '$taskboardid', '$url_tab', '{$_SESSION['contactid']}' FROM (SELECT COUNT(*) `rows` FROM `taskboard_seen` WHERE `taskboardid`='$taskboardid' AND IFNULL(`tab`,'".($url_tab == 'sales' ? '' : $url_tab)."') = '$url_tab' AND `contactid`='{$_SESSION['contactid']}') `num` WHERE `num`.`rows`=0");
$dbc->query("UPDATE `taskboard_seen` SET `seen_date`=CURRENT_TIMESTAMP WHERE `contactid`='{$_SESSION['contactid']}' AND `taskboardid`='$taskboardid' AND IFNULL(`tab`,'".($url_tab == 'sales' ? '' : $url_tab)."')='$url_tab'");
?>
<style>
.note_block ul, .note_block ul li { margin-left:0; padding-left:0; }
</style>
<script type="text/javascript" src="tasks.js"></script>
<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
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
                url: "task_ajax_all.php?fill=stop_timer&taskid="+taskid+"&timer_value="+timer_value+"&contactid="+contactid,
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

    $('.milestone_select').on('change', function(){
        if($(this).val() != '') {
            $(location).attr('href', $(this).val());
        }
    });

	milestoneActions();

    $('#task_userid').change(function() {
        var taskid = $(this).data('id');
        var staff_list = $(this).val();
        $.ajax({
            type: "GET",
            url: "task_ajax_all.php?fill=update_assigned_staff&taskid="+taskid+'&staff_list='+staff_list,
            dataType: "html",
            success: function(response) {}
        });
    });

    $('li.t_item').each(function() {
        $(this).find('.t_name').width( $(this).width() - $(this).find('.t_staff').outerWidth() - $(this).find('.t_drag').outerWidth() - 10 );
    });

});
$(document).on('change', 'select[name="change_milestone"]', function() { changeMilestone(this); });

function changeMilestone(sel, type = '') {
	if(sel != '') {
		var id = $(sel).val();
		$('.sortable_milestone').addClass('hidden-xs');
		$('.sortable_milestone#'+id).removeClass('hidden-xs');
	} else if(type == 'next') {
		var current_block = $('.sortable_milestone:not(.hidden-xs)');
		var next_block = $(current_block).next('.sortable_milestone');
		$('.sortable_milestone').addClass('hidden-xs');
		if(next_block.length == 0) {
			next_block = $('.sortable_milestone').first();
		}
		$('.sortable_milestone').addClass('hidden-xs');
		$(next_block).removeClass('hidden-xs');
		$('select[name="change_milestone"]').val($(next_block).prop('id'));
		$('select[name="change_milestone"]').trigger('change.select2');
	} else if(type == 'prev') {
		var current_block = $('.sortable_milestone:not(.hidden-xs)');
		var prev_block = $(current_block).prev('.sortable_milestone');
		$('.sortable_milestone').addClass('hidden-xs');
		if(prev_block.length == 0) {
			prev_block = $('.sortable_milestone').last();
		}
		$('.sortable_milestone').addClass('hidden-xs');
		$(prev_block).removeClass('hidden-xs');
		$('select[name="change_milestone"]').val($(prev_block).prop('id'));
		$('select[name="change_milestone"]').trigger('change.select2');
	}
}

function milestone_reporting(sel) {
    $('.milestone_select').toggle();
}

function milestoneActions() {
	$('.scrum_tickets').sortable({
		handle: '.milestone_drag',
		items: '.connectedSortable',
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
			$(this).hide().prevAll('h4').show().find('a').text(this.value);
			$.post('task_ajax_all.php?action=milestone_edit', { id: $(this).data('id'), table: $(this).data('table'), field: 'label', value: this.value });
		});
	});
	$('.milestone_add').off('click').click(function() {
		var list = $(this).closest('.sortable_milestone');
		var clone = list.clone();
		clone.find('.ui-state-default').remove();
		clone.find('.info-block-header h4 a').text('New Milestone');
		clone.find('.info-block-header input[name=milestone_name]').val('');
		clone.find('.info-block-header [name=sort]').val('');
		$.post('task_ajax_all.php?action=milestone_edit', { id: 0, field: 'sort', value: list.find('.info-block-header [name=sort]').data('sort'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), taskboard: '<?= $_GET['category'] ?>' }, function(response) {
			clone.find('.info-block-header input[name=milestone_name]').data('id',response);
			var classes = clone.attr('class').split(' ');
			classes[2] = 'milestone.'+response;
			clone.attr('class',classes.join(' '));
		});
		list.after(clone);
		milestoneActions();
		tasksInit();
	});
	$('.milestone_rem').off('click').click(function() {
		$(this).closest('.sortable_milestone').remove();
		DoubleScroll(document.getElementById('scrum_tickets'));
		$.post('task_ajax_all.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), field: 'deleted', value: 1 });
	});
	$('.info-block-header [name=sort').off('change').change(function() {
		$.post('task_ajax_all.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), field: 'sort', value: this.value });
	});
}

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
       if(task_id.toString().substring(0,5) == 'BOARD') {
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, true);
}
/* Function call before the slider
function send_reply(task) {
	task_id = $(task).parents('span').data('task');
	if(task_id.toString().substring(0,5) == 'BOARD') {
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, true);
}
/* Function call before the slider
function send_reply(task) {
	task_id = $(task).parents('span').data('task');
	if(task_id.toString().substring(0,5) == 'BOARD') {
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, true);
}
/* Function call before the slider
function send_reply(task) {
	task_id = $(task).parents('span').data('task');
	if(task_id.toString().substring(0,5) == 'BOARD') {
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, true);
}
/* Function call before the slider
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
} */

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
				complete: function(result) { console.log(result.responseText); window.location.reload();
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: task_id, reply: 'Time added '+time+':00' },
                        complete: function(result) { console.log(result.responseText); window.location.reload(); }
                    });
                }
			});
		}
	});
	$('[name=task_time_'+task_id+']').timepicker('show');
}

function track_time(task) {
    var task_id = $(task).parents('span').data('task');
    $('.timer_block_'+task_id).toggle();
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

function flag_item_manual(task) {
	var item = $(task).closest('li');
	item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').show();
	item.find('[name=flag_cancel]').off('click').click(function() {
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		return false;
	});
	item.find('[name=flag_off]').off('click').click(function() {
		item.find('[name=colour]').val('FFFFFF');
		item.find('[name=label]').val('');
		item.find('[name=flag_start]').val('');
		item.find('[name=flag_end]').val('');
		item.find('[name=flag_it]').click();
		return false;
	});
	item.find('[name=flag_it]').off('click').click(function() {
		$.ajax({
			url: '../Tasks/task_ajax_all.php?fill=taskflagmanual',
			method: 'POST',
			data: {
				value: item.find('[name=colour]').val(),
				label: item.find('[name=label]').val(),
				start: item.find('[name=flag_start]').val(),
				end: item.find('[name=flag_end]').val(),
				id: item.find('[data-task]').data('task')
			}
		});
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		item.data('colour',item.find('[name=colour]').val());
		item.css('background-color','#'+item.find('[name=colour]').val());
		item.find('.flag-label').text(item.find('[name=label]').val());
		return false;
	});
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

function task_archive(task) {
	task_id = $(task).parents('span').data('task');
    //alert(task_id);
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
	if(type=='task board' && confirm("Are you sure you want to archive this task board?")) {
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=delete_board&boardid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				var tab='<?=$_GET['tab']?>';
				window.location.replace("<?= WEBSITE_URL; ?>/Tasks/index.php");
			}
		});
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
function savePathName(name) {
	$.post('task_ajax_all.php?action=set_path_name', {name:name,taskboard:<?= $taskboardid > 0 ? $taskboardid : 0 ?>});
}

//Checklist functions
function checklist_flag(checklist) {
	checklistid = $(checklist).closest('span').data('checklist');
	$.ajax({
		method: "POST",
		url: "tasks_ajax.php?fill=checklistFlagItem",
		data: { id: checklistid },
		complete: function(result) {
			$(checklist).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}

function checklist_email(checklist) {
	task_board = $(checklist).closest('span').data('taskboard');
	checklistid = $(checklist).closest('span').data('checklist');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=task_checklist&checklistid='+checklistid+'&task_board='+task_board, 'auto', false, true);
}

function checklist_reminder(checklist) {
	task_board = $(checklist).closest('span').data('taskboard');
	checklistid = $(checklist).closest('span').data('checklist');
	var item = $(checklist).closest('li');
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
					url: 'task_ajax_all.php?fill=checklistReminder',
					data: {
						taskboardid: task_board,
						id: checklistid,
						value: reminder,
						users: users,
					},
					success: function(result) {
						select.hide();
						select.find('select').trigger('change.select2');
					}
				});
			}
			return false;
		});
		select.show();
	}).focus();
}

function checklist_archive(checklist) {
	checklistid = $(checklist).closest('span').data('checklist');
	if(confirm('Are you sure you want to archive this Checklist?')) {
		$.ajax({
			method: 'POST',
			url: '../Sales/sales_ajax_all.php?fill=checklistArchive',
			data: {
				id: checklistid
			},
			success: function(result) {
				console.log(result);
				$(checklist).closest('li').remove();
			}
		});
	}
}

function checklist_attach_file(checklist) {
	checklistid = $(checklist).closest('span').data('checklist');
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

<div class="container">
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>

	<div class="row hide_on_iframe">
        <!--
        <div class="pull-left tab double-gap-top hide-titles-mob">
			<span class="popover-examples list-inline">
				<a data-toggle="tooltip" data-placement="top" title="Unassigned tasks appear in this task board."><img src="../img/info.png" width="20"></a>
				<img class="" src="../img/alert.png" border="0" alt="" />
			</span>
		</div>
        -->

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

        //if($_GET['category'] !== 'All') {
            $task_board = mysqli_fetch_array(mysqli_query($dbc, "SELECT `taskboardid`, `flag_colour`, `task_path_name` FROM `task_board` WHERE `taskboardid`='{$_GET['category']}'"));
            $task_flag = $task_board['flag_colour'];
			if ( !empty($taskboardid) ) {
				$task_path = get_task_board($dbc, $taskboardid, 'task_path');
			}
			$path_name = empty($task_board['task_path_name']) ? ($task_path > 0 ? get_project_path_milestone($dbc, $task_path, 'project_path') : 'New Path') : $task_board['task_path_name']; ?>
            <form name="form_sites" method="post" action="" class="form-inline" role="form" <?php echo ($task_flag == '' ? '' : 'style="background-color: #'.$task_flag.';"'); ?>>
				<span class="pull-left col-sm-6"><h3>Path: <?= '<span>'.$path_name.'</span>'.($task_path > 0 ? '<img class="inline-img cursor-hand small" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).hide();$(this).next(\'span\').show().find(\'input\').focus();"><span class="col-sm-4 pull-right" style="display:none;"><input onblur="savePathName(this.value); $(this).parent().hide().prev().show().prev().text(this.value);" type="text" value="'.$path_name.'" class="form-control"></span>' : '') ?></h3></span>
                <!--
                <span class="pull-right double-gap-top" style="cursor: pointer;" data-task="BOARD<?php echo $_GET['category']; ?>">
                    <?php if(in_array('flag', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('alert', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Activate Alerts and Get Notified" onclick="send_alert(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('email', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('reminder', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('attach', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('archive', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Archive Task Board" onclick="task_archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2em;"></span><?php } ?>
                    <br /><input type="text" name="reminder_board_<?php echo $_GET['category']; ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
                </span>
                -->


                <span class="pull-right text-right double-gap-top" style="" data-task="BOARD<?php echo $_GET['category']; ?>">
										<span class="theme-color-icon" style="cursor:pointer; padding: 0.25em 0.5em 0.25em 0;" title="Task Board History" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/task_history.php?taskboardid=<?=$taskboardid?>');"><img src="<?php echo WEBSITE_URL; ?>/img/time-machine.png" style="height:2em;"></span>
                    <!-- <span style="cursor:pointer;"><img src="../img/icons/pie-chart.png" class="gap-right" onclick="milestone_reporting(this);" /></span> -->
                    <a href=""><img src="../img/clear-checklist.png" class="" alt="Clear Completed Tasks" title="Clear Completed Tasks" style="height:2em;" onclick="clearCompleted(this);" /></a><?php
                    if ( !empty($_GET['category']) && !empty($_GET['tab']) && $_GET['tab'] != 'sales') { ?>
                        <span class="" style="cursor:pointer; padding: 0.25em 0.5em;" title="Edit Task Board" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_taskboard.php?taskboardid=<?=$_GET['category']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-edit-icon.png" style="height:2em;"></span>
                    <?php }
					if ( !empty($_GET['category']) && !empty($_GET['tab']) && in_array('archive', $quick_actions) && $_GET['tab'] != 'sales') { ?>
                        <span class="" style="cursor:pointer; padding: 0.25em 0.5em 0.25em 0;" title="Archive Task Board" onclick="task_archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2em;"></span><?php
                    } ?>

										<br />
                    <select class="milestone_select" style="display:none; margin-top:10px; width:100%;">
                        <option value="" disabled selected>Select Milestone...</option><?php
                        $taskboardid = isset($_GET['category']) ? trim($_GET['category']) : '';
                        if ( !empty($taskboardid) ) {
                            $each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
                            foreach ($each_tab as $cat_tab) {
                                echo '<option value="?category='.$_GET['category'].'&tab='.$_GET['tab'].'&milestone='.$cat_tab.'">'. $cat_tab .'</option>';
                            }
                        } ?>
                    </select>
                </span>
                <div class="clearfix"></div>
                <input type="file" name="attach_board_<?php echo $_GET['category']; ?>" style="display:none;" />

                <div class="clearfix"></div>

                <div id="scrum_tickets" class="scrum_tickets"><?php
					$taskboardid = filter_var($_GET['category']);
                    if($_GET['tab'] == 'sales') {
						$task_path = get_field_value('sales_path', 'sales', 'salesid', $taskboardid);

						$tabs = get_field_value('milestone timeline', 'sales_path', 'pathid', $task_path);
						$each_tab = explode('#*#', $tabs['milestone']);
						$timeline = explode('#*#', $tabs['timeline']);
						$prior_sort = 0;
						foreach($each_tab as $i => $milestone) {
							$milestone_rows = $dbc->query("SELECT `sort` FROM `sales_path_custom_milestones` WHERE `salesid`='$taskboardid' AND `milestone`='$milestone'");
							if($milestone_rows->num_rows > 0) {
								$prior_sort = $milestone_rows->fetch_assoc()['sort'];
							} else {
								$dbc->query("INSERT INTO `sales_path_custom_milestones` (`salesid`,`milestone`,`label`,`sort`) VALUES ('$taskboardid','$milestone','$milestone','$prior_sort')");
							}
						}
						$milestones = $dbc->query("SELECT `id`, `milestone`, `label`, `sort`, 'sales_path_custom_milestones' `table` FROM `sales_path_custom_milestones` WHERE `deleted`=0 AND `salesid`='$taskboardid' ORDER BY `sort`, `id`");
					} else {
                        $task_path = get_task_board($dbc, $taskboardid, 'task_path');

						$each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
						$timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));
						$additional_milestones_query = mysqli_query($dbc, "SELECT milestone FROM task_additional_milestones WHERE task_board_id='$taskboardid'");
						if ( $additional_milestones_query->num_rows>0 ) {
							while ( $row_milestone=mysqli_fetch_assoc($additional_milestones_query) ) {
								$each_tab[] = $row_milestone['milestone'];
							}
						}
						$additional_milestones_query = mysqli_query($dbc, "SELECT `task_milestone_timeline` FROM tasklist WHERE task_board='$taskboardid' GROUP BY `task_milestone_timeline`");
						if ( $additional_milestones_query->num_rows>0 ) {
							while ( $row_milestone=mysqli_fetch_assoc($additional_milestones_query) ) {
								if(!in_array($row_milestone['task_milestone_timeline'],$each_tab)) {
									$each_tab[] = $row_milestone['task_milestone_timeline'];
								}
							}
						}
						$prior_sort = 0;
						foreach($each_tab as $i => $milestone) {
							$milestone_rows = $dbc->query("SELECT `sort` FROM `taskboard_path_custom_milestones` WHERE `taskboard`='$taskboardid' AND `milestone`='$milestone'");
							if($milestone_rows->num_rows > 0) {
								$prior_sort = $milestone_rows->fetch_assoc()['sort'];
							} else {
								$dbc->query("INSERT INTO `taskboard_path_custom_milestones` (`taskboard`,`milestone`,`label`,`sort`) VALUES ('$taskboardid','$milestone','$milestone','$prior_sort')");
							}
						}
						$milestones = $dbc->query("SELECT `id`, `milestone`, `label`, `sort`, 'taskboard_path_custom_milestones' `table` FROM `taskboard_path_custom_milestones` WHERE `deleted`=0 AND `taskboard`='$taskboardid' ORDER BY `sort`, `id`");
					}
					$i=0; ?>

					<?php if(count($each_tab) > 0 && (count($each_tab) == 1 && empty($each_tab[0]) ? false : true)) { ?>
						<div class="col-xs-12 gap-bottom show-on-mob">
							<div class="col-xs-2">
								<a href="" onclick="changeMilestone('', 'prev'); return false"><img src="../img/icons/back-arrow.png" style="height: 2em;" class="pull-left"></a>
							</div>
							<div class="col-xs-8">
								<select name="change_milestone" class="chosen-select-deselect" data-placeholder="Select a Milestone...">
									<?php foreach ($each_tab as $cat_tab) {
										echo '<option value="sortable'.$i.'">'.$cat_tab.'</option>';
										$i++;
									} ?>
								</select>
							</div>
							<div class="col-xs-2">
								<a href="" onclick="changeMilestone('', 'next'); return false"><img src="../img/icons/next-arrow.png" style="height: 2em;" class="pull-right"></a>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>

					<?php $i = 0;

					/* if ( $url_tab == 'My' ) {
                        $result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE (tl.contactid IN (".$_SESSION['contactid'].") OR (tb.board_security='Company' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%')) AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_tododate");
                    } */

                    if($milestones->num_rows > 0) {
						while($milestone_row = $milestones->fetch_assoc()) {
							$cat_tab = $milestone_row['milestone'];
							$label = $milestone_row['label'] ?: 'Tasks';
							if ( $url_tab == 'Private' ) {
								//$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE contactid IN (". $_SESSION['contactid'] .") AND (task_path='$task_path' OR '$task_path' = '') AND (task_milestone_timeline='$cat_tab' OR ('$cat_tab' = '' AND task_milestone_timeline NOT IN ('".implode("','",$each_tab)."'))) AND task_board = '$taskboardid' AND (DATE(`archived_date`) >= (DATE(NOW() - INTERVAL 3 DAY)) OR archived_date IS NULL OR archived_date = '0000-00-00') AND `deleted`=0 ORDER BY task_path ASC, tasklistid DESC");
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tl.contactid IN (".$_SESSION['contactid'].") AND tb.taskboardid='$taskboardid' AND tb.board_security='Private' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'Company' ) {
								$result = mysqli_query($dbc, "SELECT tl.*, tb.company_staff_sharing FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tb.taskboardid='$taskboardid' AND tb.board_security='Company' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'Project' ) {
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tl.contactid IN (".$_SESSION['contactid'].") AND tb.taskboardid='$taskboardid' AND tb.board_security='Project' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'Client' ) {
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tl.contactid IN (".$_SESSION['contactid'].") AND tb.taskboardid='$taskboardid' AND tb.board_security='Client' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 AND (tb.contactid=tl.clientid OR tb.businessid=tl.businessid) ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'sales' ) {
								$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE IFNULL(sales_milestone,'')='{$milestone_row['milestone']}' AND IFNULL(archived_date,'0000-00-00')='0000-00-00' AND deleted=0 AND `salesid`='$taskboardid' ORDER BY tasklistid DESC");
							} else {
								//$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_board='$taskboardid' AND task_milestone_timeline='$cat_tab' AND contactid IN (". $_SESSION['contactid'] .") ORDER BY task_path ASC, tasklistid DESC");
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.contactid IN (". $_SESSION['contactid'] .") AND tb.board_security='$url_tab' ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							}

							if ( empty($cat_tab) && $url_tab == 'Client' ) {
								$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_board='$taskboardid' AND task_milestone_timeline='$cat_tab' AND clientid <> '' AND contactid IN (". $_SESSION['contactid'] .") AND (archived_date IS NULL OR archived_date='0000-00-00') AND deleted=0 ORDER BY task_tododate");
							}

							$checklist_result = mysqli_query($dbc, "SELECT * FROm `checklist` WHERE `task_path` = '$task_path' AND `task_board` = '$taskboardid' AND `task_milestone_timeline` = '$cat_tab' AND `deleted` = 0");

							$task_count = mysqli_num_rows($result);

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
							/* if($get_config['total_unread'] > 0) {
								$alert = '&nbsp;<img src="../img/alert.png" border="0" alt="" />';
							} */

							echo '<ul id="sortable'.$i.'" class="sortable_milestone connectedSortable '.$status.' '.$class_on.' '.($i > 0 ? 'hidden-xs' : '').'" style="padding-top:0;">'; ?>

							<div class="info-block-header">
								<h4 class="pad-top"><?= '<a href="?category='.$_GET['category'].'&tab='.$_GET['tab'].'&milestone='.$cat_tab.'" class="pull-left">'. $label .'</a>'. $alert ?><!--<a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_milestones.php?task_board=<?=$taskboardid?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img class="no-margin black-color inline-img pull-right" src="../img/icons/ROOK-add-icon.png" /></a>-->
									<img class="small no-gap-top milestone_name cursor-hand inline-img pull-left gap-left" src="../img/icons/ROOK-edit-icon.png">
									<img class="small no-gap-top milestone_drag cursor-hand inline-img pull-right" src="../img/icons/drag_handle.png">
									<img class="small milestone_add cursor-hand no-gap-top inline-img pull-right" src="../img/icons/ROOK-add-icon.png">

									<img class="small milestone_rem cursor-hand no-gap-top inline-img pull-right" src="../img/remove.png">

									<input type="hidden" name="sort" value="<?= $milestone_row['sort'] ?>"></h4>
									<input type="text" name="milestone_name" data-milestone="<?= $cat_tab ?>" data-id="<?= $milestone_row['id'] ?>" data-table="<?= $milestone_row['table'] ?>" value="<?= $label ?>" style="display:none;" class="form-control">
								<!--
								<a href=""> --><?php
									/* if ( empty($task_count) ) {
										$task_count = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(tasklistid) AS count FROM tasklist WHERE task_path='$task_path' AND task_milestone_timeline='$cat_tab' AND task_board='$taskboardid' AND `deleted`=0"));
										$task_count = $task_count['count'];
									} */ ?>
									<!-- <div class="small">TASKS: <?= $task_count ?></div> -->
									<div class="clearfix"></div>
								<!--
								</a>
								-->
								<div class="clearfix"></div>
							</div><?php
							/* echo '<li class="ui-state-default ui-state-disabled no-sort '.$class_on_2.'">';
							echo $alert.$cat_tab.'<br>'.$timeline[$i].'</li>'; */

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

                                if ( $row['task_milestone_timeline']==$cat_tab ) {
                                    echo '<li id="'.$row['tasklistid'].'" data-table="tasklist" data-id-field="tasklistid" class="ui-state-default t_item '.$class_on.'" style="margin-top:4px; '.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'">';

                                    $businessid = $url_tab=='Business' ? $row['businessid'] : '';
                                    $clientid = $url_tab=='Client' ? $row['clientid'] : '';

                                    $past = 0;

                                    $date = new DateTime($row['task_tododate']);
                                    $now = new DateTime();

                                    if($date < $now && $row['status'] != $status_complete) {
                                        $past = 1;
                                    }



                                    //echo '<span class="pull-right action-icons gap-top" data-task="'.$row['tasklistid'].'">';
                                        //echo '<img class="drag_handle pull-right inline-img" src="../img/icons/drag_handle.png" />';
                                    //echo '</span>'; ?>
                                    <div class="row pull-left t_name">
                                        <h4 style="<?= $style_strikethrough ?>">
                                            <input type="checkbox" name="status" value="<?= $row['tasklistid'] ?>" class="form-checkbox no-margin small pull-left" onchange="mark_done(this);" <?= ( $row['status'] == $status_complete ) ? 'checked' : '' ?> />
                                            <div class="pull-left gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $row['tasklistid'] ?></a></div>: <?= ($url_tab=='Business') ? get_contact($dbc, $businessid, 'name') . ': ' : '' ?><?= ($url_tab=='Client') ? get_contact($dbc, $clientid) . ': ' : '' ?><span><?= $row['heading']; ?></span>
                                        </h4>
                                    </div>
                                    <span class="pull-right action-icons offset-top-5 t_drag" data-task="<?= $row['tasklistid'] ?>">
                                        <img class="drag_handle pull-right inline-img" src="../img/icons/drag_handle.png" />
                                    </span>
                                    <div class="small pull-right offset-top-5 t_staff"><?php
                                        if ( $row['company_staff_sharing'] ) {
                                            foreach ( array_filter(explode(',', $row['company_staff_sharing'])) as $staffid ) {
                                                profile_id($dbc, $staffid);
                                            }
                                        } else {
                                            profile_id($dbc, $row['contactid']);
                                        } ?>
                                    </div>
                                    <div class="clearfix"></div><?php


                                    echo '<span class="pull-right action-icons double-gap-bottom gap-top" style="width: 100%;" data-task="'.$row['tasklistid'].'">';
                                        $mobile_url_tab = trim($_GET['tab']);
                                        if ( $url_tab=='Project' || $mobile_url_tab=='Project' ) { ?>
                                            <span style="display:inline-block; text-align:center; width:11%"><a href="../Project/projects.php?edit=<?= $row['projectid'] ?>" title="View Project" style="background-color:#fff; border:1px solid #3ac4f2; border-radius:50%; color:#3ac4f2 !important; display:inline-block; height:1.5em; width:1.5em;">►</a></span><?php
                                        }
                                        if (in_array('edit', $quick_actions)) { ?>
                                            <span title="Edit Task" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?=WEBSITE_URL?>/img/icons/ROOK-edit-icon.png" class="inline-img" onclick="return false;"></span><?php
                                        }
                                        echo in_array('flag_manual', $quick_actions) ? '<span title="Flag This!" onclick="flag_item_manual(this); return false;"><img src="../img/icons/ROOK-flag-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo !in_array('flag_manual', $quick_actions) && in_array('flag', $quick_actions) ? '<span title="Flag This!" onclick="flag_item(this); return false;"><img src="../img/icons/ROOK-flag-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo $row['projectid'] > 0 && in_array('sync', $quick_actions) ? '<span title="Sync to External Path" onclick="sync_task(this); return false;"><img src="../img/icons/ROOK-sync-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('alert', $quick_actions) ? '<span title="Send Alert" onclick="send_alert(this); return false;"><img src="../img/icons/ROOK-alert-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('email', $quick_actions) ? '<span title="Send Email" onclick="send_email(this); return false;"><img src="../img/icons/ROOK-email-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('reminder', $quick_actions) ? '<span title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="../img/icons/ROOK-reminder-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('attach', $quick_actions) ? '<span title="Attach File(s)" onclick="attach_file(this); return false;"><img src="../img/icons/ROOK-attachment-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('reply', $quick_actions) ? '<span title="Comment" onclick="send_reply(this); return false;"><img src="../img/icons/ROOK-reply-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('time', $quick_actions) ? '<span title="Add Time" onclick="quick_add_time(this); return false;"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('timer', $quick_actions) ? '<span title="Track Time" onclick="track_time(this); return false;"><img src="../img/icons/ROOK-timer2-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                        echo in_array('archive', $quick_actions) ? '<span title="Archive Task" onclick="task_archive(this); return false;"><img src="../img/icons/ROOK-trash-icon.png" class="inline-img" onclick="return false;"></span>' : '';
                                    echo '</span>';
									if(in_array('flag_manual',$quick_actions)) { ?>
										<span class="col-sm-3 text-center flag_field_labels" style="display:none;">Label</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Colour</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Start Date</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">End Date</span>
										<div class="col-sm-3"><input type='text' name='label' value='<?= $row['flag_label'] ?>' class="form-control" style="display:none;"></div>
										<div class="col-sm-3"><select name='colour' class="form-control" style="display:none;background-color:#<?= $row['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
												<option value="FFFFFF" style="background-color:#FFFFFF;">No Flag</option>
												<?php foreach($task_colours as $flag_colour) { ?>
													<option <?= $row['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"></option>
												<?php } ?>
											</select></div>
										<div class="col-sm-3"><input type='text' name='flag_start' value='<?= $row['flag_start'] ?>' class="form-control datepicker" style="display:none;"></div>
										<div class="col-sm-3"><input type='text' name='flag_end' value='<?= $row['flag_end'] ?>' class="form-control datepicker" style="display:none;"></div>
										<button class="btn brand-btn pull-right" name="flag_it" onclick="return false;" style="display:none;">Flag This</button>
										<button class="btn brand-btn pull-right" name="flag_cancel" onclick="return false;" style="display:none;">Cancel</button>
										<button class="btn brand-btn pull-right" name="flag_off" onclick="return false;" style="display:none;">Remove Flag</button>
									<?php }
                                    echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
                                    echo '<input type="text" name="task_time_'.$row['tasklistid'].'" style="display:none;" class="form-control timepicker" />'; ?>
                                    <div class="timer_block_<?= $row['tasklistid'] ?>" style="display:none; margin-top:2.2em;">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Timer:</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="timer_<?= $row['tasklistid'] ?>" id="timer_value" style="float:left; max-width:56%;" class="form-control timer" placeholder="0 sec" />&nbsp;&nbsp;
                                                <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                                                <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $row['tasklistid'] ?>">Stop</a>
                                            </div>
                                        </div>
                                    </div><?php
                                    echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none;" class="form-control datepicker" />';
                                    echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
                                    echo '<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect" data-id="'.$row['tasklistid'].'"><option value="unassign">Unassigned</option>';
                                    foreach(array_unique(array_filter(explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`project_path_milestone`.`milestone` SEPARATOR '#*#') `milestones` FROM `project` LEFT JOIN `project_path_milestone` ON CONCAT(',',`project`.`external_path`,',') LIKE CONCAT('%,',`project_path_milestone`.`project_path_milestone`,',%') WHERE `projectid`='".$row['projectid']."'"))['milestones']))) as $external_milestone) { ?>
                                            <option <?= $external_milestone == $row['external'] ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
                                    <?php }
                                    echo '</select></div><div class="clearfix"></div>';
                                    //echo '<a href="add_tasklist.php?type='.$row['status'].'&tasklistid='.$row['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">';
                                    //echo limit_text($row['heading'], 5 ).'</a><img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /><span class="pull-right">'; ?>
                                    <!--
                                        <div class="form-group gap">
                                            <div class="col-sm-3">Assign Staff:</div>
                                            <div class="col-sm-9">
                                                <select id="task_userid" data-placeholder="Select Users" multiple name="task_userid[]" data-table="tasklist" data-field="contactid" data-id="<?= $row['tasklistid'] ?>" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                                                    <option value=""></option>
                                                    <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                                    foreach($staff_list as $staff_id) { ?>
                                                        <option <?= (strpos(','.$row['contactid'].',', ','.$staff_id.',') !== false) ? ' selected' : ''; ?> value="<?= $staff_id; ?>"><?= get_contact($dbc, $staff_id); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    -->

                                    <?php

                                    echo '<div class="clearfix gap-top"></div>';
                                    $documents = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `document` FROM `task_document` WHERE `tasklistid`='{$row['tasklistid']}' ORDER BY `taskdocid` DESC");
                                    if ( $documents->num_rows > 0 ) { ?>
                                        <div class="form-group clearfix full-width">
                                            <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
                                                while ( $row_doc=mysqli_fetch_assoc($documents) ) { ?>
                                                    <div class="note_block row">
                                                        <div class="col-xs-1"><?= profile_id($dbc, $row_doc['created_by']); ?></div>
                                                        <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                                                            <div><a href="../Tasks/download/<?= $row_doc['document'] ?>"><?= $row_doc['document'] ?></a></div>
                                                            <div><em>Added by <?= get_contact($dbc, $row_doc['created_by']); ?> on <?= $row_doc['created_date']; ?></em></div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <hr class="margin-vertical" /><?php
                                                } ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div><?php
                                    }
                                    $comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='{$row['tasklistid']}' AND `deleted`=0 ORDER BY `taskcommid` DESC");
                                    if ( $comments->num_rows > 0 ) { ?>
                                        <div class="form-group clearfix full-width">
                                            <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
                                                $odd_even = 0;
                                                while ( $row_comment=mysqli_fetch_assoc($comments) ) {
                                                    $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                                                    <div class="note_block row <?= $bg_class ?>">
                                                        <div class="col-xs-1"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
                                                        <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                                                            <div><?= html_entity_decode($row_comment['comment']); ?></div>
                                                            <div><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div><?php
                                                    $odd_even++;
                                                } ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div><?php
                                    }

                                    echo '</li>';
                                }

                                $task_path = $row['task_path'];
                                $task_board = $row['task_board'];
							}
							if(get_config($dbc, 'task_include_checklists') == 1) {
								while($row = mysqli_fetch_array( $checklist_result )) {
									$colour = $row['flag_colour'];
									if($colour == 'FFFFFF' || $colour == '') {
										$colour = '';
									}
									echo '<li style="margin-top: 4px;  '.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'" data-id-field="checklistid" id="'.$row['checklistid'].'" data-table="checklist" class="ui-state-default">';
									echo '<input type="file" name="attach_checklist_board_'.$row['checklistid'].'" style="display:none;" />';
									echo '<div class="row pull-left"><h4><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/checklist.php?view='.$row['checklistid'].'&iframe_slider=1\'); return false;">'.$row['checklist_name'].'</a></h4></div><span class="pull-right action-icons offset-top-5" data-checklist="'.$row['checklistid'].'"><img class="drag_handle pull-right inline-img" src="../img/icons/drag_handle.png" /></span><div class="clearfix"></div>';
									echo '<span class="pull-right action-icons" style="width: 100%;" data-checklist="'.$row['checklistid'].'" data-taskboard="'.$task_board.'">'.
										'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/edit_checklist.php?edit='.$row['checklistid'].'\'); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img" title="Edit"></a>'.
										(in_array('flag_manual',$quick_actions) || in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" onclick="checklist_flag(this); return false;" class="inline-img flag-icon" title="Flag This!">' : '').
										(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" onclick="checklist_email(this); return false;" class="inline-img email-icon" title="Send Email">' : '').
										(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" onclick="checklist_reminder(this); return false;" class="inline-img reminder-icon" title="Schedule Reminder">' : '').
										(in_array('attach', $quick_actions) ? '<img src="../img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" onclick="checklist_attach_file(this); return false;" title="Attach File(s)">' : '').
										(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" onclick="checklist_archive(this); return false;" class="inline-img archive-icon" title="Archive">' : '');
									echo '</span>';
									if(in_array('flag_manual',$quick_actions)) { ?>
										<span class="col-sm-3 text-center flag_field_labels" style="display:none;">Label</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Colour</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Start Date</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">End Date</span>
										<div class="col-sm-3"><input type='text' name='label' value='<?= $row['flag_label'] ?>' class="form-control" style="display:none;"></div>
										<div class="col-sm-3"><select name='colour' class="form-control" style="display:none;background-color:#<?= $row['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
												<option value="FFFFFF" style="background-color:#FFFFFF;">No Flag</option>
												<?php foreach($flag_colours as $flag_colour) { ?>
													<option <?= $row['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"></option>
												<?php } ?>
											</select></div>
										<div class="col-sm-3"><input type='text' name='flag_start' value='<?= $row['flag_start'] ?>' class="form-control datepicker" style="display:none;"></div>
										<div class="col-sm-3"><input type='text' name='flag_end' value='<?= $row['flag_end'] ?>' class="form-control datepicker" style="display:none;"></div>
										<button class="btn brand-btn pull-right" name="flag_it" onclick="return false;" style="display:none;">Flag This</button>
										<button class="btn brand-btn pull-right" name="flag_cancel" onclick="return false;" style="display:none;">Cancel</button>
										<button class="btn brand-btn pull-right" name="flag_off" onclick="return false;" style="display:none;">Remove Flag</button>
									<?php }
									echo '<div class="clearfix"></div>'; ?>

									<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect"><option value="unassign">Unassigned</option>
										<?php foreach($external_path as $external_milestone) { ?>
											<option <?= $external_milestone == $item_external ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
										<?php } ?></select></div>
									<div class="select_users" style="display:none;">
										<select data-placeholder="Select Staff" multiple class="chosen-select-deselect">
										<?php foreach($staff_list as $staff_id) { ?>
											<option value="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></option>
										<?php } ?>
										</select>
										<button class="submit_button btn brand-btn pull-right">Submit</button>
										<button class="cancel_button btn brand-btn pull-right">Cancel</button>
									</div><?php
									echo '<input type="text" name="reminder" value="" class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;float:right;">';

									$checklistid = $row['checklistid'];
									$_GET['view'] = $checklistid;
									$_GET['override_block'] = 'true';
									$_GET['hide_header'] = 'true';
									$_GET['different_function_name'] = 'true';
							        echo '<div class="checklist_screen" data-querystring="view='.$checklistid.'&override_block=true&hide_header=true&different_function_name=true">';
									include('../Checklist/view_checklist.php');
									echo '</div>';

									echo '<div class="clearfix"></div>';
								echo '</li>';
								}
							}

							if(is_array($task_board)) {
								$task_board = $task_board['taskboardid'];
							}

							echo '<li class="new_task_box no-sort"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to quickly add a task and then hit Enter."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>
								<input onChange="changeEndAme(this)" name="add_task" placeholder="Quick Add" id="add_new_task '.$status.' '.$task_path.' '.$task_board.'" type="text" class="form-control" style="max-width:96%;" /></li>';

                            ?>

							<li class="no-sort"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?tab=<?=$_GET['tab']?>&task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$task_board?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right">Add Task</a><?php if(get_config($dbc, 'task_include_checklists') == 1) { ?><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Checklist/edit_checklist.php?edit=NEW&iframe_slider=1&add_to_taskboard=1&task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$task_board?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right">Add Checklist</a><?php } ?></li><?php

							echo '</ul>';
							$i++;
						}
					} else {
						echo "<h3>No Tasks Found</h3>";
					} ?>
                </div><!-- #scrum_tickets -->
            </form><?php
        //} ?>
	</div><!-- .hide_on_iframe -->
</div><!-- .container -->
