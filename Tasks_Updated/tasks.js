$(document).ready(function() {
	tasksInit();
});

function task_status(sel) {
    var status = sel.value;
	var tasklistid = sel.id.split('_')[1];
	var status = status.replace(" ", "FFMSPACE");
	var status = status.replace("&", "FFMEND");
	var status = status.replace("#", "FFMHASH");
    $.ajax({
        type: "GET",
        url: "../Tasks_Updated/task_ajax_all.php?fill=task_status&tasklistid="+tasklistid+'&status='+status,
        dataType: "html",
		success: function(response){
			//window.location.reload();
		}
    });
}

function saveTaskDefaultStatus(sel) {
    var status = sel.value;
	var status = status.replace(" ", "FFMSPACE");
	var status = status.replace("&", "FFMEND");
	var status = status.replace("#", "FFMHASH");
    $.ajax({
        type: "GET",
        url: "../Tasks_Updated/task_ajax_all.php?fill=task_default_status&task_default_status="+status,
        dataType: "html",
		success: function(response){
			//window.location.reload();
		}
    });
}

function mark_task_date(sel) {
    var todo_date = sel.value;
	var tasklistid = sel.id.split('_')[1];

    $.ajax({
        type: "GET",
        url: "../Tasks_Updated/task_ajax_all.php?fill=mark_date&tasklistid="+tasklistid+'&todo_date='+todo_date,
        dataType: "html",
        success: function(response){
		}
    });
}

function mark_task_staff(sel) {
	var tasklistid = sel.id.split('_')[1];
	var staff = [];

	$('#taskid_'+tasklistid+' [name="task_userid[]"]').find('option:selected').each(function() {
        staff.push(this.value);
	});

    $.ajax({
        type: "GET",
        url: "../Tasks_Updated/task_ajax_all.php?fill=mark_staff&tasklistid="+tasklistid+'&staff='+staff,
        dataType: "html",
        success: function(response) {
			//window.location.reload();
		}
    });
}

function saveTaskChecklist() {
	var checklist = 0;

    if ($('[name="task_include_checklists"]').is(':checked')) {
        checklist = 1;
    } else {
        checklist = 0;
    }

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_task_checklist&checklist="+checklist,
		dataType: "html",   //expect html to be returned
	});
}

function saveTaskIntake() {
	var intake = 0;

    if ($('[name="task_include_intake"]').is(':checked')) {
        intake = 1;
    } else {
        intake = 0;
    }

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_task_intake&intake="+intake,
		dataType: "html",   //expect html to be returned
	});
}

function saveFields() {
	var tab_list = [];
	$('[name="task_fields[]"]:checked').not(':disabled').each(function() {
		tab_list.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_fields&tab_list="+tab_list,
		dataType: "html",   //expect html to be returned
	});

}

function saveTabs() {
	var tab_list = [];
	$('[name="project_manage_dashboard[]"]:checked').not(':disabled').each(function() {
		tab_list.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_tabs&tab_list="+tab_list,
		dataType: "html",   //expect html to be returned
	});

}

function saveQuickIcon() {
	var tab_list = [];
	$('[name="task_quick_action_icons[]"]:checked').not(':disabled').each(function() {
		tab_list.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_quick_icon&tab_list="+tab_list,
		dataType: "html",   //expect html to be returned
	});
}

function saveFlagColours() {
	var flag_colours = [];
	$('[name="flag_colours[]"]:checked').not(':disabled').each(function() {
		flag_colours.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_flag_colours&flag_colours="+flag_colours,
		dataType: "html",   //expect html to be returned
	});
}

function saveFlagName() {
	var flag_name = [];

	$('[name="flag_name[]"]').each(function() {
		flag_name.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=setting_flag_name&flag_name="+flag_name,
		dataType: "html",   //expect html to be returned
	});
}

function sliderLayout(sel) {
	var tile_value = sel.value;

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=tasks_slider_layout&layout="+tile_value,
		dataType: "html",   //expect html to be returned
	});
}

function taskTileNoun(sel) {
	var task_tile = $('.task_tile').val();
	var task_noun = $('.task_noun').val();

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=task_tile_noun&task_tile="+task_tile+"&task_noun="+task_noun,
		dataType: "html",   //expect html to be returned
	});
}

