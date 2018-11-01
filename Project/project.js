$(document).ready(function() {
	$('.main-screen a').click(function(event) {
		if(!event.isDefaultPrevented() && $(this).attr('target') != '_blank' && this.href != '' && this.href != undefined) {
			$('.main_full_screen').css('float', 'left');
			loadingOverlayShow('.main_full_screen', $('.main_full_screen').height() + 20);
		}
	});
});

function clearCompletedProjectTask(sel) {
	var projectid = sel.value;

	if(confirm("Are you sure you want to clear all the completed tasks on this board?")) { //&& confirm("Are you sure you want to clear all the completed tasks on this board?")) {
        $.ajax({
            type: "GET",
            url: "../Tasks_Updated/task_ajax_all.php?fill=clear_project_completed_task&projectid="+projectid,
            dataType: "html",   //expect html to be returned
            success: function(response){
				alert('Completed task Deleted.');
                window.location.reload();
            }
        });
        //window.location.reload();
	} else {
		return false;
	}
}

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

function mark_task_date(sel) {
    var todo_date = sel.value;
	var tasklistid = sel.id.split('_')[1];

    $.ajax({
        type: "GET",
        url: "../Tasks_Updated/task_ajax_all.php?fill=mark_date&tasklistid="+tasklistid+'&todo_date='+todo_date,
        dataType: "html",
        success: function(response){
			//window.location.reload();
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

function viewProfile(img, category) {
	contact = $(img).closest('.form-group').find('option:selected').first().val();
	if(contact > 0) {
		overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+contact, '75%', true, true);
        if(category != 'no_reload') {
            var options = $(img).closest('.form-group').find('select').first();
            var iframe_check = setInterval(function() {
                if(!$('.iframe_overlay iframe').is(':visible')) {
                    $.post('projects_ajax.php?action=get_category_list', { category: category }, function(response) {
                        $(options).html(response);
                        $(options).trigger('change.select2');
                        $(options).val(contact).change();
                    });
                    clearInterval(iframe_check);
                }
            }, 500);
        }
	} else {
        alert("Please select a contact before attempting to view their profile.");
    }
}
function addReminder(img) {
    projectid = $('[name=projectid]').val();
    contact = 0;
    if(img != undefined) {
        contact = $(img).closest('.form-group').find('option:selected').first().val();
    }
    overlayIFrameSlider('../quick_action_reminders.php?tile=project&id='+projectid+'&contactid='+contact, 'auto', true, true);
}
function viewReminders(img) {
    projectid = $('[name=projectid]').val();
	contact = $(img).closest('.form-group').find('option:selected').first().val();
	if(contact > 0) {
		overlayIFrameSlider('../quick_action_reminders.php?tile=project&view=true&id='+projectid+'&contactid='+contact, 'auto', true, true);
	}
}
function newContact(img, category) {
	var options = $(img).closest('.form-group').find('select').first();
	overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&change=true&edit=new&businessid='+$('[name=businessid]').val()+'&category='+category, '75%', true, true, 'auto', true);
	iframe_contactid = 0;
	var iframe_check = setInterval(function() {
		if(!$('.iframe_overlay iframe').is(':visible')) {
			if(iframe_contactid > 0) {
				$.post('projects_ajax.php?action=get_category_list', { category: category }, function(response) {
					$(options).html(response);
					$(options).trigger('change.select2');
					$(options).val(iframe_contactid).change();
				});
			}
			clearInterval(iframe_check);
		} else if(!(iframe_contactid > 0)) {
			iframe_contactid = $($('.iframe_overlay iframe').get(0).contentDocument).find('[name=contactid]').val();
		}
	}, 500);
}
function selectType(type, target, label = '') {
	if(target == undefined) {
		$('#project_admin,#project_summary').hide();
		target = $('#display_screen');
		target.show();
	}
	if(type != undefined && type != '') {
		ajaxCalls.forEach(function(call) { call.abort(); });
		project_type = type;
		$('.search-results').addClass('hidden');
		$('.main-content-screen, #project_accordions').removeClass('hidden');
		$('.active.blue:not(.sidebar-lower-level)').removeClass('active').removeClass('blue');
		$('a[href$="type='+type+'"] li,a[href*="type='+type+'&"] li').addClass('active blue');
		if(label != '') {
			target.html('<h3 class="double-pad-left">'+label+'</h3>');
		} else {
			target.html('');
		}
        if (type=='favourite' || type=='pending') {
            $.ajax ({
                type: "GET",
                url: "projects_ajax.php?action=show_notes&subtab=projects_"+type,
                dataType: "html",
                success: function(response){
                	if(response != '') {
	                    target.append(response);
                	}
                }
            });
        }
        if(project_list[type] == undefined) {
            current_list = [];
        } else if(type == 'VIEW_ALL') {
        	current_list = project_list['VIEW_ALL'].slice();
        } else {
			current_list = project_list[type].slice();
		}
		loadProjects(target);
	} else {
		$('#display_screen').html('<h3>Please select a '+project_tile+' type.</h3>');
	}
}
function loadDBPanel() {
	var panel = $(this).closest('.panel').find('.panel-body');
	selectType(panel.data('project-type'), panel);
	setTimeout(function() { $(window).scrollTop(panel.closest('.panel').offset().top).scroll(); }, 500);
}
var ajaxCalls = [];
function loadProjects(target) {
	if(target.html() == '') {
		$('.main_full_screen').css('float', 'left');
		loadingOverlayShow('.main_full_screen', $('.main_full_screen').height() + 20);
	}
	//if(($('.dashboard-item:visible').length == 0 || ($('.dashboard-item:visible').last().offset().top < $(window).innerHeight() && loadMore) || ($('.dashboard-item:visible').last().offset().top < $(window).scrollTop() + $(window).innerHeight() && loadMore))) {
		loadMore = false;
		if(current_list.length > 0) {
			var business = [];
			$('.active.blue[data-businessid]').each(function() {
				if($(this).data('businessid') > 0) {
					business.push($(this).data('businessid'));
				}
			});
			var site = [];
			$('.active.blue[data-siteid]').each(function() {
				if($(this).data('siteid') > 0) {
					site.push($(this).data('siteid'));
				}
			});
			var dest = target;
			var load_list = [];
			if((business.indexOf(parseInt(current_list[0]['businessid'])) != -1 || !(business.length > 0)) && (site.indexOf(parseInt(current_list[0]['siteid'])) != -1 || !(site.length > 0))) {
				var project = current_list[0];
				if(project > 0) {
					load_list.push(project);
				} else {
					load_list.push(project['projectid']);
				}
				current_list.shift();
				if(load_list.length > 0) {
					ajaxCalls.push($.ajax({
						url: 'dashboard_load.php',
						method: 'POST',
						data: {
							tile: current_tile,
							projectids: load_list
						},
						dataType: 'html',
						success: function(response) {
							loadingOverlayHide();
							destroyInputs($('.panel-body:visible'));
							destroyInputs($('.main-content-screen .main-screen'));
							destroyInputs($('.search-results .main-screen'));
							dest.append(response);
							loadMore = true;
							$('.toggle-switch').off('click').click(function() {
								$(this).find('img').toggle();
								$(this).find('input').val($(this).find('input').val() == 'Yes' ? 'No' : 'Yes').change();
							});
							initInputs('.panel-body:visible');
							initInputs('.main-content-screen .main-screen');
							initInputs('.search-results .main-screen');
                            initTooltips();
							$('[data-table]').off('change',saveDBField).change(saveDBField);
							$('.empty_note').remove();
							loadProjects(dest);
							setSelectOnChange();
						}
					}));
				} else {
					loadMore = true;
				}
			} else {
				current_list.shift();
				loadMore = true;
				loadProjects(target);
			}
		} else if($('.dashboard-item:visible').length == 0) {
			loadingOverlayHide();
			target.append('<div class="dashboard-item override-dashboard-item empty_note"><b><em>No '+project_tile+' Found.</em></b></div>');
		}
	//}
}
function markReviewed(project) {
	$.ajax({
		url: 'projects_ajax.php?action=review_project',
		method: 'POST',
		data: {
			projectid: $(project).data('id')
		},
		success: function(response) {
			project.find('.review_date').html(response);
		}
	});
}
function markFavourite(img) {
	$(img).closest('h4').find('img').toggle();
	$.ajax({
		url: 'projects_ajax.php?action=mark_favourite',
		method: 'POST',
		data: {
			id: $(img).closest('.dashboard-item').data('id')
		},
		dataType: 'json',
		success: function(response) {
			$('a:contains(Favourite)').find('.pull-right').text(response.length);
			project_list["favourite"] = response.slice();
		}
	});
}
function saveDBField() {console.log('DBField');
	if($(this).data('table') != '') {
		var project = this;
		$.ajax({
			url: 'projects_ajax.php?action=project_fields',
			method: 'POST',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('identifier'),
				table: $(this).data('table'),
				field: this.name,
				value: this.value,
				project: $(this).data('project')
			},
			success: function(response) {
				if(project.name == 'status' && project.value == 'Archive') {
					$(project).closest('.dashboard-item').hide();
				}
				$(project).closest('.dashboard-item').find('[data-table='+$(project).data('table')+'][data-id=""]').data('id',response);
			}
		});
	}
}
function saveFieldMethod(field) {
	if(field.value == 'MANUAL') {
		doneSaving();
		return false;
	}
	if(field.type == 'checkbox' && field.checked) {
		$(field).closest('li').addClass('strikethrough');
	} else if(field.type == 'checkbox') {
		$(field).closest('li').removeClass('strikethrough');
		if(field.name == 'status' && $(field).is('[data-incomplete]')) {
			field.value = $(field).data('incomplete');
		}
	}
	var value = field.value;
	var name = field.name;
	var table = $(field).data('table');
	var type = $(field).data('type');
	if(name.substr(-2) == '[]') {
		value = '';
		name = name.substr(0,name.length-2);
		$('[name="'+field.name+'"]').each(function() {
			if(this.value != '') {
				if(value != '') {
					value += ',';
				}
				value += this.value;
			}
		});
	} else if($(field).data('concat') != undefined && $(field).data('concat') != '') {
        var value = [];
		$('[name="'+field.name+'"]').each(function() {
            value.push(this.value);
		});
        value = value.join($(field).data('concat'));
	} else if(name == 'to_do_date') {
		$('[name=to_do_end_date]').val(field.value).change();
	} else if(name == 'MANUAL' && table == 'project_detail') {
        type = 'detail_custom_'+$(field).closest('.form-group').find('[name=type]').first().val();
        table = 'project_comment';
        name = 'comment';
        $(field).data('type-field','type');
        $(field).data('id-field','projectcommid');
    } else if(name == 'type' && table == 'project_comment') {
        value = 'detail_custom_'+value;
    }
	$.ajax({
		url: 'projects_ajax.php?action=project_fields',
		method: 'POST',
		data: {
			field: name,
			value: value,
			table: table,
			id: $(field).data('id'),
			id_field: $(field).data('id-field'),
			type: type,
			type_field: $(field).data('type-field'),
			project: $('[name=projectid]').val()
		},
		success: function(response) {
			if(response > 0 && name == 'link') {
				// reloadDocuments();
			} else if(response > 0 && table == 'project') {
				$('[data-table=project]').data('id',response);
				$('[name=projectid]').val(response);
				$('[name=created_date]').trigger('change');
				window.history.replaceState('','Software',window.location.href.replace('edit=0','edit='+response));
				var id = response;
				$('a').not('.new-btn').each(function() {
					if(this.href.search('edit=0') >= 0) {
						this.href = this.href.replace('edit=0','edit='+id);
					} else if(this.href.search('edit%3D0') >= 0) {
						this.href = this.href.replace('edit%3D0','edit%3D'+id);
					}
				});
				if(salesid > 0) {
					$.post('projects_ajax.php?action=load_sales_scope',{ project: id, sales: salesid });
				}
			} else if(response > 0 && type != undefined && type != '' && table == 'project_comment') {
                $(field).closest('.new_group').find('input,textarea,select').data('id',response);
			} else if(response > 0 && type != undefined && type != '') {
				$('[data-table='+table+'][data-type='+type+']').data('id',response);
			} else if(response > 0) {
				$('[data-table='+table+']').data('id',response);
			}
			doneSaving();
			getProjectLabel($('[name=projectid]').val());
		}
	});
}
function loadPanel() {
	var panel = $(this).closest('.panel').find('.panel-body');
	panel.html('');
	$.ajax({
		url: panel.data('file-name'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			panel.html(response);
			loadingOverlayHide();
		}
	});
}
function waitForSave(btn,btname,funct) {
	if(btname == 'next'){
		var i = 0;
		var err = 0;
		$(".required").each(function(e){
			$(this).parent('div').find('.error_block').remove();
			if($(this).val() == ''){
				$(this).parent('div').append('<span class="error_block" style="color: #f00;font-size: 12px;">This field is requried</span>');
				err = 1;
				if(i==0){$(this).focus();}
				i++;
			}
		});
		if(err == 0){
			$(btn).text('Saving...');
			if(saving_field == null && current_fields.length == 0) {
				console.log('Waiting for Save to finish');
				setTimeout(function() { $(btn).click(); }, 500);
				return false;
			}
		} else {
			return false;
		}
	} else {
		$(btn).text('Saving...');
		if(saving_field == null && current_fields.length == 0) {
			console.log('Waiting for Save to finish');
			setTimeout(function() { $(btn).click(); }, 500);
			return false;
		}
	}
    return true;
}
function presave() {
	var flag = 0;
	var firsttarget = '';
	$('.required').each(function() {
			var target = this;
				if($(target).val() != null && $(target).val().length === 0) {
					if(flag == 0) {
						$firsttarget = $(this);
					}

					if($(target).is('select')) {
						var select2 = $(target).next('.select2');
						$(select2).find('.select2-selection').css('background-color', 'red');
						$(select2).find('.select2-selection__placeholder').css('color', 'white');
					} else {
						$(target).css('background-color', 'red');
					}

					flag = 1;
			}
			else {
				if($(target).is('select')) {
					var select2 = $(target).next('.select2');
					$(select2).find('.select2-selection').css('background-color', 'white');
				} else {
					$(target).css('background-color', 'white');
				}
			}
	});

	var currenttop = $firsttarget.offset().top;
	if(flag == 1) {
			alert("Please fill in the required fields");
			$('.main-screen .main-screen').scrollTop($('.standard-body-content').scrollTop() + currenttop - 30);
			return false;
	}

	return true;
}
function setSelectOnChange() {
	$('select[name="status[]"]').on('change', function() { selectStatus(this); });
}
function getDeliverables(mode) {
	if($('[name=include]:checked').length > 0) {
		var deliverables = [];
		$('[name=include]:checked').each(function() {
			deliverables.push(this.value);
		});
		$('#no-more-tables,.email_options,.pdf_options').hide();
		$('.'+mode+'_options').show();
		$.ajax({
			url: 'deliverable_list.php',
			method: 'POST',
			data: {
				list: deliverables,
				details: $('[name=includeDetails]:checked').length
			},
			success: function(response) {
				var editor = tinyMCE.get($('.email_options:visible,.pdf_options:visible').first().find('[name=deliver_list]').attr('id'));
				editor.setContent(response.split('#*#')[0]);
				editor.theme.resizeBy(-10,editor.contentDocument.body.scrollHeight - $(editor.iframeElement).height() + 40);
				$('[name=output_list]').val(response.split('#*#')[1]);
			}
		});
	} else {
		alert('Please select at least one deliverable to include.');
	}
}
function deliverable_email() {
	var deliverables = $('.deliver_list [name=list]').val().split(',');

}
function savePathName(type, name, i, projectid) {
	$.post('projects_ajax.php?action=set_path_names', {type:type,name:name,key:i,project:projectid});
}
function getProjectLabel(id) {
	$.post('projects_ajax.php?action=project_label', { projectid: id }, function(response) {
		$('.project_name').text(response);
	});
}
function toggleProjectTracking() {
	$('.time_tracking').text($('.time_tracking').text() == 'Stop Tracking Time' ? 'Get To Work' : 'Stop Tracking Time');
	$.post('../Project/projects_ajax.php?action=toggle_time_tracking', { projectid: projectid });
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