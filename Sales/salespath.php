<div class="form-horizontal">
<?php $quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$task_colours = explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `flag_colours` FROM task_dashboard"))['flag_colours']);
$flag_colours = explode(',', get_config($dbc, "ticket_colour_flags"));
$show_tasks = $tasks_only ? true : tile_enabled($dbc, 'tasks');
$show_forms = $tasks_only ? false : tile_enabled($dbc, 'intake');
$show_checklists = $tasks_only ? false : tile_enabled($dbc, 'checklist');
$flag_labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
$sales_lead = $dbc->query("SELECT * FROM `sales` WHERE `salesid`='$salesid'")->fetch_assoc();

if(!empty($sales_lead['flag_label'])) {
    $flag_colour = $sales_lead['flag_colour'];
    $flag_label = $sales_lead['flag_label'];
} else if(!empty($sales_lead['flag_colour'])) {
    $flag_colour = $sales_lead['flag_colour'];
    $flag_label = $flag_labels[array_search($sales_lead['flag_colour'], $flag_colours)];
} ?>
<script>
$(document).ready(function() {
	milestoneActions();
    sortableItems();
    DoubleScroll(document.getElementById('scrum_tickets'));
});

function changeEndAme(sel) {
	$(this).focus();

	$(this).prop("disabled",false);
	var stage = sel.value;
	var typeId = sel.id;

	var tasklistid = typeId.split(' ');

	var status = tasklistid[1];
	var task_path = tasklistid[2];
	var taskboardid = tasklistid[3];

	var stage = stage.replace(" ", "FFMSPACE");
	var stage = stage.replace("&", "FFMEND");
	var stage = stage.replace("#", "FFMHASH");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Tasks_Updated/task_ajax_all.php?fill=add_task&sales_milestone="+$(sel).data('milestone')+"&heading="+stage+"&salesid=<?=$_GET['id']?>",
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
	function DoubleScroll(element) {
		$('.double_scroll_div').remove();
		var scrollbar= document.createElement('div');
		scrollbar.className = 'double_scroll_div';
		scrollbar.appendChild(document.createElement('div'));
		scrollbar.style.overflow= 'auto';
		scrollbar.style.overflowY= 'hidden';
		scrollbar.style.width= '';
		scrollbar.firstChild.style.width= element.scrollWidth+'px';
		scrollbar.firstChild.style.paddingTop= '1px';
		scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
		scrollbar.onscroll= function() {
			element.scrollLeft= scrollbar.scrollLeft;
		};
		element.onscroll= function() {
			scrollbar.scrollLeft= element.scrollLeft;
		};
		element.parentNode.insertBefore(scrollbar, element);
        $('.double_scroll_div').innerHeight($('.double_scroll_div').outerHeight() - $('.double_scroll_div:visible').get(0).clientHeight);
        $('.scrum_tickets').outerHeight($('.standard-body-content').innerHeight() - $('.double_scroll_div').innerHeight() - 2);
	}
	function sortableItems() {
		$('.sortable_milestone').sortable({
			handle: '.drag_handle',
			items: 'li:not(.no-sort)',
			connectWith: '.sortable_milestone',
			stop: function (e, block) {
				var id = block.item.prop('id');
				var id_field = block.item.data('id-field');
				var table = block.item.data('table');
				var milestone = block.item.closest('.sortable_milestone').find('[name="milestone_name"]').data('milestone');
				$.ajax({
					url: '../Sales/sales_ajax_all.php?fill=updateSalesMilestone',
					method: 'POST',
					data: { table: table, id_field: id_field, id: id, milestone: milestone },
					success: function(response) {
						console.log(response);
					}
				});
			}
		});
	}
	function getMilestoneId(block) {
		if(!($(block).find('[data-id]').data('id') > 0)) {
			$.ajax({
				url: '../Sales/sales_ajax_all.php?action=milestone_edit',
				method: 'POST',
				data: { salesid: '<?= $_GET['id'] ?>', id: 0, field: 'sort', value: $('.info-block-header [name=sort]').last().val(), table: $(block).find('.info-block-header [name=milestone_name]').data('table') },
				async: false,
				success: function(response) {
					$(block).find('.info-block-header input[name=milestone_name]').data('id',response);
					var classes = $(block).attr('class').split(' ');
					classes[2] = 'milestone.'+response;
					$(block).attr('class',classes.join(' '));
				}
			});
		}
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
		getMilestoneId($(this).closest('.sortable_milestone'));
		$(this).closest('h4').hide().nextAll('input[name=milestone_name]').show().focus().keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			}
		}).blur(function() {
			$(this).hide().prevAll('h4').show().find('a,span').first().text(this.value);
				$.post('../Sales/sales_ajax_all.php?action=milestone_edit', { id: $(this).data('id'), table: $(this).data('table'), field: 'label', value: this.value });
		});
	});
	$('.milestone_add').off('click').click(function() {
		var list = $(this).closest('.sortable_milestone');
		var clone = list.clone();
		clone.find('.ui-state-default').remove();
		clone.find('.info-block-header h4 a').text('New Milestone');
		clone.find('.info-block-header input[name=milestone_name]').val('');
		clone.find('.info-block-header [name=sort]').val('');
		$.post('../Sales/sales_ajax_all.php?action=milestone_edit', { salesid: '<?= $_GET['id'] ?>', id: 0, field: 'sort', value: list.find('.info-block-header [name=sort]').val(), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table') }, function(response) {
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
		getMilestoneId($(this).closest('.sortable_milestone'));
		$(this).closest('.sortable_milestone').remove();
		DoubleScroll(document.getElementById('scrum_tickets'));
			$.post('../Sales/sales_ajax_all.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), field: 'deleted', value: 1 }	);
	});
	$('.info-block-header [name=sort]').off('change').change(function() {
		getMilestoneId($(this).closest('.sortable_milestone'));
			$.post('../Sales/sales_ajax_all.php?action=milestone_edit', { id: $(this).closest('.info-block-header').find('[name=milestone_name]').data('id'), table: $(this).closest('.info-block-header').find('[name=milestone_name]').data('table'), field: 'sort', value: this.value });
	});
}

function task_sync_task(task) {
	var item = $(task).parents('li');
	item.find('.assign_milestone').show().find('select').off('change').change(function() {
		item.find('.assign_milestone').hide();
		$.ajax({
			url: '../Tasks_Updated/task_ajax_all.php?fill=taskexternal',
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

function task_send_alert(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	choose_user('alert', type, task_id);
}

function task_send_email(task) {
	salesid = '<?= $_GET['id'] ?>';
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=sales_task&id='+task_id+'&type='+type+'&salesid='+salesid, 'auto', false, true);
}

function task_send_reminder(task) {
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

function task_send_reply(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
    overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tasks&id='+task_id, 'auto', false, true);

    /* Function before notes slider
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
				url: '../Tasks/task_ajax_all.php?fill=taskreply',
				data: { taskid: task_id, reply: save_reply },
				complete: function(result) {
                    console.log(result.responseText);
                    //$('.updates_'+task_id).append(result);
                    window.location.reload();
                }
			})
		}
	}); */
}

function task_quick_add_time(task) {
	task_id = $(task).parents('span').data('task');
	$('[name=task_time_'+task_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: '../Tasks_Updated/task_ajax_all.php?fill=task_quick_time',
				data: { id: task_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); window.location.reload(); }
			});
            $.ajax({
				method: 'POST',
				url: '../Tasks_Updated/task_ajax_all.php?fill=taskreply',
				data: { taskid: task_id, reply: 'Time added '+time+':00' },
				complete: function(result) { console.log(result.responseText); window.location.reload(); }
			});
		}
	});
	$('[name=task_time_'+task_id+']').timepicker('show');
}

function task_track_time(task) {
    var task_id = $(task).parents('span').data('task');
    $('.timer_block_'+task_id).toggle();
}

function task_attach_file(task) {
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
			url: "../Tasks_Updated/task_ajax_all.php?fill=task_upload&type="+type+"&id="+task_id,
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

function task_manual_flag_item(task) {
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

function task_flag_item(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	$.ajax({
		method: "POST",
		url: "../Tasks_Updated/task_ajax_all.php?fill=taskflag",
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
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	if(type == 'task' && confirm("Are you sure you want to archive this task?")) {
		$.ajax({
			type: "GET",
			url: "../Tasks_Updated/task_ajax_all.php?fill=delete_task&taskid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				window.location.reload();
				console.log(response.responseText);
			}
		});
	}
	else if(confirm("Are you sure you want to archive this task board?")) {
		$.ajax({
			type: "GET",
			url: "../Tasks_Updated/task_ajax_all.php?fill=delete_board&boardid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				var tab='<?=$_GET['tab']?>';
				window.location = "<?= WEBSITE_URL; ?>/Tasks_Updated/index.php?category=My&tab=My";
			}
		});
	}
}

function task_mark_done(sel) {
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
            console.log(response);
            window.location.reload();
        }
    });
}

//Intake functions
function intake_flag(intake) {
	intakeid = $(intake).closest('span').data('intake');
	$.ajax({
		method: "POST",
		url: "sales_ajax_all.php?fill=intakeFlagItem",
		data: { id: intakeid },
		complete: function(result) {
			$(intake).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}

function intake_manual_flag(intake) {
	var item = $(intake).closest('li');
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
			url: '../Intake/intake_ajax_all.php?fill=intakeflagmanual',
			method: 'POST',
			data: {
				value: item.find('[name=colour]').val(),
				label: item.find('[name=label]').val(),
				start: item.find('[name=flag_start]').val(),
				end: item.find('[name=flag_end]').val(),
				id: item.find('[data-intake]').data('intake')
			}
		});
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		item.data('colour',item.find('[name=colour]').val());
		item.css('background-color','#'+item.find('[name=colour]').val());
		item.find('.flag-label').text(item.find('[name=label]').val());
		return false;
	});
}

function intake_email(intake) {
	salesid = '<?= $_GET['id'] ?>';
	intakeid = $(intake).closest('span').data('intake');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=sales_intake&intakeid='+intakeid+'&salesid='+salesid, 'auto', false, true);
}

function intake_reminder(intake) {
	salesid = '<?= $_GET['id'] ?>';
	intakeid = $(intake).closest('span').data('intake');
	var item = $(intake).closest('li');
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
					url: 'sales_ajax_all.php?fill=intakeReminder',
					data: {
						salesid: salesid,
						id: intakeid,
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

function intake_archive(intake) {
	intakeid = $(intake).closest('span').data('intake');
	if(confirm('Are you sure you want to archive this Intake Form?')) {
		$.ajax({
			method: 'POST',
			url: 'sales_ajax_all.php?fill=intakeArchive',
			data: {
				id: intakeid
			},
			success: function(result) {
				console.log(result);
				$(intake).closest('li').remove();
			}
		});
	}
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

//Checklist functions
function checklist_flag(checklist) {
	checklistid = $(checklist).closest('span').data('checklist');
	$.ajax({
		method: "POST",
		url: "sales_ajax_all.php?fill=checklistFlagItem",
		data: { id: checklistid },
		complete: function(result) {
			$(checklist).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}

function checklist_email(checklist) {
	salesid = '<?= $_GET['id'] ?>';
	checklistid = $(checklist).closest('span').data('checklist');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=sales_checklist&checklistid='+checklistid+'&salesid='+salesid, 'auto', false, true);
}

function checklist_reminder(checklist) {
	salesid = '<?= $_GET['id'] ?>';
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
					url: 'sales_ajax_all.php?fill=checklistReminder',
					data: {
						salesid: salesid,
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
			url: 'sales_ajax_all.php?fill=checklistArchive',
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

<?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0")); ?>
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
<?php if($_GET['p'] == 'salespath') { ?>
<div id="sales_path_div" class="main-screen-white standard-body" style="padding-left: 0; padding-right: 0; border: none;">
    <div class="standard-body-title" data-id="<?= $salesid ?>" style="<?= empty($flag_colour) ? '' : 'background-color:#'.$flag_colour.';' ?>position:absolute;z-index:1;width:100%;">
        <h3><?= SALES_NOUN.' #'.$salesid ?> Task Path: <?= get_contact($dbc, $sales_lead['contactid'], 'name_company') ?>
            <div class="pull-right"><?php include('quick_actions.php'); ?></div>
            <img class="inline-img pull-right cursor-hand no-toggle" title="Add Path Template" src="../img/project-path.png" onclick="overlayIFrameSlider('select_path.php?id=<?= $salesid ?>','auto',true,false);">
            <span class="flag-label" data-colour="<?= $flag_colour ?>"><?= $flag_label ?></span>
        </h3>
    </div>
    <div class="standard-body-content">
    <?php } ?>
        <div class="double_scroll_div" style="overflow-x: auto; overflow-y: hidden;"><div style="width: 2240px; padding-top: 1px;">&nbsp;</div></div>
            <div id="scrum_tickets" class="scrum_tickets" style="padding:0;">
                <?php $path = get_field_value('sales_path', 'sales', 'salesid', $salesid);

                $tabs = get_field_value('milestone timeline', 'sales_path', 'pathid', $path);
                $each_tab = explode('#*#', $tabs['milestone']);
                $timeline = explode('#*#', $tabs['timeline']);
                $prior_sort = 0;
                foreach($each_tab as $i => $milestone) {
                    $milestone_rows = $dbc->query("SELECT `sort` FROM `sales_path_custom_milestones` WHERE `salesid`='$salesid' AND `salesid` > 0 AND `milestone`='$milestone'");
                    if($milestone_rows->num_rows > 0) {
                        $prior_sort = $milestone_rows->fetch_assoc()['sort'];
                    } else {
                        $dbc->query("INSERT INTO `sales_path_custom_milestones` (`salesid`,`milestone`,`label`,`sort`) VALUES ('$salesid','$milestone','$milestone','$prior_sort')");
                    }
                }
                $milestoneid = ($_GET['milestone'] > 0 ? $_GET['milestone'] : 0);
                $milestones = $dbc->query("SELECT `id`, `milestone`, `label`, `sort`, 'sales_path_custom_milestones' `table` FROM `sales_path_custom_milestones` WHERE `deleted`=0 AND `salesid`='$salesid' AND `salesid` > 0 AND '$milestoneid' IN (`id`,0) AND `milestone` != '' UNION SELECT 0, '', '', 9999999, 'sales_path_custom_milestones' `table` ORDER BY `sort`, `id`");
                while($milestone_row = $milestones->fetch_assoc()) {
                    $cat_tab = $milestone_row['milestone'];
                    $label = $milestone_row['label'] ?: 'Tasks';
                    $task_result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE IFNULL(sales_milestone,'')='$cat_tab' AND IFNULL(archived_date,'0000-00-00')='0000-00-00' AND deleted=0 AND `salesid`='$salesid' AND `salesid` > 0 ORDER BY tasklistid DESC");
                    $task_count = mysqli_num_rows($task_result) ?: 0;
                    $form_result = mysqli_query($dbc, "SELECT * FROM intake WHERE IFNULL(sales_milestone,'')='$cat_tab' AND deleted=0 AND ((`contactid` IN (SELECT `contactid` FROM `sales` WHERE `salesid`='$salesid' AND `salesid` > 0) AND `contactid` > 0) || (`salesid`='$salesid' AND `salesid` > 0)) ORDER BY received_date DESC");
                    $form_count = mysqli_num_rows($form_result) ?: 0;
                    $checklist_result = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE IFNULL(sales_milestone,'')='$cat_tab' AND deleted=0 AND `salesid`='$salesid' AND `salesid` > 0");
                    $checklist_count = mysqli_num_rows($checklist_result) ?: 0;

                    $status = str_replace("#","FFMHASH",str_replace(" ","FFMSPACE",str_replace("&","FFMEND",$cat_tab)));

                    echo '<ul id="sortable'.$i.'" class="sortable_milestone connectedSortable '.($milestoneid > 0 ? 'full-width' : '').' '.$status.' '.($i > 0 ? 'hidden-xs' : '').'" style="padding-top:0;">'; ?>

							<div class="info-block-header" data-table="sales_path_custom_milestones">
							<h4><?= $milestoneid > 0 ? '<span>'.$label.'</span>'.$alert : '<a href="?p=salespath&id='.$_GET['id'].'&milestone='.$milestone_row['id'].'">'. $label .'</a>'. $alert ?>
								<img class="small no-gap-top milestone_name cursor-hand inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit">
								<img class="small no-gap-top milestone_drag cursor-hand inline-img pull-right no-toggle" src="../img/icons/drag_handle.png" title="Drag">
								<img class="small milestone_rem cursor-hand no-gap-top inline-img pull-right" src="../img/remove.png">
								<img class="small milestone_add cursor-hand no-gap-top inline-img pull-right" src="../img/icons/ROOK-add-icon.png">
								<input type="hidden" name="sort" value="<?= $milestone_row['sort'] ?>"></h4>
								<input type="text" name="milestone_name" data-milestone="<?= $cat_tab ?>" data-id="<?= $milestone_row['id'] ?>" data-table="<?= $milestone_row['table'] ?>" value="<?= $label ?>" style="display:none;" class="form-control">
								<div class="small">
									<?php if($show_tasks) { ?> TASKS: <?= $task_count ?><?php } ?><?php if($show_forms && strpos($value_config, ',Sales Lead Path Intake,') !== FALSE) { ?> INTAKE: <?= $form_count ?><?php } ?><?php if($show_checklists && strpos($value_config, ',Sales Lead Path Checklists,') !== FALSE) { ?> CHECKLISTS: <?= $checklist_count ?><?php } ?>
								</div>

								<div class="clearfix"></div>
							<div class="clearfix"></div>
						</div><?php

						echo '<li class="new_task_box no-sort"><input onChange="changeEndAme(this)" data-milestone="'.$cat_tab.'" name="add_task" placeholder="Quick Add" id="add_new_task '.$status.' '.$task_path.' '.$taskboardid.'" type="text" class="form-control" style="max-width:96%;" /></li>';

                    while($row = mysqli_fetch_array( $task_result )) {
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
                            echo '<li id="'.$row['tasklistid'].'" data-id-field="tasklistid" data-table="tasklist" class="ui-state-default '.$class_on.'" style="'.($row['flag_colour'] == '' ? '' : 'background-color: '.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'">';

                        $businessid = $url_tab=='Business' ? $row['businessid'] : '';
                        $clientid = $url_tab=='Client' ? $row['clientid'] : '';

                        $past = 0;

                        $date = new DateTime($row['task_tododate']);
                        $now = new DateTime();

                        if($date < $now && $row['status'] != $status_complete) {
                            $past = 1;
                        } ?>
                        <div class="row">

                            <h4 style="<?= $style_strikethrough ?>"><input type="checkbox" name="status" value="<?= $row['tasklistid'] ?>" class="form-checkbox no-margin pull-left" onchange="mark_done(this);" <?= ( $row['status'] == $status_complete ) ? 'checked' : '' ?> />
                                <div class="col-sm-5">
                                <a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">
                                	Task #<?= $row['tasklistid'] ?>
                                </a><br>
                                <?= ($url_tab=='Business') ? get_contact($dbc, $businessid, 'name') . ': ' : '' ?><?= ($url_tab=='Client') ? get_contact($dbc, $clientid) . ': ' : '' ?><?=limit_text($row['heading'], 5 )?>
                                </div>
                                <div>
                        <?php echo '<img class="drag_handle pull-right inline-img no-toggle" src="../img/icons/drag_handle.png" title="Drag" />';
                        echo '<span class="pull-right small">';
                        if ( $row['company_staff_sharing'] ) {
                            foreach ( array_filter(explode(',', $row['company_staff_sharing'])) as $staffid ) {
                                profile_id($dbc, $staffid);
                            }
                        } else {
                            profile_id($dbc, $row['contactid']);
                        }
                        echo '</span></div></h4></span></div>';

                        echo '<span class="pull-right action-icons" style="width: 100%;" data-task="'.$row['tasklistid'].'">';
                            $mobile_url_tab = trim($_GET['tab']);
                            if (in_array('edit', $quick_actions)) { ?>
                                <span title="Edit Task" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?=WEBSITE_URL?>/img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" onclick="return false;" title="Edit"></span><?php
                            }
                            echo in_array('flag_manual', $quick_actions) ? '<span onclick="task_manual_flag_item(this); return false;"><img src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" onclick="return false;" title="Flag This!"></span>' : '';
                            echo !in_array('flag_manual', $quick_actions) && in_array('flag', $quick_actions) ? '<span onclick="task_flag_item(this); return false;"><img src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" onclick="return false;" title="Flag This!"></span>' : '';
                            echo $row['projectid'] > 0 && in_array('sync', $quick_actions) ? '<span onclick="task_sync_task(this); return false;"><img src="../img/icons/ROOK-sync-icon.png" class="inline-img no-toggle" onclick="return false;" title="Sync to External Path"></span>' : '';
                            echo in_array('alert', $quick_actions) ? '<span onclick="task_send_alert(this); return false;"><img src="../img/icons/ROOK-alert-icon.png" class="inline-img no-toggle" onclick="return false;" title="Send Alert"></span>' : '';
                            echo in_array('email', $quick_actions) ? '<span onclick="task_send_email(this); return false;"><img src="../img/icons/ROOK-email-icon.png" class="inline-img no-toggle" onclick="return false;" title="Send Email"></span>' : '';
                            echo in_array('reminder', $quick_actions) ? '<span onclick="task_send_reminder(this); return false;"><img src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;" title="Schedule Reminder"></span>' : '';
                            echo in_array('attach', $quick_actions) ? '<span onclick="task_attach_file(this); return false;"><img src="../img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle" onclick="return false;" title="Attach Files"></span>' : '';
                            echo in_array('reply', $quick_actions) ? '<span onclick="task_send_reply(this); return false;"><img src="../img/icons/ROOK-reply-icon.png" class="inline-img no-toggle" onclick="return false;" title="Add Note"></span>' : '';
                            echo in_array('time', $quick_actions) ? '<span onclick="task_quick_add_time(this); return false;"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img no-toggle" onclick="return false;" title="Add Time"></span>' : '';
                            echo in_array('time', $quick_actions) ? '<span onclick="task_track_time(this); return false;"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img no-toggle" onclick="return false;" title="Track Time"></span>' : '';
                            echo in_array('archive', $quick_actions) ? '<span onclick="task_archive(this); return false;"><img src="../img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" onclick="return false;" title="Archive"></span>' : '';
                        echo '</span>';
                        echo '<div class="clearfix"></div>';
                        echo '<div class="clearfix"></div>';
                        $documents = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `document` FROM `task_document` WHERE `tasklistid`='{$row['tasklistid']}' ORDER BY `taskdocid` DESC");
                        if ( $documents->num_rows > 0 ) { ?>
                            <div class="form-group clearfix">
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
                        if ( $comments->num_rows > 0 ) {
                            $odd_even = 0; ?>
                            <div class="form-group clearfix">
                                <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
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
                        echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
                        echo '<input type="text" name="task_time_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />'; ?>
                        <div class="timer_block_<?= $row['tasklistid'] ?>" style="display:none; margin-top:2.2em;">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Timer:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="timer_<?= $row['tasklistid'] ?>" id="timer_value" style="float:left;" class="form-control timer" placeholder="0 sec" />&nbsp;&nbsp;
                                    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                                    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $row['tasklistid'] ?>">Stop</a>
                                </div>
                            </div>
                        </div><?php
                        echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
                        echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
                        echo '<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect" data-id="'.$row['tasklistid'].'"><option value="unassign">Unassigned</option>';
                        foreach(array_unique(array_filter(explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`project_path_milestone`.`milestone` SEPARATOR '#*#') `milestones` FROM `project` LEFT JOIN `project_path_milestone` ON CONCAT(',',`project`.`external_path`,',') LIKE CONCAT('%,',`project_path_milestone`.`project_path_milestone`,',%') WHERE `projectid`='".$row['projectid']."'"))['milestones']))) as $external_milestone) { ?>
                                <option <?= $external_milestone == $row['external'] ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
                        <?php }
                        echo '</select></div><div class="clearfix"></div>';

                        echo '</li>';
                    }
                    while($row = mysqli_fetch_array( $ticket_result )) {
                        $border_colour = '';
                        foreach(explode(',',$row['contactid'].','.$row['alerts_enabled']) as $userid) {
                            if($userid > 0 && $border_colour == '') {
                                $border_colour = get_contact($dbc, $userid, 'calendar_color');
                            }
                        }
                            echo '<li id="'.$row['ticketid'].' data-id-field="ticketid" data-table="tickets" class="ui-state-default '.$class_on.'" style="'.($row['flag_colour'] == '' ? '' : 'background-color: '.$row['flag_colour'].';').($border_colour == '' ? '' : 'border-style:solid;border-color: '.$border_colour.';border-width:3px;').'">';

                        $businessid = $url_tab=='Business' ? $row['businessid'] : '';
                        $clientid = $url_tab=='Client' ? $row['clientid'] : '';

                        $past = 0;

                        $date = new DateTime($row['to_do_date']);
                        $now = new DateTime();

                        if($date < $now && $row['status'] != $status_complete) {
                            $past = 1;
                        }

							echo '<span class="pull-right action-icons" style="width: 100%;" data-ticket="'.$row['ticketid'].'">';
								$mobile_url_tab = trim($_GET['tab']);
								if (in_array('edit', $quick_actions)) { ?>
									<a title="Edit <?= TICKET_NOUN ?>" href="<?=WEBSITE_URL?>/Ticket/index.php?type=<?=$row['status']?>&ticketid=<?=$row['ticketid']?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="<?=WEBSITE_URL?>/img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" onclick="return false;" title="Edit"></a><?php
								}
								echo in_array('flag', $quick_actions) ? '<span onclick="ticket_flag_item(this); return false;"><img src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" title="Flag This!" onclick="return false;"></span>' : '';
								echo in_array('alert', $quick_actions) ? '<span onclick="ticket_send_alert(this); return false;"><img src="../img/icons/ROOK-alert-icon.png" class="inline-img no-toggle" title="Send Alert" onclick="return false;"></span>' : '';
								echo in_array('email', $quick_actions) ? '<span onclick="ticket_send_email(this); return false;"><img src="../img/icons/ROOK-email-icon.png" class="inline-img no-toggle" title="Send Email" onclick="return false;"></span>' : '';
								echo in_array('attach', $quick_actions) ? '<span onclick="ticket_attach_file(this); return false;"><img src="../img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle" title="Attach Files" onclick="return false;"></span>' : '';
								echo in_array('reply', $quick_actions) ? '<span onclick="ticket_send_reply(this); return false;"><img src="../img/icons/ROOK-reply-icon.png" class="inline-img no-toggle" title="Add Note" onclick="return false;"></span>' : '';
								echo in_array('archive', $quick_actions) ? '<span onclick="ticket_archive(this); return false;"><img src="../img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive" onclick="return false;"></span>' : '';
								echo '<img class="drag_handle pull-right inline-img no-toggle" title="Drag" src="../img/icons/drag_handle.png" />';
							echo '</span>';
							echo '<input type="text" name="reply_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
							echo '<input type="file" name="attach_'.$row['ticketid'].'" style="display:none;" class="form-control" />';
							echo '<div style="display:none;" class="assign_milestone"><select class="chosen-select-deselect" data-id="'.$row['ticketid'].'"><option value="unassign">Unassigned</option>';
							foreach(array_unique(array_filter(explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`project_path_milestone`.`milestone` SEPARATOR '#*#') `milestones` FROM `project` LEFT JOIN `project_path_milestone` ON CONCAT(',',`project`.`external_path`,',') LIKE CONCAT('%,',`project_path_milestone`.`project_path_milestone`,',%') WHERE `projectid`='".$row['projectid']."'"))['milestones']))) as $external_milestone) { ?>
									<option <?= $external_milestone == $row['external'] ? 'selected' : '' ?> value="<?= $external_milestone ?>"><?= $external_milestone ?></option>
							<?php }
							echo '</select></div><div class="clearfix"></div>'; ?>
							<div class="row">

								<h4><input type="checkbox" name="status" value="<?= $row['ticketid'] ?>" class="form-checkbox no-margin" onchange="mark_done(this);" <?= ( $row['status'] == $status_complete ) ? 'checked' : '' ?> />
									<a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$row['status']?>&tasklistid=<?=$row['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $row['tasklistid'] ?></a>: <?= ($url_tab=='Business') ? get_contact($dbc, $businessid, 'name') . ': ' : '' ?><?= ($url_tab=='Client') ? get_contact($dbc, $clientid) . ': ' : '' ?><?=limit_text($row['heading'], 5 )?>
							<?php echo '</span></h4></span></div>';
							echo '</li>';
						}
						if($show_forms && strpos($value_config, ',Sales Lead Path Intake,') !== FALSE) {
							while($row = mysqli_fetch_array( $form_result )) {
									$intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$row['intakeformid']."'"));
									$colour = $row['flag_colour'];
									if($colour == 'FFFFFF' || $colour == '') {
										$colour = '';
									}
									echo '<li style="background-color: #'.$colour.'" data-id-field="intakeid" id="'.$row['intakeid'].'" data-table="intake" class="ui-state-default">';
									echo '<div class="row"><h4>Intake #'.$row['intakeid'].': '.html_entity_decode($intake_form['form_name']).'<img class="drag_handle pull-right inline-img" src="../img/icons/drag_handle.png" /></div><div class="clearfix"></div>
										<input type="hidden" name="comment" value="" data-name="comment" data-table="intake_comments" data-id-field="intakecommid" data-id="" data-type="'.$row['intakeid'].'" data-type-field="intakeid">';
									echo '<input type="text" name="reminder" value="" class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;float:right;">';
									if(!empty($row['contactid'])) {
										echo '<div class="form-group">
											<label class="col-sm-4">Contact:</label>
											<div class="col-sm-8">'.(!empty(get_client($dbc, $row['contactid'])) ? get_client($dbc, $row['contactid']) : get_contact($dbc, $row['contactid'])).'</div>
										</div>';
										echo '<div class="clearfix"></div>';
									}

                                echo '<div class="form-group">
                                    <label class="col-sm-4">PDF:</label>
                                    <div class="col-sm-8"><a href="'.WEBSITE_URL.'/Intake/'.$row['intake_file'].'" target="_blank">View PDF <img class="inline-img" src="../img/pdf.png"></a></div>
                                </div>';
                                echo '<div class="clearfix"></div>';

                                echo '<div class="form-group">
                                    <label class="col-sm-4">Last Updated Date:</label>
                                    <div class="col-sm-8">'.$row['received_date'].'</div>
                                </div>';
                                echo '<div class="clearfix"></div>'; ?>

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
									</div><?php
									echo '<span class="pull-right action-icons" style="width: 100%;" data-intake="'.$row['intakeid'].'">'.
										(in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" onclick="intake_manual_flag(this); return false;" class="inline-img flag-icon no-toggle" title="Flag This!">' : '').
										(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" onclick="intake_flag(this); return false;" class="inline-img flag-icon no-toggle" title="Flag This!">' : '').
										(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" onclick="intake_email(this); return false;" class="inline-img email-icon no-toggle" title="Send Email">' : '').
										(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" onclick="intake_reminder(this); return false;" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '').
										(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" onclick="intake_archive(this); return false;" class="inline-img archive-icon no-toggle" title="Archive">' : '');
									echo '';
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
									echo '<div class="clearfix"></div>';
							echo '</li>';
							}
						}
						if($show_checklists && strpos($value_config, ',Sales Lead Path Checklists,') !== FALSE) {
							while($row = mysqli_fetch_array( $checklist_result )) {
								$colour = $row['flag_colour'];
								if($colour == 'FFFFFF' || $colour == '') {
									$colour = '';
								}
								echo '<li style="background-color: #'.$colour.'" data-id-field="checklistid" id="'.$row['checklistid'].'" data-table="checklist" class="ui-state-default">';
								echo '<input type="file" name="attach_checklist_board_'.$row['checklistid'].'" style="display:none;" />';
								echo '<span class="pull-right action-icons" style="width: 100%;" data-checklist="'.$row['checklistid'].'">'.
									'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/edit_checklist.php?edit='.$row['checklistid'].'\'); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit"></a>'.
									(in_array('flag_manual',$quick_actions) || in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" onclick="checklist_flag(this); return false;" class="inline-img flag-icon no-toggle" title="Flag This!">' : '').
									(in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" onclick="checklist_email(this); return false;" class="inline-img email-icon no-toggle" title="Send Email">' : '').
									(in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" onclick="checklist_reminder(this); return false;" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '').
									(in_array('attach', $quick_actions) ? '<img src="../img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon no-toggle" onclick="checklist_attach_file(this); return false;" title="Attach Files">' : '').
									(in_array('archive',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" onclick="checklist_archive(this); return false;" class="inline-img archive-icon no-toggle" title="Archive">' : '');
								echo '<img class="drag_handle pull-right inline-img no-toggle" src="../img/icons/drag_handle.png" title="Drag" />';
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
                                <?php foreach($staff_list as $staff) { ?>
                                    <option value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
                                <?php } ?>
                                </select>
                                <button class="submit_button btn brand-btn pull-right">Submit</button>
                                <button class="cancel_button btn brand-btn pull-right">Cancel</button>
                            </div><?php
                            echo '<div class="row"><h4><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/checklist.php?view='.$row['checklistid'].'&iframe_slider=1\'); return false;">'.$row['checklist_name'].'</a></h4></div><div class="clearfix"></div>';
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
                    } ?>

                        <li class="no-sort">
                            <?php if($show_checklists && strpos($value_config, ',Sales Lead Path Checklists,') !== FALSE) { ?>
                                <a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Checklist/edit_checklist.php?edit=NEW&salesid=<?= $_GET['id'] ?>&sales_milestone=<?= $status ?>'); return false;" data-milestone="<?= $milestone_row['milestone'] ?>" class="btn brand-btn pull-right">Add Checklist</a>
                            <?php } ?>
                            <?php if($show_forms && strpos($value_config, ',Sales Lead Path Intake,') !== FALSE) { ?>
                                <a href="" onclick="addIntakeForm(this); return false;" data-milestone="<?= $milestone_row['milestone'] ?>" class="btn brand-btn pull-right">Add Intake</a>
                            <?php } ?>
                            <a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?tab=sales&sales_milestone_timeline=<?=$status?>&task_path=<?=$task_path?>&salesid=<?=$_GET['id']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" class="btn brand-btn pull-right">Add Task</a></li><?php

                    echo '</ul>';
                    $i++;
                } ?>
            </div>
        </div>
        <?php if($_GET['p'] == 'salespath') { ?>
    </div>
</div>
<?php } ?>
