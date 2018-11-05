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
$quick_actions = explode(',',get_config($dbc, 'task_quick_action_icons'));
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
.new_task_box { border:1px solid #ACA9A9; margin:6px !important; padding:10px !important; }
.flag_color_box{background-color: #fff;padding: 10px;min-width: 250px;position: absolute;left: 0;top: 40px;z-index: 1;border: 2px solid #878787;}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.css" rel="stylesheet">
<script type="text/javascript" src="tasks.js"></script>
<script>
$(document).ready(function() {
	$('.demo_cpicker').colorpicker();

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
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

    $('.sortable_milestone').each(function() {
        var count = $(this).find('ul li.t_item').length;
        var add_block = $(this).find('ul li.new_task_box');
        if ( count > 0 ) {
            $(add_block).hide();
        }
    });
    $('.milestone_options').off('click').click(function() {
        $(this).closest('.sortable_milestone').find('.new_task_box').toggle();
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
		$(this).closest('div').find('.milestone_actions').hide();
        $(this).closest('h4').hide().nextAll('input[name=milestone_name]').show().focus().keyup(function(e) {
            if(e.which == 13) {
				$(this).blur();
			}
		}).blur(function() {
			$(this).closest('div').find('.milestone_actions').show();
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
		var result = confirm("Are you sure you want to delete this task?");
		if (result) {
            confirm('If you delete a column you delete all action items in the column.');
            $(this).closest('.sortable_milestone').remove();
            DoubleScroll(document.getElementById('scrum_tickets'));
            $.post('task_ajax_all.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), field: 'deleted', value: 1 });
        }
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

function send_email(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tasks&id='+task_id+'&from_task=task&type='+type, 'auto', false, false);
}

function send_task_reminder(task) {
       task_id = $(task).parents('span').data('task');
       var type = 'task';
       if(task_id.toString().substring(0,5) == 'BOARD') {
                var type = 'task board';
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=tasks&id='+task_id+'&type='+type, 'auto', false, false);
}

function send_task_alert(task) {
       task_id = $(task).parents('span').data('task');
       if(task_id.toString().substring(0,5) == 'BOARD') {
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_alert.php?tile=tasks&id='+task_id, 'auto', false, false);
}


function send_note(task) {
       task_id = $(task).parents('span').data('task');
       if(task_id.toString().substring(0,5) == 'BOARD') {
               task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, false);
}

function track_time(task) {
    var task_id = $(task).parents('span').data('task');
   if(task_id.toString().substring(0,5) == 'BOARD') {
           task_id = task_id.substring(5);
   }
   overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_timer.php?tile=tasks&id='+task_id, 'auto', false, false);

    //$('.timer_block_'+task_id).toggle();
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
       task_id = $(task).parents('span').data('task');
       var type = 'task';
       if(task_id.toString().substring(0,5) == 'BOARD') {
            var type = 'task_board';
            task_id = task_id.substring(5);
       }
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=tasks&id='+task_id+'&type='+type, 'auto', false, false);
}

function highlight_item(sel) {
    var task_id = $(sel).parents('span').data('task');
    var type = 'task';
    if(task_id.toString().substring(0,5) == 'BOARD') {
        var type = 'task_board';
        task_id = task_id.substring(5);
    }
    $('#color_'+task_id).click();
}

function choose_color(sel) {
	var task_id = $(sel).parents('span').data('task');
    var taskcolor = sel.value;
	var taskcolor = taskcolor.replace("#", "");

    var type = 'task';
    if(task_id.toString().substring(0,5) == 'BOARD') {
        var type = 'task_board';
        task_id = task_id.substring(5);
    }

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=task_highlight&tasklistid="+task_id+'&taskcolor='+taskcolor+'&type='+type,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function setManualFlag(tasklistid, colour, label) {
    window.location.reload();
	//var item = $('.dashboard-item[data-id="'+tasklistid+'"]');
	//item.data('colour',colour);
	//item.css('background-color','#'+colour);
	//item.find('.flag-label').text(label);
}

function flag_item_manual1(task) {
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
			url: '../Tasks_Updated/task_ajax_all.php?fill=taskflagmanual',
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

function flag_item_box(taskid){
	//$('#flag_color_box_'+taskid).show();
	$('#colorpickerbtn_'+taskid)[0].click();
}

function flag_item(task,flag_colour) {
	alert(task); alert(flag_colour);
	//task_id = $(task).parents('span').data('task');
	//task_id = $('#flag_color_box_'+task).next('span').data('task');
	//flag_colour = $('#demo_'+task_id).val();
	task_id = task;
	flag_colour = flag_colour. substring(1, flag_colour. length);
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	$.ajax({
		method: "POST",
		//url: "task_ajax_all.php?fill=taskflag",
		url: "task_ajax_all.php?fill=taskflagcolorbox",
		data: { type: type, id: task_id, new_colour:flag_colour },
		complete: function(result) {
			console.log(result.responseText);
			if(type == 'task') {
				//$(task).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
				$('#'+task_id).css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			} else {
				//$(task).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
				$('#'+task_id).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			}
			//$('#flag_color_box_'+task_id).hide();
		}
	});
}

function task_archive(task) {
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
	if(type=='task board' && confirm("Are you sure you want to archive this task board?")) {
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=delete_board&boardid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				var tab='<?=$_GET['tab']?>';
				window.location.replace("<?= WEBSITE_URL; ?>/Tasks_Updated/index.php?category=All&tab=Summary");
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
                //window.parent.location.href = "<?= WEBSITE_URL; ?>/Tasks_Updated/index.php?category="+task_board_id+"tab=<?= trim($_GET['tab']) ?>";
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

// Add Intake
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
<?php
        echo '<div class="row dashboard_heading">';
            //echo '<div class="col-sm-6"><h3>'. ($title=='Search' ? $title .': '. $term : $title .': '. $board_name['board_name']) .'</h3></div>';


            $task_board = mysqli_fetch_array(mysqli_query($dbc, "SELECT `taskboardid`, `flag_colour`, `task_path_name` , `company_staff_sharing`, `flag_label`, `flag_colour` FROM `task_board` WHERE `taskboardid`='{$_GET['category']}'"));
            //$task_flag = $task_board['flag_colour'];
            if (!empty($taskboardid)) {
                $task_path = get_task_board($dbc, $taskboardid, 'task_path');
            }
            //$path_name = empty($task_board['task_path_name']) ? ($task_path > 0 ? get_project_path_milestone($dbc, $task_path, 'project_path') : 'New Path') : $task_board['task_path_name'];

            $path_name = get_project_path_milestone($dbc, $task_path, 'project_path');

            $businessid = get_sales($dbc, $_GET['category'], 'businessid');
            if($_GET['tab'] != 'sales') {
                $board_name = $board_name['board_name'];
            } else {
                $board_name = get_client($dbc, $businessid);
            }
            ?>
            <div class="col-sm-6"><h3 class="offset-left-5 gap-top"><?php echo $board_name .': '.$path_name; ?>
            <img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).hide();$(this).next('span').show().find('input').focus();" title="Edit"><span class="col-sm-4 pull-right" style="display:none;"><input onblur="savePathName(this.value); $(this).parent().hide().prev().show().prev().text(this.value);" type="text" value="<?php echo $path_name; ?>" class="form-control"></span>

            <?php
            if($_GET['tab'] == 'Company') {
                if(!empty($task_board['company_staff_sharing'])) {
                    $cids_ex = explode(',', $task_board['company_staff_sharing']);
                    $cids_unique = array_unique($cids_ex);
                    $i=0;
                    foreach (array_filter($cids_unique) as $staffcid ) {
                        $i++;
                        if($i>5){
                            //break;
                        }
                        profile_id($dbc, $staffcid);
                    }
                }
            }
            if($task_board['flag_label'] != '') { ?>
            <span class="block-label flag-label-block" style="font-weight: bold; background-color: <?php echo '#'.$task_board['flag_colour']; ?>">Flagged: <?= $task_board['flag_label'] ?></span>
            <?php } ?>
            </h3>
            </div>

            <div class="col-sm-6 text-right">
            <span class="pull-right text-right" data-task="BOARD<?php echo $_GET['category']; ?>">
            <?php
                if ( $url_tab!='Search' && $url_tab!='Summary' && $url_tab!='Reporting' ) {
                    echo '<div class="gap-top gap-right action-icons" style="font-size:19px;">'; ?>
                      <img class="inline-img no-toggle cursor-hand" title="<?= TASK_NOUN ?> Board History" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/task_history.php?label=<?=$label?>&taskboardid=<?=$taskboardid?>','auto',true,true);" src="../img/icons/eyeball.png" />
                      <a href=""><img src="../img/clear-checklist.png" class="inline-img no-toggle cursor-hand" alt="Clear Completed Tasks" title="Clear Completed Tasks" onclick="clearCompleted(this);" /></a>

                    <span title="Flag This!" onclick="flag_item_manual(this); return false;"><img title="Flag This!" src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" onclick="return false;"></span>

                    <span title="Highlight" onclick="highlight_item(this); return false;"><img src="../img/icons/color-wheel.png" class="inline-img no-toggle" title="Highlight" onclick="return false;"></span>

                    <input type="color" class="color_picker" onchange="choose_color(this); return false;" id="color_<?=$taskboardid?>" data-taskid="<?=$taskboardid?>" name="color_<?=$taskboardid?>" style="display:none;" />

                    <span title="Send Email" onclick="send_email(this); return false;"><img src="../img/icons/ROOK-email-icon.png" title="Send Email" class="inline-img no-toggle" onclick="return false;"></span>

                    <span title="Schedule Reminder" onclick="send_task_reminder(this); return false;"><img title="Schedule Reminder" src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;"></span>

                    <?php
                    if ( !empty($_GET['category']) && !empty($_GET['tab']) && $_GET['tab'] != 'sales') {
                        if($_GET['tab'] != 'Company') {
                        ?>
                        <span class="no-toggle cursor-hand" title="Edit <?= TASK_NOUN ?> Board" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_taskboard.php?taskboardid=<?=$_GET['category']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-edit-icon.png" class="inline-img" /></span>
                        <?php } else { ?>
                        <span class="no-toggle cursor-hand" title="Share <?= TASK_NOUN ?> Board" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_taskboard.php?taskboardid=<?=$_GET['category']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-share-icon.png" class="inline-img" /></span>
                        <?php } ?>
                    <?php }
                        if ( !empty($_GET['category']) && !empty($_GET['tab']) && in_array('archive', $quick_actions) && $_GET['tab'] != 'sales') { ?>
                            <span class="no-toggle cursor-hand" title="Archive <?= TASK_NOUN ?> Board" onclick="task_archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/trash-icon-red.png" class="inline-img" /></span><?php
                        }

                        /*
                        if ( $board_name['company_staff_sharing'] ) {
                            $c_ex = explode(',', $board_name['company_staff_sharing']);
                            $c_unique = array_unique($c_ex);
                            foreach ( array_filter($c_unique) as $staffid ) {
                                profile_id($dbc, $staffid);
                            }
                        } else {
                            profile_id($dbc, $board_name['contactid']);
                        }
                        */
                    echo '</div>';
                }
            echo '</span></div>';
        echo '</div>';
    ?>

                <!--<span class="pull-right text-right double-gap-top" style="" data-task="BOARD<?php echo $_GET['category']; ?>">

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
                -->

<div class="standard-dashboard-body-content">
<div class="dashboard-item no-gap-pad">

<div class="container no-padding">
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
            $task_board = mysqli_fetch_array(mysqli_query($dbc, "SELECT `taskboardid`, `flag_colour`, `task_path_name`, `flag_label` FROM `task_board` WHERE `taskboardid`='{$_GET['category']}'"));
            if($task_board['flag_label'] == '') {
                $task_flag = $task_board['flag_colour'];
            }
			if ( !empty($taskboardid) ) {
				$task_path = get_task_board($dbc, $taskboardid, 'task_path');
			}
			$path_name = empty($task_board['task_path_name']) ? ($task_path > 0 ? get_project_path_milestone($dbc, $task_path, 'project_path') : 'New Path') : $task_board['task_path_name']; ?>
            <form name="form_sites" method="post" action="" class="form-inline" role="form" <?php echo ($task_flag == '' ? '' : 'style="background-color: #'.$task_flag.';"'); ?>>
				<!-- <span class="pull-left col-sm-6"><h3>Path: <?= '<span>'.$path_name.'</span>'.($task_path > 0 ? '<img class="inline-img cursor-hand small no-toggle" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).hide();$(this).next(\'span\').show().find(\'input\').focus();" title="Edit"><span class="col-sm-4 pull-right" style="display:none;"><input onblur="savePathName(this.value); $(this).parent().hide().prev().show().prev().text(this.value);" type="text" value="'.$path_name.'" class="form-control"></span>' : '') ?></h3></span> -->
                <!--
                <span class="pull-right double-gap-top" style="cursor: pointer;" data-task="BOARD<?php echo $_GET['category']; ?>">
                    <?php if(in_array('flag', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('alert', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Activate Alerts and Get Notified" onclick="send_alert(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('email', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('reminder', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('attach', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" style="height:2em;"></span><?php } ?>
                    <?php if(in_array('archive', $quick_actions)) { ?><span style="padding: 0.25em 0.5em;" title="Archive Task Board" onclick="task_archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/trash-icon-red.png" style="height:2em;"></span><?php } ?>
                    <br /><input type="text" name="reminder_board_<?php echo $_GET['category']; ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
                </span>
                -->

                <div class="clearfix"></div>
                <input type="file" name="attach_board_<?php echo $_GET['category']; ?>" style="display:none;" />

                <div class="clearfix"></div>

                <div id="scrum_tickets" class="scrum_tickets no-overflow-y" style="padding:0 8px;"><?php
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
							$label = $milestone_row['label'] ?: ''.TASK_TILE;
							if ( $url_tab == 'Private' ) {
								//$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE contactid IN (". $_SESSION['contactid'] .") AND (task_path='$task_path' OR '$task_path' = '') AND (task_milestone_timeline='$cat_tab' OR ('$cat_tab' = '' AND task_milestone_timeline NOT IN ('".implode("','",$each_tab)."'))) AND task_board = '$taskboardid' AND (DATE(`archived_date`) >= (DATE(NOW() - INTERVAL 3 DAY)) OR archived_date IS NULL OR archived_date = '0000-00-00') AND `deleted`=0 ORDER BY task_path ASC, tasklistid DESC");

								//$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tl.contactid IN (".$_SESSION['contactid'].") AND tb.taskboardid='$taskboardid' AND tb.board_security='Private' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");

                                $result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND tb.taskboardid='$taskboardid' AND tb.board_security='Private' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");

							} elseif ( $url_tab == 'Company' ) {
								$result = mysqli_query($dbc, "SELECT tl.*, tb.company_staff_sharing FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tb.taskboardid='$taskboardid' AND tb.board_security='Company' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'Project' ) {
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE tl.contactid IN (".$_SESSION['contactid'].") AND tb.taskboardid='$taskboardid' AND tb.board_security='Project' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'Client' ) {
								//$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND tb.taskboardid='$taskboardid' AND tb.board_security='Client' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 AND (tb.contactid=tl.clientid OR tb.businessid=tl.businessid) ORDER BY tl.task_path ASC, tl.tasklistid DESC");

                                $result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) WHERE (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND tb.taskboardid='$taskboardid' AND tb.board_security='Client' AND tb.company_staff_sharing LIKE '%,".$_SESSION['contactid'].",%' AND tl.task_path='$task_path' AND tl.task_milestone_timeline='$cat_tab' AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							} elseif ( $url_tab == 'sales' ) {
								$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE IFNULL(sales_milestone,'')='{$milestone_row['milestone']}' AND task_milestone_timeline=' ' AND IFNULL(archived_date,'0000-00-00')='0000-00-00' AND deleted=0 AND `salesid`='$taskboardid' ORDER BY tasklistid DESC");
							} else {
								//$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_board='$taskboardid' AND task_milestone_timeline='$cat_tab' AND contactid IN (". $_SESSION['contactid'] .") ORDER BY task_path ASC, tasklistid DESC");
								$result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.contactid IN (". $_SESSION['contactid'] .") AND tb.board_security='$url_tab' ORDER BY tl.task_path ASC, tl.tasklistid DESC");
							}

							if ( empty($cat_tab) && $url_tab == 'Client' ) {
								$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_board='$taskboardid' AND task_milestone_timeline='$cat_tab' AND clientid <> '' AND contactid IN (". $_SESSION['contactid'] .") AND (archived_date IS NULL OR archived_date='0000-00-00') AND deleted=0 ORDER BY task_tododate");
							}

							$checklist_result = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `task_path` = '$task_path' AND `task_board` = '$taskboardid' AND `task_milestone_timeline` = '$cat_tab' AND `deleted` = 0");

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

							echo '<div id="sortable'.$i.'" class="sortable_milestone connectedSortable '.$status.' '.$class_on.' '.($i > 0 ? 'hidden-xs' : '').'" style="padding-top:0;">'; ?>

							<div class="info-block-header">
								<h4 class="pull-left">
                                    <?= '<a href="?category='.$_GET['category'].'&tab='.$_GET['tab'].'&milestone='.$cat_tab.'" class="pull-left">'. $label .'</a>'. $alert ?>
									<img class="no-gap-top milestone_name cursor-hand inline-img pull-left gap-left no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit">
                                <!--<a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_milestones.php?task_board=<?=$taskboardid?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img class="no-margin black-color inline-img pull-right" src="../img/icons/ROOK-add-icon.png" /></a>-->
                                </h4>
                                <div class="milestone_actions pull-right offset-top-5">
									<img class="small no-gap-top milestone_drag cursor-hand inline-img pull-right no-toggle" style="padding-top:2px;" src="../img/icons/drag_handle.png" title="Drag">
									<img class="milestone_rem cursor-hand no-gap-top inline-img pull-right" src="../img/remove.png" style="padding:0 3px;">
									<img class="milestone_add cursor-hand no-gap-top pull-right" src="../img/icons/ROOK-add-icon.png" width="20">
									<input type="hidden" name="sort" value="<?= $milestone_row['sort'] ?>">
                                </div>
                                <div class="clearfix"></div>
                                <?php
                                    if ( empty($task_count) ) {
                                        $task_count = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(tasklistid) AS count FROM tasklist WHERE task_path='$task_path' AND task_milestone_timeline='$cat_tab' AND task_board='$taskboardid' AND `deleted`=0"));
                                        $task_count = $task_count['count'];
                                    }
                                ?>
                                <input type="text" name="milestone_name" data-milestone="<?= $cat_tab ?>" data-id="<?= $milestone_row['id'] ?>" data-table="<?= $milestone_row['table'] ?>" value="<?= $label ?>" style="display:none;" class="form-control">
                                <div class="pull-left small">TASKS: <?= $task_count ?></div>
                                <div class="pull-right" style="margin-top:-6px;"><img src="../img/icons/ROOK-3dot-icon.png" alt="Show/Hide Options" title="Show/Hide Options" class="no-toggle cursor-hand milestone_options" width="20" /></div>
                                <div class="clearfix"></div>
								<!--
								</a>
								-->
								<div class="clearfix"></div>
							</div>
                            <ul class="scrollable_unit no-gap-pad <?php echo $status; ?> overflow-y"><?php
							/* echo '<li class="ui-state-default ui-state-disabled no-sort '.$class_on_2.'">';
							echo $alert.$cat_tab.'<br>'.$timeline[$i].'</li>'; */

							echo '<li class="new_task_box no-sort">
								<input onChange="changeEndAme(this)" name="add_task" placeholder="Quick Add Task" id="add_new_task '.$status.' '.$task_path.' '.$taskboardid.' '.$salesid.'" type="text" class="form-control" /><br /><br />'; ?>

                    <?php if(get_config($dbc, 'task_include_intake') == 1) { ?>
					<a href="" onclick="addIntakeForm(this); return false;" data-milestone="<?= $milestone_row['milestone'] ?>" class="btn brand-btn pull-right">Intake +</a>
                    <?php } ?>
                    <?php if(get_config($dbc, 'task_include_checklists') == 1) {
                    ?>
                    <a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Checklist/edit_checklist.php?edit=NEW&iframe_slider=1&add_to_taskboard=1&task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$task_board?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right">Checklist +</a>
                    <?php } ?>

                    <?php
                    $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

                    if($slider_layout == 'accordion') {
                    ?>

                    <a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?tab=<?=$_GET['tab']?>&task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$task_board?>&salesid=<?=$_GET['category']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right"><?= TASK_NOUN ?> +</a>

                    <?php } else { ?>
                    <a href="../Tasks_Updated/add_task_full_view.php?tab=<?=$_GET['tab']?>&task_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&task_board=<?=$task_board?>&salesid=<?=$_GET['category']?>" class="btn brand-btn pull-right"><?= TASK_NOUN ?> +</a>
                    <?php } ?>

                            </li><?php

							while($row = mysqli_fetch_array( $result )) {
								if ( $row['status']==$status_complete ) {
									$style_strikethrough = 'text-decoration:line-through; filter: gray; -webkit-filter: grayscale(1); filter: grayscale(1);';
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
                                    $bg_color = '';
                                    if($row['flag_label'] == '' && $row['flag_colour'] != '') {
                                        $bg_color = $row['flag_colour'];
                                    }
                                    echo '<li id="'.$row['tasklistid'].'" data-table="tasklist" data-id-field="tasklistid" class="ui-state-default t_item '.$class_on.'" style="margin-top:4px; background-color: #'.$bg_color.';'.($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'">';

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
                                        <?php
                                        if($row['flag_label'] != '') { ?>
                                        <span class="block-label flag-label-block" style="font-weight: bold; background-color: <?php echo '#'.$row['flag_colour']; ?>">Flagged: <?= $row['flag_label'] ?></span>
                                        <?php } ?>
                                        <h4 style="<?= $style_strikethrough ?>">

                                            <input type="checkbox" name="status" value="<?= $row['tasklistid'] ?>" class="form-checkbox no-margin small pull-left" onchange="mark_done(this);" <?= ( $row['status'] == 'Complete' || $row['status'] == 'Done' || $row['status'] == 'Finish') ? 'checked' : '' ?> />
                                            <div class="pull-left gap-left">

                                            <?php
                                            $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

                                            if($slider_layout == 'accordion') {
                                            ?>
                                            <a href="" style="<?= $style_strikethrough ?>" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><?= TASK_NOUN ?> #<?= $row['tasklistid'] ?> </a>
                                            <?php } else { ?>
                                            <a style="<?= $style_strikethrough ?>" href="../Tasks_Updated/add_task_full_view.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>"><?= TASK_NOUN ?> #<?= $row['tasklistid'] ?> </a>
                                            <?php } ?>


                                            </div>
                                            <img src="../img/icons/ROOK-sync-icon.png" class="inline-img no-toggle pull-right sync_visible_icon small" title="Synced to Customer Support Scrum Board" style="<?= $row['is_sync'] > 0 ? '' : 'display:none;' ?>">
                                        </h4>
                                    </div>
                                    
                                    <span style="<?= $style_strikethrough ?>" class="pull-right action-icons offset-top-5 t_drag" data-task="<?= $row['tasklistid'] ?>">

                                        <img class="drag_handle pull-right inline-img no-toggle" src="../img/icons/drag_handle.png" title="Drag" />
                                    </span>
                                    <div class="pull-right t_staff" style="<?= $style_strikethrough ?>"><?php

                                        /*if ( $row['company_staff_sharing'] ) {
                                            $c_ex = explode(',', $row['company_staff_sharing']);
                                            $c_unique = array_unique($c_ex);
                                            foreach (array_filter($c_unique) as $staffid ) {
                                                profile_id($dbc, $staffid);
                                            }
                                        } else {
                                            */
                                        	if(!empty($row['contactid'])){
                                        		$cids_ex = explode(',', $row['contactid']);
	                                            $cids_unique = array_unique($cids_ex);
	                                            $i=0;
	                                            foreach (array_filter($cids_unique) as $staffcid ) {
	                                            	$i++;
	                                            	if($i>5){
	                                            		break;
	                                            	}
	                                                profile_id($dbc, $staffcid);
	                                            }
                                        	}else{
                                            	profile_id($dbc, $row['contactid']);
                                        	}
                                        // }
                                         ?>
                                    </div>

                                    <div class="clearfix"></div>
                                    <h4><span style="<?= $style_strikethrough ?>"><?= $row['heading']; ?></span></h4>

                                    <?php

                                    echo '<span class="pull-right action-icons double-gap-bottom full-width" data-task="'.$row['tasklistid'].'">';

                                        $mobile_url_tab = trim($_GET['tab']);
                                        if ( $url_tab=='Project' || $mobile_url_tab=='Project' ) { ?>
                                            <span style="display:inline-block; text-align:center; width:11%;"><a href="../Project/projects.php?edit=<?= $row['projectid'] ?>" title="View Project" style="background-color:#fff; border:1px solid #3ac4f2; border-radius:50%; color:#3ac4f2 !important; display:inline-block; height:1.5em; width:1.5em;">?</a></span><?php
                                        }
                                        echo in_array('flag_manual', $quick_actions) ? '<span title="Flag This!" onclick="flag_item_manual(this); return false;"><img title="Flag This!" src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';

                                        echo in_array('flag', $quick_actions) ? '<span title="Highlight" onclick="highlight_item(this); return false;"><img src="../img/icons/color-wheel.png" class="inline-img no-toggle" title="Highlight" onclick="return false;"></span>' : '';

                                        echo '<input type="color" class="color_picker" onchange="choose_color(this); return false;" id="color_'.$row['tasklistid'].'"" data-taskid="'.$row['tasklistid'].'" name="color_'.$row['tasklistid'].'" style="display:none;" value="#f6b73c" />';


                                        echo $row['projectid'] > 0 && in_array('sync', $quick_actions) ? '<span title="Sync to External Path" onclick="sync_task(this); return false;"><img title="Sync to External Path" src="../img/icons/ROOK-sync-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                                        echo in_array('alert', $quick_actions) ? '<span title="Send Alert" onclick="send_task_alert(this); return false;"><img src="../img/icons/ROOK-alert-icon.png" title="Send Alert" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                                        echo in_array('email', $quick_actions) ? '<span title="Send Email" onclick="send_email(this); return false;"><img src="../img/icons/ROOK-email-icon.png" title="Send Email" class="inline-img no-toggle" onclick="return false;"></span>' : '';

                                        echo in_array('reminder', $quick_actions) ? '<span title="Schedule Reminder" onclick="send_task_reminder(this); return false;"><img title="Schedule Reminder" src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';

                                        echo in_array('attach', $quick_actions) ? '<span title="Attach File(s)" onclick="attach_file(this); return false;"><img src="../img/icons/ROOK-attachment-icon.png" title="Attach File(s)" class="inline-img no-toggle" onclick="return false;"></span>' : '';

                                        echo in_array('reply', $quick_actions) ? '<span title="Add Note" onclick="send_note(this); return false;"><img src="../img/icons/ROOK-reply-icon.png" title="Add Note" class="inline-img no-toggle" onclick="return false;"></span>' : '';

                                        echo in_array('time', $quick_actions) ? '<span title="Add Time" onclick="quick_add_time(this); return false;"><img src="../img/icons/ROOK-timer-icon.png" title="Add Time" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                                        echo in_array('timer', $quick_actions) ? '<span title="Track Time" onclick="track_time(this); return false;"><img src="../img/icons/ROOK-timer2-icon.png" title="Track Time" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                                        echo in_array('scrum_sync', $quick_actions) ? '<span title="Sync to Scrum Board" onclick="sync(this); return false;" data-sync="'.($row['is_sync'] > 0 ? 0 : 1).'"><img src="../img/icons/ROOK-sync-icon.png" title="'.($row['is_sync'] > 0 ? 'Synced To Customer' : 'Not Synced To Customer').' Scrum Board" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                                        ?>

									    <img class="inline-img no-toggle" title="History" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/task_history.php?label=<?=$label?>&taskboardid=<?=$taskboardid?>&tasklistid=<?=$row['tasklistid']?>','auto',true,true);" src="../img/icons/eyeball.png">

                                        <?php
                                        echo in_array('archive', $quick_actions) ? '<span title="Archive Task" onclick="task_archive(this); return false;"><img src="../img/icons/trash-icon-red.png" title="Archive Task" class="inline-img no-toggle" onclick="return false;"></span>' : '';

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

                                    <?php

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
                                            $item['task_tododate'] = $row['task_tododate'];
                                            $item['tasklistid'] = $row['tasklistid'];
                                            $item['contactid'] = $row['contactid'];
                                            $item['status'] = $row['status'];
                                            include('../Tasks_Updated/dashboard_fields.php'); ?>

                                    <?php

                                    echo '<div class="clearfix gap-top"></div>';
                                    $documents = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `document` FROM `task_document` WHERE `tasklistid`='{$row['tasklistid']}' ORDER BY `taskdocid` DESC");
                                    if ( $documents->num_rows > 0 ) { ?>
                                        <div class="form-group clearfix full-width">
                                            <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
                                                while ( $row_doc=mysqli_fetch_assoc($documents) ) { ?>
                                                    <div class="note_block row" style="padding:5px 10px 0 10px;">
                                                        <div class="col-xs-2 col-sm-1"><?= profile_id($dbc, $row_doc['created_by']); ?></div>
                                                        <div class="col-xs-10 col-sm-11" style="<?= $style_strikethrough ?>">
                                                            <div><a target="_blank" href="../Tasks_Updated/download/<?= $row_doc['document'] ?>"><?= $row_doc['document'] ?></a></div>
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
                                        <div class="form-group clearfix" style="max-width:50em; word-break:break-word;">
                                            <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
                                                $odd_even = 0;
                                                while ( $row_comment=mysqli_fetch_assoc($comments) ) {
                                                    $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                                                    <div class="note_block row <?= $bg_class ?>">
                                                        <div class="col-xs-2 col-sm-1"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
                                                        <div class="col-xs-10 col-sm-11" style="<?= $style_strikethrough ?>">
                                                            <div><?= html_entity_decode($row_comment['comment']); ?></div>
                                                            <!-- <div><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div> -->
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

							if(is_array($task_board)) {
								$task_board = $task_board['taskboardid'];
							}
                            $salesid = 0;
                            if(!empty($_GET['tab']) && $_GET['tab'] == 'sales') {
                                $salesid = $_GET['category'];
                            } ?>

							<!-- <li class="no-sort"> --><?php

							if(get_config($dbc, 'task_include_checklists') == 1) {
                                while($row = mysqli_fetch_array( $checklist_result )) {
									$colour = $row['flag_colour'];
									if($colour == 'FFFFFF' || $colour == '') {
										$colour = '';
									}
									echo '<li style="margin-top: 4px;  '.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'" data-id-field="checklistid" id="'.$row['checklistid'].'" data-table="checklist" class="ui-state-default">';
									echo '<input type="file" name="attach_checklist_board_'.$row['checklistid'].'" style="display:none;" />';
									echo '<div class="row pull-left"><h4><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/checklist.php?view='.$row['checklistid'].'&iframe_slider=1\'); return false;">'.$row['checklist_name'].'</a></h4></div><span class="pull-right action-icons offset-top-5" data-checklist="'.$row['checklistid'].'"><img class="drag_handle pull-right inline-img no-toggle" title="Drag" src="../img/icons/drag_handle.png" /></span><div class="clearfix"></div>';
									echo '<span class="pull-right action-icons" style="width: 100%;" data-checklist="'.$row['checklistid'].'" data-taskboard="'.$task_board.'">'.
										'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/edit_checklist.php?edit='.$row['checklistid'].'\'); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit"></a>'.
										(in_array('flag_manual',$quick_actions) || in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" onclick="checklist_flag(this); return false;" class="inline-img no-toggle flag-icon" title="Flag This!">' : '').
										(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" onclick="checklist_email(this); return false;" class="inline-img no-toggle email-icon" title="Send Email">' : '').
										(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" onclick="checklist_reminder(this); return false;" class="inline-img no-toggle reminder-icon" title="Schedule Reminder">' : '').
										(in_array('attach', $quick_actions) ? '<img src="../img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle attach-icon" onclick="checklist_attach_file(this); return false;" title="Attach File(s)">' : '').
										(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" onclick="checklist_archive(this); return false;" class="inline-img no-toggle archive-icon" title="Archive">' : '');
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

							echo '</ul>';
							echo '</div><!-- #sortable -->';
							$i++;
						}
					} else {
						echo "<h3>No ".TASK_TILE." Found</h3>";
					} ?>
                </div><!-- #scrum_tickets -->
            </form><?php
        //} ?>
	</div><!-- .hide_on_iframe -->
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
</div><!-- .container -->

</div>
</div>