function saveAutoArchive(sel) {
	var tile_value = sel.value;

    if ( $(sel).is(':checked') ) {
        status = 1;
    } else {
        status = 0;
    }

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=tasklist_auto_archive&archive="+status,
		dataType: "html",   //expect html to be returned
	});
}

function saveAutoArchiveDays(sel) {
	var tile_value = sel.value;

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "task_ajax_all.php?fill=tasklist_auto_archive_days&archivedays="+tile_value,
		dataType: "html",   //expect html to be returned
	});
}

function tasksInit() {
	$("#task_path").change(function() {
		var task_path = $("#task_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=task_path_milestone&task_path="+task_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#task_milestone_timeline').html(response);
				$("#task_milestone_timeline").trigger("change.select2");
			}
		});
	});

    $( ".connectedSortable" ).sortable({
		connectWith: ".connectedSortable",
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		update: function( event, ui ) {
			var taskid = ui.item.attr("id"); //Done
			var table = ui.item.data('table');
			var id_field = ui.item.data('id-field');
			var table_class = ui.item.parent().attr("class");
			var status = table_class.split(' ')[2];

			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "task_ajax_all.php?fill=tasklist&tasklistid="+taskid+"&table="+table+"&id_field="+id_field+"&task_milestone_timeline="+status,
				dataType: "html",   //expect html to be returned
				success: function(response){
					location.reload();
				}
			});
		}
    }).disableSelection();

	DoubleScroll(document.getElementById('scrum_tickets'));
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
	scrollbar.firstChild.style.height= '0px';
	scrollbar.firstChild.style.paddingTop= '1px';
	scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
	scrollbar.onscroll= function() {
		element.scrollLeft= scrollbar.scrollLeft;
	};
	element.onscroll= function() {
		scrollbar.scrollLeft= element.scrollLeft;
	};
	element.parentNode.insertBefore(scrollbar, element);
}

function changeEndAme(sel) {
	$(this).focus();

	$(this).prop("disabled",false);
	var stage = sel.value;
	var typeId = sel.id;

	var tasklistid = typeId.split(' ');

	var status = tasklistid[1];
	var task_path = tasklistid[2];
	var taskboardid = tasklistid[3];
	var salesid = tasklistid[4];

	var stage = stage.replace(" ", "FFMSPACE");
	var stage = stage.replace("&", "FFMEND");
	var stage = stage.replace("#", "FFMHASH");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=add_task&task_milestone_timeline="+status+"&task_path="+task_path+"&heading="+stage+"&taskboardid="+taskboardid+"&salesid="+salesid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function handleClick(sel) {
    var stagee = sel.value;
	var contactide = $('.contacterid').val();

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=trellotable&contactid="+contactide+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function addStaff(sel) {
	var taskid = $(sel).data('taskid');
    //var block = $('div.add_staff').last();
	var block = $('div#taskid_'+taskid).last();
    destroyInputs('.add_staff');
    clone = block.clone();
    clone.find('.form-control').val('');
    block.after(clone);
    initInputs('.add_staff');
}

function removeStaff(button) {
    if($('div.add_staff').length <= 1) {
        addStaff();
    }
	var taskid = $(button).data('taskid');

    $(button).closest('div#taskid_'+taskid).remove();
    $('div.add_staff').first().find('[name="task_userid[]"]').change();
}

function sync(task) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=is_sync&sync="+$(task).data('sync')+"&tasklistid="+$(task).closest('[data-task]').data('task'),
		dataType: "html",   //expect html to be returned
		success: function(response){
            // $(task).hide();
            $(task).data('sync',$(task).data('sync') > 0 ? 0 : 1);
            $(task).closest('li.t_item,.standard-body-title').find('.sync_visible_icon').toggle()
            $(task).find('img').prop('title',($(task).data('sync') > 0 ? 'Not Synced To Customer Scrum Board' : 'Synced To Customer Scrum Board'));
            try {
                $(task).find('img').tooltip('destroy');
            } catch (err) { }
            initTooltips();
			// location.reload();
		}
	});
}
