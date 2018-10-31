ticket_wait = false;
ticket_lock_interval = '';
ticket_reload_tabs = '';
ticket_excess_confirm = true;
address_validation = [''];
use_google_suggest = false;
suggest_from_google = true;
ticket_reloading_service_checklist = '';
finishing_ticket = false;
var current_business = 0;
var reload_services = false;
var delay_notify = false;
$(document).ready(function() {
	// Mark fields manually set as manual
	$('input').keyup(function() {
        $(this).off('keyup');
        if(this.name == 'heading' && $('[name=heading_auto]').val() == 1) {
            $('[name=heading_auto]').val(0).change();
        }
		$(this).data('manual','1');
	});
	ticketid = $("#ticketid").val();
	$("#add_ticket_form").submit(function( event ) {
		var supportid = $("#supportid").val();
		if(ticketid == undefined) {
			var businessid      = $("#businessid").val();
			var clientid        = $("#clientid").val();
			var service_type    = $("#service_type").val();
			var heading         = $("input[name=heading]").val();
			var status          = $("#status").val();
			var contactid       = $("#contactid").val();
			var to_do_date      = $("#to_do_date").val();
			var to_do_end_date  = $("#to_do_end_date").val();

			if(supportid != 0) {
				if ((businessid=='' && clientid=='') || service_type == '' || heading == '' ||  contactid != null) {
					alert("Please make sure you have filled in all of the required fields.");
					if(businessid == '') {
						setFocusInPanel($("#businessid"));
					} else if(service_type == '') {
						setFocusInPanel($("#service_type"));
					} else if(heading == '') {
						setFocusInPanel($("input[name=heading]"));
					} else if(contactid != null) {
						setFocusInPanel($("#contactid"));
					}
					return false;
				}
			} else {
				if ((businessid=='' && clientid=='') || service_type == '' || heading == '' || status == '' || to_do_date == '' || to_do_end_date == '' || contactid == null) {
					alert("Please make sure you have filled in all of the required fields.");
					if(businessid == '') {
						setFocusInPanel($("#businessid"));
					} else if(service_type == '') {
						setFocusInPanel($("#service_type"));
					} else if(heading == '') {
						setFocusInPanel($("input[name=heading]"));
					} else if(status == '') {
						setFocusInPanel($("#status"));
					} else if(to_do_date == '') {
						setFocusInPanel($("#to_do_date"));
					} else if(to_do_end_date == '') {
						setFocusInPanel($("#to_do_end_date"));
					} else if(contactid == null)  {
						setFocusInPanel($("#contactid"));
					}
					return false;
				}
			}
		}
	});

	$("#project_path").change(function() {
		var project_path = $("#project_path").val();
		$.ajax({
			type: "GET",
			url: "ticket_ajax_all.php?fill=project_path_milestone&project_path="+project_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#milestone_timeline').html(response);
				$("#milestone_timeline").trigger("change.select2");
			}
		});
	});

    $.datepicker.setDefaults({
        onSelect: function(value) {
			$(this).change();
            if(this.id == 'to_do_date') {
                var date = new Date(value);
                date.setDate(date.getDate() + 1);
                $("#to_do_end_date").datepicker("setDate", date).change();
            }
        }
    });
	reload_attached_image();
	reload_related();
	setSave();
	initSelectOnChanges();
	filterStaffNoShift();
});
$(window).load(function() {
	destroyTinyMce();
});
$(document).on('click', 'img[src*="ROOK-add-icon.png"][data-history-label]', function() {
	var label = $(this).data('history-label');
	var section = $(this).closest('.tab-section').find('h3').first().text();
	addClickHistory('Add', label, section);
});
$(document).on('click', 'img[src*="remove.png"][data-history-label]', function() {
	var label = $(this).data('history-label');
	var section = $(this).closest('.tab-section').find('h3').first().text();
	addClickHistory('Remove', label, section);
});
$(document).on('click', '[data-history-label]:not([src*="remove.png"],[src*="ROOK-add-icon.png"])', function() {
	var label = $(this).data('history-label');
	var section = $(this).closest('.tab-section').find('h3').first().text();
	addClickHistory('Function', label, section);
});
function addClickHistory(icon, label, section) {
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_click_history',
		method: 'POST',
		data: { ticketid: ticketid, section: section, label: label, icon: icon },
		success:function(response) {

		}
	});
}
function destroyTinyMce() {
	// if($('#textarea_style').val() == 'no_editor') {
		// destroyInputs('.tab-section');
		// destroyInputs('.panel-body');
		// $('.tab-section textarea,.panel-body textarea').addClass('noMceEditor');
		// initInputs('.tab-section');
		// initInputs('.panel-body');
	// }
}
function viewFullTicket(a) {
	if($('#calendar_view').val() == 'true') {
		window.parent.$('[name="calendar_iframe"],[name="daysheet_iframe"]').off('load');
		window.parent.$('[name="calendar_iframe"],[name="daysheet_iframe"]').attr('src','../blank_loading_page.php');
		window.parent.overlayIFrameSlider('../Ticket/index.php?edit='+ticketid+'&ticketid='+ticketid+'&from='+from_url+'&calendar_view=true'+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name));
	} else {
		window.location.href = '../Ticket/index.php?edit='+ticketid+'&ticketid='+ticketid+'&from='+from_url+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
	}
}
function viewProfile(img, view_contactid = '') {
	var contact = $(img).closest('.multi-block,.form-group').find('[name=item_id],select').first();
	if(view_contactid == '') {
		view_contactid = contact.val();
	}
	if(view_contactid > 0 && contact.data('type') == 'Staff') {
		overlayIFrameSlider('../Staff/staff_edit.php?view_only=id_card&contactid='+view_contactid, '33%', true, true);
	} else if(view_contactid > 0) {
		overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+view_contactid, '75%', true, true);
	} else {
		alert('Please select the contact before attempting to view their profile.');
	}
}

function viewProject(img) {
	var contact = $(img).closest('.multi-block,.form-group').find('[name=item_id],select').first();
	if(contact.val() > 0) {
		overlayIFrameSlider('../Project/projects.php?edit='+contact.val()+'&tab=summary', '75%', true, true);
	} else {
		alert('Please select the Project before attempting to view summary.');
	}
}

function viewService(img) {
	var contact = $(img).closest('.multi-block,.form-group').find('[name=item_id],select').first();
	if(contact.val() > 0) {
		overlayIFrameSlider('../Services/service.php?p=preview&id='+contact.val(), '75%', true, true,'100%');
	} else {
		alert('Please select the Service before attempting to view summary.');
	}
}

function viewSite(img) {
	var contact = $(img).closest('.multi-block,.form-group').find('[name=item_id],select').first();
	if($(contact).find('option:selected').length > 1) {
		dialogViewSite(img);
	} else {
		viewProfile(img);
	}
}	

function dialogViewSite(img) {
	$('#dialog_view_site').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		open: function() {
			var contact = $(img).closest('.multi-block,.form-group').find('[name=item_id],select').first();
			var site_html = '';
			$(contact).find('option:selected').each(function() {
				site_html += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
			});
			$('[name="view_site"]').html(site_html).trigger('change.select2');
		},
		buttons: {
			"View Site": function() {
				viewProfile(img,$('[name="view_site"]').val());
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
}

function send_email(button) {
	if($(button).is('[data-table]')) {
		$.ajax({
			url: 'ticket_ajax_all.php?action=send_email',
			method: 'POST',
			data: {
				table: $(button).data('table'),
				id_field: $(button).data('id-field'),
				id: $(button).data('id'),
				field: $(button).data('field'),
				recipient: $(button).closest('.email-block').find('.email_recipient').val(),
				sender: $(button).closest('.email_div').find('.email_sender').val(),
				sender_name: $(button).closest('.email_div').find('.email_sender_name').val(),
				subject: $(button).closest('.email_div').find('.email_subject').val(),
				body: $(button).closest('.email_div').find('.email_body').val()
			},
			success: function(response) {
				//if(response != '') {
					//alert(response);
				//}
				alert("Email Sent Successfully.");
				$(button).closest('.email_div').hide().closest('.multi-block').find('[name=check_send_email]').removeAttr('checked');
			}
		});
	} else if($(button).closest('#approval_submit').length > 0) {
		$.ajax({
			url: 'ticket_ajax_all.php?action=send_email',
			method: 'POST',
			data: {
				recipient: $(button).closest('#approval_submit').find('[name=email_recipient]').val().split(';'),
				sender: $(button).closest('#approval_submit').find('.email_sender').val(),
				sender_name: $(button).closest('#approval_submit').find('.email_sender_name').val(),
				subject: $(button).closest('#approval_submit').find('.email_subject').val(),
				body: $(button).closest('#approval_submit').find('.email_body').val()
			},
			success: function(response) {
				$('[data-target=#approval_submit]').first().click();
			}
		});
	} else {
		$.ajax({
			url: 'ticket_ajax_all.php?action=send_email',
			method: 'POST',
			data: {
				recipient: $(button).closest('.scheduled_stop').find('.email_recipient').val(),
				sender: $(button).closest('.email_div').find('.email_sender').val(),
				sender_name: $(button).closest('.email_div').find('.email_sender_name').val(),
				subject: $(button).closest('.email_div').find('.email_subject').val(),
				body: $(button).closest('.email_div').find('.email_body').val()
			},
			success: function(response) {
				//if(response != '') {
					//alert(response);
				//}
				alert("Email Sent Successfully.");
				$(button).closest('.email_div').hide().closest('.scheduled_stop').find('[name=email]').closest('.form-group').find('[type=checkbox]').removeAttr('checked');
			}
		});
	}
}
function setSave() {
	$('[data-table]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
	$('.toggleSwitch').off('click').click(function() {
		if($(this).find('.toggle').data('no-toggle-off') == undefined || $(this).find('.toggle').data('no-toggle-off') != 1 || $(this).find('.toggle').val() != 1 || $(this).find('.on_img').is(':visible')) {
			$(this).find('span').toggle();
			if($(this).find('.toggle').data('no-toggle-off') != undefined && $(this).find('.toggle').data('no-toggle-off') == 1) {
				$(this).find('.toggle').val(1).change();
				if(typeof still_reloading_tab != 'undefined') {
					clearInterval(still_reloading_tab);
				}
				reload_checkin($(this).closest('.tab-section'));
			} else {
				$(this).find('.toggle').val($(this).find('.toggle').val() == 1 ? 0 : 1).change();
				if($(this).hasClass('staffSwitch')) {
					if(typeof still_reloading_tab != 'undefined') {
						clearInterval(still_reloading_tab);
					}
					reload_checkin($(this).closest('.tab-section'));
				}
			}
			reload_summary();
		}
	});
	$('#collapse_delivery,#tab_section_ticket_delivery').sortable({
		handle: '.stop_sort',
		items: '.scheduled_stop',
		update: sortScheduledStops
	});
}
function getTabLocks() {
	var lock_ids = [];
	$('.tab-section:visible,.panel-body:visible').filter(function() { if($(this).find('[data-table]:visible').not('[data-table=ticket_comment],[data-table=ticket_document]').length > 0 && $(this).parents('[id^=tab_section_]').length == 0) { return true; } }).each(function() {
		var id = this.id.replace('tab_section_','');
		if($(this).is('[class*=panel-body]') || ($('a[data-tab-target='+id+']').find('.active.blue').length > 0 && $('a[data-tab-target='+id+']').length > 0)) {
			if($(this).data('lock_held') != 'true') {
				// console.log('getting_lock');
				$(this).data('lock_held','true');
				var tab = this;
				$.get('ticket_ajax_all.php?action=get_locks&tab='+id+'&ticketid='+ticketid,function(response) {
					var response = JSON.parse(response);
					response.users.push(user_id);
					if(response.locked > 0 && response.locked != user_id) {
						lockTab(tab);
					} else {
						response.locked = user_id;
						unlockTab(tab);
					}
					lock_ids.push(response.locked);
					$('.tile-header').find('.user_list').remove();
					$('.tile-header').find('.settings-block').after('<div class="pull-right settings-block user_list"></div>');
					response.users.forEach(function(user_id) {
						$.post('../ajax_all.php?action=user_profile_id&user='+user_id, function(response) {
							$('.tile-header').find('.user_list #'+user_id).remove();
							$('.tile-header').find('.user_list').append('<span id="'+user_id+'">'+response+'</span>');
						});
					});
				});
			}
		} else if($('a[data-tab-target='+id+']').length > 0) {
			$(this).data('lock_held','false');
			lockTab(this);
		}
	});
}
function releaseLock() {
	$('.tab-section:visible,.panel-body:visible').filter(function() { if($(this).find('[data-table]:visible').not('[data-table=ticket_comment],[data-table=ticket_document]').length > 0) { return true; } }).each(function() {
		lockTab(this);
	})
}
function unlockTab(tab) {
	var params = new URLSearchParams(window.location.search);
	if(tab.id != 'tab_section_ticket_type') {
		$(tab).load('edit_ticket_tab.php?tab_only=true&tab='+(tab.id.replace('tab_section_',''))+'&ticketid='+ticketid+'&from='+params.get('from'), function() {
			initInputs('#'+tab.id);
			setSave();
		});
	}
	reload_documents();
	reload_notes();
	clearTimeout(ticket_lock_interval);
	// ticket_lock_interval = setTimeout(releaseLock, 300000);
}
function lockTab(tab) {
	$(tab).find('[data-table]:visible').not('[data-table=ticket_document],[data-table=ticket_comment],[type=hidden]').each(function() {
		$(this).closest('div').css('pointer-events','none').css('opacity','0.5');
	});
	$(tab).find('img[src*=add],img[src*=remove]').each(function() {
		$(this).css('pointer-events','none').css('opacity','0.5');
	});
	var time = new Date() / 1000;
	if(time > $(tab).data('lock_time') + 30 || $(tab).data('lock_time') == '' || $(tab).data('lock_time') == undefined) {
		$.get('ticket_ajax_all.php?action=release_locks&tab='+tab.id.replace('tab_section_','')+'&ticketid='+ticketid);
		// console.log('saving lock');
		$(tab).data('lock_time',time);
	}

	clearTimeout(ticket_reload_tabs);
	// ticket_reload_tabs = setTimeout(reloadTabs, 60000);
}
function reloadTabs() {
	var params = new URLSearchParams(window.location.search);
	$('.tab-section:visible,.panel-body:visible').filter(function() { if($(this).find('[style*=opacity]').length > 0) { return true; } }).each(function() {
		if(this.id != 'tab_section_ticket_type') {
			$(this).load('edit_ticket_tab.php?tab_only=true&tab='+(this.id.replace('tab_section_',''))+'&ticketid='+ticketid+'&from='+params.get('from'), function() {
				initInputs('#'+this.id);
				setSave();
				lockTab(this);
			});
		}
	});
	reload_documents();
	reload_notes();
	clearTimeout(ticket_reload_tabs);
}
function saveFieldMethod(field) {
	if(field.target != undefined) {
		field = field.target;
	}
    if(field.name == 'ticket_type' && $(field).data('table') == 'tickets' && ticketid == '0') {
        // window.location.replace(window.location.href.replace('type=','type='+field.value+'&'));

		var curr_url = window.location.search;
		if(curr_url.indexOf('?') != -1) {
			curr_url = curr_url.split('?')[1];
		}
		var query_string_arr = {};
		var query_strings = curr_url.split('&');
		query_strings.forEach(function(query_string) {
			if(query_string.indexOf('=') != -1) {
				var pair = query_string.split('=');
				query_string_arr[pair[0]] = pair[1].replace(/\+/g, " ");
			}
		});
		query_string_arr["type"] = field.value;
		var new_url = decodeURIComponent("?"+$.param(query_string_arr));
		window.history.replaceState(null, '', new_url);
        window.location.reload();
        return;
    }
	if($('#new_ticket_from_calendar').val() == '1') {
		$('#new_ticket_from_calendar').val(0);
		saveNewTicketFromCalendar(field);
		return;
	} else if($(field).data('table') != 'tickets' && !(ticketid > 0)) {
		current_fields.unshift(field);
		field = $('[data-table=tickets]').not('[name=ticket_type]').first().get(0);
	} else if($(field).data('table') == 'tickets' && field.name != 'ticket_type' && !(ticketid > 0) && $('[name=ticket_type]').length > 0) {
		current_fields.unshift(field);
		field = $('[name=ticket_type]').first().get(0);
	}
    if($('#customer_rate_services').val() == '1') {
		$('#customer_rate_services').val('0');
		$('[name=billing_discount_type],[name=billing_discount]').filter(function() { return this.value != ''; }).each(function() {
			current_fields.push(this);
		});
	}
	if(ticketid_list.length == 0) {
		ticketid_list = [ticketid];
	}
	if(force_caps && field.type == 'text') {
		field.value = field.value.toUpperCase();
	}
	if(field.name == 'item_id' && $(field).data('table') == 'ticket_attached' && $(field).data('type') == 'Staff' && ($(field).data('verified-shift') == undefined || $(field).data('verified-shift') != 'true')) {
		var check_field = field;
		var check_field_value = field.value;
		checkStaffShifts(check_field.value).success(function(response) {
			var response = JSON.parse(response);
			if(response.success == true) {
				$(check_field).data('verified-shift','true');
				$(check_field).val(check_field_value).trigger('change.select2').change();
			} else {
				alert(response.message);
				$(check_field).val('').trigger('change.select2');
				$(check_field).data('verified-shift','');
			}
		});
		field = '';
	}
	ticketid_list.forEach(function(ticket) {
		var current_ticketid = ticket;
		if(field.type == 'file' && field.name == 'attached_image') {
			var file = new FormData();
			file.append('file', field.files[0]);
			file.append('ticket',current_ticketid);
			field_name = field.name;
			if(field_name.split('__')[1] > 0) {
				field_name = field_name.split('__')[0];
			}
			$.ajax({
				url: 'ticket_ajax_all.php?action=attached_image',
				method: 'POST',
				processData: false,
				contentType: false,
				data: file,
				success: function(response) {
					doneSaving();
					reload_attached_image();
				}
			})
		} else if(field.type == 'file') {
			var files = new FormData();
			for(var i = 0; i < field.files.length; i++) {
				files.append('files[]',field.files[i]);
			}
			files.append('table',$(field).data('table'));
			files.append('table_id',$(field).data('id'));
			files.append('field',field.name);
			files.append('ticket',current_ticketid);
			field_name = field.name;
			field.value = '';
			$.ajax({
				url: 'ticket_ajax_all.php?action=add_file',
				method: 'POST',
				processData: false,
				contentType: false,
				data: files,
				success: function(response) {
					doneSaving();
					reload_documents();
					reload_customer_images();
				}
			});
		} else if(field.value == 'ADD_NEW') {
			if($(field).data('table') != 'inventory') {
				overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit=new&category='+$(field).data('category')+(field.name == 'clientid' ? '&businessid='+$('select[name=businessid]').val() : ''), '75%', true, true);
				iframe_contactid = 0;
				var this_category = $(field).data('category');
				var iframe_check = setInterval(function() {
					if(!$('.iframe_overlay iframe').is(':visible')) {
						if(iframe_contactid > 0) {
							$.post('ticket_ajax_all.php?action=get_category_list', { category: this_category }, function(response) {
								$(field).html(response);
								$(field).append('<option value="ADD_NEW">Add New '+this_category+'</option>');
								$(field).append('<option value="ADD_NEW">One Time '+this_category+'</option>');
								$(field).trigger('change.select2');
								$(field).val(iframe_contactid).change();
							});
						}
						clearInterval(iframe_check);
					} else if(!(iframe_contactid > 0)) {
						iframe_contactid = $($('.iframe_overlay iframe').get(0).contentDocument).find('[name=contactid]').val();
					}
				}, 500);
			}
			doneSaving();
		} else if(field.value != 'MANUAL' && field != '') {
			var block = $(field).closest('.multi-block,.scheduled_stop,.staff-multi-time');
			var id_num = $(field).data('id');
			var table_name = $(field).data('table');
			var data_type = $(field).data('type');
			var save_value = field.value;
			var field_name = field.name.replace('[]','');
			if(field_name.split('__')[1] > 0) {
				field_name = field_name.split('__')[0];
			}
			if($(field).data('field-name') != undefined && $(field).data('field-name') != '') {
				field_name = $(field).data('field-name');
			}
			if(table_name == 'tickets' && $(field).data('id-field') == 'ticketid' && $.inArray(field_name,['pickup_name','pickup_address','pickup_city','pickup_postal_code','pickup_link','pickup_volume','to_do_date','to_do_start_time','pickup_order']) < 0) {
				id_num = current_ticketid;
			}
            if(['est_time'].indexOf(field_name) >= 0 && $(field).is('[class*=timepicker]:not([class*=datetimepicker])')) {
                save_value = save_value.split(':');
                save_value = (save_value[0] * 1) + (save_value[1] / 60);
            }
			if(field.name.substr(-2) == '[]' && $(field).find('option').length > 0) {
				var value = [];
				$(field).find('option:selected').each(function() {
					value.push(this.value);
				});
				save_value = ','+value.join(',')+',';
				if(field_name == 'contactid') {
					value.forEach(function(id) {
						if($('#collapse_staff,#tab_section_ticket_staff_list').length > 0 && $('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).val() == id; }).length == 0) {
							if($('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).find('option:selected').length == 0; }).length == 0) {
								addMulti($('#collapse_staff .multi-block img,#tab_section_ticket_staff_list .multi-block img'));
							}
							$('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).find('option:selected').length == 0; }).last().val(id).trigger('change.select2').change();
						}
					});
				}
			} else if(field.name == 'other_ind') {
				var value = [];
				$('.individual_present').each(function() {
					var item = $(this).find('select[name=other_ind]').val();
					if(item == 'MANUAL') {
						item = $(this).find('input[name=other_ind]').val();
					}
					value.push(item);
				});
				save_value = value.join('#*#');
			} else if($(field).is('[data-concat]')) {
				var value = [];
				$('[name='+field.name+'][data-concat="'+$(field).data('concat')+'"]').filter(function() { return $(this).data('id') == $(field).data('id') && $(this).data('group') == $(field).data('group'); }).each(function() {
					if(!$(this).is(':disabled') && (this.type != 'checkbox' || this.checked)) {
						value.push(this.value);
					}
				});
				if(value.length == 0 && table_name == 'tickets') {
					$('[name='+field.name+'][data-table=tickets]').each(function() {
						if(!$(this).is(':disabled') && (this.type != 'checkbox' || this.checked)) {
							value.push(this.value);
						}
					});
				}
				save_value = value.join($(field).data('concat'));
			} else if(field.type == 'checkbox' && !field.checked) {
				save_value = '';
			}
            if((save_value == '' || save_value == undefined) && !(id_num > 0) && field.name != 'ticket_type') {
                doneSaving();
                return;
            }
			if((field_name == 'item_id' || field_name == 'deleted') && $(field).data('type') == 'equipment') {
				var equipmentids = [];
				$('#collapse_ticket_equipment [name=item_id] option:selected,#tab_section_ticket_equipment [name=item_id] option:selected').each(function() {
					if(this.value > 0 && $(this).closest('.multi-block').find('[name="deleted"]').val() != 1) {
						equipmentids.push(this.value);
					}
				});
				$('[name=equipmentid][data-table=tickets]').val(equipmentids.join(',')).change();
			} else if((field_name == 'item_id' || field_name == 'deleted') && $(field).data('type') == 'Staff') {
				var staff_ids = [];
				$('#collapse_staff [name=item_id] option:selected,#tab_section_ticket_staff_list [name=item_id] option:selected').each(function() {
					if(this.value > 0 && $(this).closest('.multi-block').find('[name="deleted"]').val() != 1) {
						staff_ids.push(this.value);
					}
				});
				$('[name=contactid]').val(','+staff_ids.join(',')+',').change();
			} else if(field_name == 'mileage' && table_name == 'mileage') {
				$('[name=cost][data-rate][readonly]:not([data-table])').val(round2Fixed(save_value * $('[name=cost][data-rate][readonly]:not([data-table])').data('rate')));
			} else if(field_name == 'position' && table_name == 'ticket_attached' && data_type == 'Staff') {
				var opt = $(field).find('option:selected').first();
				if(opt.data('regular') > 0) {
					$(field).closest('.multi-block').find('[name=rate]').first().val(opt.data('regular'));
				} else if(opt.data('hourly') > 0) {
					$(field).closest('.multi-block').find('[name=rate]').first().val(opt.data('hourly'));
				}
			} else if(field_name == 'item_id' && table_name == 'ticket_attached' && data_type == 'inventory') {
				var opt = $(field).find('option:selected').first();
				var price = opt.data('price');
				$(field).closest('.multi-block').find('[name=rate]').first().empty();
				price.forEach(function(price_point) {
					$(field).closest('.multi-block').find('[name=rate]').first().append('<option value="'+price_point+'">'+price_point+'</option>');
				});
				$(field).closest('.multi-block').find('[name=total]').first().val($(field).closest('.multi-block').find('[name=qty]').val() * $(field).closest('.multi-block').find('[name=rate]').val());
			} else if(field_name == 'qty' && table_name == 'ticket_attached' && data_type == 'inventory') {
				$(field).closest('.multi-block').find('[name=total]').first().val(save_value * $(field).closest('.multi-block').find('[name=rate]').val());
			} else if(field_name == 'qty' && table_name == 'ticket_attached' && data_type == 'misc_item') {
				$(field).closest('.multi-block').find('[name=total]').first().val(save_value * $(field).closest('.multi-block').find('[name=rate]').val());
			} else if(field_name == 'rate' && table_name == 'ticket_attached' && data_type == 'inventory') {
				$(field).closest('.multi-block').find('[name=total]').first().val(save_value * $(field).closest('.multi-block').find('[name=qty]').val());
			} else if(field_name == 'rate' && table_name == 'ticket_attached' && data_type == 'misc_item') {
				$(field).closest('.multi-block').find('[name=total]').first().val(save_value * $(field).closest('.multi-block').find('[name=qty]').val());
			} else if((field_name == 'address' || field_name == 'city' || field_name == 'postal_code') && table_name == 'ticket_schedule' && block.find('[name=map_link]').first().data('auto-fill') == 'auto') {
				block.find('[name=map_link]').first().val('https://www.google.ca/maps/place/'+encodeURI(block.find('[name=address]').val()+','+block.find('[name=city]').val()+','+block.find('[name=postal_code]').val())).change();
				if($(field).closest('.scheduled_stop').find('[name="type"]') == undefined || $(field).closest('.scheduled_stop').find('[name="type"] option:selected').data('warehouse') != 'yes' && suggest_from_google) {
					$.post('ticket_ajax_all.php?action=validate_address', { address: block.find('[name=address]').val(), city: block.find('[name=city]').val(), postal: block.find('[name=postal_code]').val() }, function(response) {
						address_validation = response.split('|');
                        setTimeout(function() {
                            validate_address(address_validation, block);
                        }, 1000);
					});
				}
			} else if(field_name == 'type' && table_name == 'ticket_schedule') {
				if($(field).find('option:selected').data('warehouse') == 'yes') {
					$(field).closest('.scheduled_stop').find('[name=type_1]').prop('checked',false).filter(function() { return this.value == 'warehouse' }).first().prop('checked',true);
					if($(field).find('option:selected').data('set-time') != '' && $(field).find('option:selected').data('set-time') != undefined) {
						$(field).closest('.scheduled_stop').find('[name=to_do_start_time]').val($(field).find('option:selected').data('set-time')).change();
					}
					if($(field).find('option:selected').data('address') != '' && $(field).find('option:selected').data('address') != undefined) {
						$(field).closest('.scheduled_stop').find('[name=address]').val($(field).find('option:selected').data('address')).change();
						$(field).closest('.scheduled_stop').find('[name=city]').val($(field).find('option:selected').data('city')).change();
						$(field).closest('.scheduled_stop').find('[name=postal_code]').val($(field).find('option:selected').data('postal')).change();
					}
				} else if(field.type == 'select') {
					$(field).closest('.scheduled_stop').find('[name=type_1]').prop('checked',false).filter(function() { return this.value != 'warehouse' }).first().prop('checked',true);
				} else if($(field).data('set-time') != '' && $(field).data('set-time') != undefined) {
					$(field).closest('.scheduled_stop').find('[name=to_do_start_time]').val($(field).find('option:selected').data('set-time')).change();
				}
			}
			if($(field).attr('id') == 'assigned_equipment') {
				if(!confirm('Changing the Equipment for this Work Order will attempt to find an Equipment Assignment for the date set in this Work Order. If found, the details in this Work Order will be updated to match the Equipment Assignment. Press OK to continue.')) {
					return;
				}
			}
            if(id_num > 0) {
                doneSaving();
            }
			$.ajax({
				url: 'ticket_ajax_all.php?action=update_fields'+($('[name=no_time_sheet]').val() > 0 ? '&time_sheet=none' : ''),
				method: 'POST',
				data: {
					table: table_name,
					field: field_name,
					value: save_value,
					id: id_num,
					id_field: $(field).data('id-field'),
					ticketid: current_ticketid,
					date: $(field).data('date'),
					type: data_type,
					type_field: $(field).data('type-field'),
					append_note: $(field).closest('[data-append-note]').data('attach'),
					attach: $(field).data('attach'),
					attach_field: $(field).data('attach-field'),
					detail: $(field).data('detail'),
					detail_field: $(field).data('detail-field'),
					auto_checkin: $(field).data('auto-checkin'),
					auto_checkout: $(field).data('auto-checkout'),
					manually_set: $(field).data('manual'),
					manual_field: $(field).data('manual-field'),
					one_time: $(field).data('one-time'),
					category: $(field).data('category'),
					tile_name: tile_name,
					auto_create_unscheduled: $('[name="auto_create_unscheduled"]').val(),
					track_timesheet: $(field).data('track-timesheet'),
					sync_recurring_data: $('#sync_recurrences').val(),
					no_toggle_off: $(field).data('no-toggle-off')
				},
				success: function(response) {
					updateTicketLabel();
					if(field_name == 'status' && response == 'created_unscheduled_stop') {
						if($(field).closest('.tab-section').prop('id') != undefined && $(field).closest('.tab-section').prop('id').indexOf('customer_notes') == -1) {
	                        reloadTab('ticket_customer_notes');
	                    }
						reload_delivery();
					} else if(field_name == 'status') {
						if($(field).closest('.tab-section').prop('id') != undefined && $(field).closest('.tab-section').prop('id').indexOf('customer_notes') == -1) {
	                        reloadTab('ticket_customer_notes');
	                    }
                    } else if(table_name == 'ticket_attached' && field_name == 'piece_type') {
						var i = 1;
						$('#tab_section_ticket_inventory_general .multi-block h4').each(function() {
							var val = $(this).closest('.multi-block[data-type=general]').find('[name=piece_type]').val()
							$(this).text('Shipment Piece '+(i++)+(val != '' ? ': '+val : ''));
						});
						if(data_type == 'inventory_general') {
							var total_count = $('[name=piece_type][data-type=inventory_general]').filter(function() { return $(this).find('option:selected').filter(function() { return this.value != ''; }).length > 0 }).length;
							$('[name=total_shipment_count]').val(total_count);
							$('[name=total_shipment_count]').closest('div').find('span').remove();
							if(total_count != $('[name=qty][data-type=inventory_shipment]').val() * 1) {
								$('[name=total_shipment_count]').after('<span class="text-red">The shipment count was '+$('[name=qty][data-type=inventory_shipment]').val()+'</span>');
							}
						}
					} else if(table_name == 'ticket_attached' && field_name == 'weight' && data_type == 'inventory_general') {
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					}
                    if(field_name == 'status' && table_name == 'tickts') {
                    	if(status_value != undefined) {
	                        status_reload = status_value != field.value;
                    	}
                    }
					if(response > 0) {
						if(table_name == 'contacts' && field_name == 'site_name') {
							$(field).closest('.site_group').find('[name=siteid],[name="siteid[]"]').find('option[value="MANUAL"]').prop('selected', false);
							$(field).closest('.site_group').find('[name=siteid],[name="siteid[]"]').append('<option selected data-police="911" value="'+response+'">'+save_value+'</option>').trigger('change.select2').change();
						} else if(block.length > 0 && table_name != 'tickets' && data_type != undefined) {
							block.find('[data-table='+table_name+'][data-type='+data_type+']').data('id',response);
						} else if(block.length > 0 && table_name != 'tickets') {
							block.find('[data-table='+table_name+']').data('id',response);
							if(table_name == 'inventory' && field_name != 'item_id') {
								block.find('[name=item_id]').val(response).change();
							}
						} else if(id_num > 0 && (field_name == 'arrived' || field_name == 'completed')) {
							$('#collapse_summary,#tab_section_ticket_summary').find('[name=hours_tracked][data-id='+id_num+']').val(Number(response).toFixed(2));
						} else if(table_name == 'ticket_schedule' && (field_name == 'vendor' || field_name == 'carrier' || field_name == 'warehouse_location') || table_name == 'tickets' && (field_name == 'businessid' || field_name == 'clientid')) {
							if(table_name != 'tickets' && data_type != undefined) {
								$('[data-table='+table_name+'][data-type='+data_type+']').data('id',response);
							}
							if(table_name == 'ticket_schedule') {
								$(field).data('table','contacts').data('id',response).data('id-field','contactid').data('attach',$(field).data('category')).data('attach-field','category');
							}
							var selects = $('[data-category="'+$(field).data('category')+'"]').closest('.form-group').find('select');
							if(selects.length > 0) {
								$.ajax({
									url: 'ticket_ajax_all.php?action=get_category_list',
									method: 'POST',
									data: {
										category: $(field).data('category')
									},
									success: function(response) {
										selects.each(function() {
											var current = this.value;
											$(this).empty().append(response).val(current).trigger('select2.change');
										});
									}
								});
							}
						} else if(table_name == 'inventory') {
							$(field).closest('.multi-block').find('[name=item_id]').append('<option value='+response+'>New Inventory</option>').val(response).change();
						} else if(table_name != 'tickets' && table_name != '' && data_type != '') {
							$('[data-table="'+table_name+'"][data-type="'+data_type+'"]').filter(function() { return !($(this).data('id') > 0); }).data('id',response);
						}
						if(table_name == 'tickets') {
							if(data_type != '' && data_type != undefined) {
								$('[data-table=tickets][data-type='+data_type+']').data('id',response);
							} else {
								$('[data-table='+table_name+']').data('id',response);
							}
							if(ticketid == 'multi') {
								if(table_name == 'tickets') {
									ticketid_list.push(response);
									current_ticketid = response;
								}
							} else {
								ticketid = (table_name == 'tickets' ? response : ticketid);
								ticketid_list = [response];
								current_ticketid = ticketid;
							}
							$('[name=ticketid]').val(ticketid);
							window.history.replaceState('',"Software", window.location.href.replace('edit=0','edit='+ticketid));
							if($('[name="intake_alerted"]').val() == 1) {
								alert('Your '+$('[name="global_ticket_noun"]').val()+' ID is '+ticketid+'. Please keep this for your reference. You will not be able to edit this '+$('[name="global_ticket_noun"]').val()+' once you leave this page.');
								$('[name="intake_alerted"]').val(0);
							}
							$('.ticket_timer_div').show();
							if((field_name != 'projectid' && field_name != 'businessid' && $('[name=projectid]').val() > 0 && $('[name=projectid]').data('id') > 0) || $('[name=projectid]').length == 0) {
								$('[name=projectid]').filter(function() { return this.value > 0; }).first().change();
								$('select[name=businessid]').filter(function() { return this.value > 0; }).first().change();
								if(field_name != 'clientid')
									$('[name=clientid]').filter(function() { return this.value > 0; }).first().change();
								if(field_name != 'milestone_timeline')
									$('[name=milestone_timeline]').filter(function() { return this.value != ''; }).first().change();
								if(field_name != 'serviceid')
									$('[name=serviceid]').filter(function() { return this.value > 0; }).first().change();
								if(field_name != 'ticket_type')
									$('[name=ticket_type]').filter(function() { return this.value != ''; }).first().change();
							} else if(field_name != 'businessid' && field_name != 'clientid' && field_name != 'projectid') {
								$('select[name=businessid]').filter(function() { return this.value > 0; }).last().change();
								$('[name=clientid]').filter(function() { return this.value > 0; }).last().change();
								$('[name=serviceid]').filter(function() { return this.value > 0; }).first().change();
							}
							if($(field).data('id-field') == 'ticketid') {
								$('a').each(function() {
									$(this).prop('href',$(this).prop('href').replace('ticketid=','ticketid='+response).replace('ticketid%3D','ticketid%3D'+response));
								});
							}
						} else if(table_name == 'ticket_attached' && $(field).closest('.tab-section').attr('id') != undefined && $(field).closest('.tab-section').attr('id').substr(0,27) == 'tab_section_general_detail_') {
							$(field).closest('.tab-section').attr('id','tab_section_general_detail_'+response);
						}
						if(table_name == 'ticket_attached' && $(field).data('type') == 'Staff' && field_name != 'item_id') {
							$(field).closest('.multi-block').find('[name="item_id"]').change();
						}
						if(table_name == 'ticket_attached' && $(field).data('type') == 'Staff') {
							$(field).closest('.multi-block').find('[name="hours_travel"]').change();
						}
						if(table_name == 'mileage') {
							$(field).closest('.multi-block').find('[name="start"],[name="end"]').change();
						}
						if(table_name == 'ticket_attached' && $(field).data('type') == 'Multiple_Timesheet_Row') {
							var staff_attached_id = $(field).closest('.staff_block').find('[data-id]').first().data('id');
							$(field).closest('.staff-multi-time').find('[name="item_id"]').val(staff_attached_id).change();
						}
						if(table_name == 'ticket_schedule' && field_name != 'type' && $(field).closest('.tab-section').hasClass('scheduled_stop')) {
							$(field).closest('.scheduled_stop').find('[name="type"]').change();
						}
						// $('[name=to_do_date]').change();
						$('[name=contactid]').first().change();
						$('[name="status"]').change();
                        doneSaving();
					} else if(response.split('#*#')[0] == 'ERROR') {
						alert(response.split('#*#')[1]);
					} else if(response != '' && (field_name == 'signature' || field_name == 'witnessed')) {
						$(field).closest('.form-group').find('.img-div').show().find('img').after('<img src="'+response+'">').remove();
						$(field).closest('.form-group').find('.sig-div').hide();
					} else if(table_name == 'ticket_schedule' && field_name == 'map_link') {
						$(field).closest('div').find('a').remove();
						$(field).after('<a href="'+field.value+'">'+field.value+'</a>');
					}
					if(table_name == 'ticket_attached' && data_type == 'equipment' && (field_name == 'rate' || field_name == 'hours_estimated')) {
						var cost = block.find('[name=hours_estimated]').val() * block.find('[name=rate]').val();
						if(block.find('[name=cost]').val() != cost) {
							block.find('[name=cost]').val(cost).change();
						}
					} else if(table_name == 'ticket_attached' && field_name == 'hours_tracked' && $(field).closest('.summary').find('[name=checked_in]').val() != '') {
						var line = $(field).closest('.summary');
						var start = new Date('1969-12-31 '+line.find('[name=checked_in]').val());
						var diff = save_value.split(':');
						diff = diff[0] * 1 + diff[1] / 60;
						var end_date = new Date(start.valueOf + diff * 60 * 60 * 1000);
						line.find('[name=checked_out]').val((end_date.getHours() > 12 ? end_date.getHours() - 12 : (end_date.getHours() == 0 ? '12' : end_date.getHours()))+':'('00'+end_date.getMinutes()).slice('-2')+(end_date.getHours() >= 12 ? ' pm' : ' am'));
					} else if(table_name == 'ticket_attached' && field_name == 'checked_out' && $(field).closest('.summary').find('[name=checked_in]').val() != '') {
						var line = $(field).closest('.summary');
						var start = new Date('1969-12-31 '+line.find('[name=checked_in]').val());
						var end = new Date('1969-12-31 '+line.find('[name=checked_out]').val());
						var hours = end.getHours() - start.getHours();
						var minutes = end.getMinutes() - start.getMinutes();
						while(minutes > 60) {
							hours++;
							minutes -= 60;
						}
						line.find('[name=hours_tracked]').val(hours+':'+('00'+minutes).slice(-2));
					}
					if(table_name == 'ticket_attached' && data_type != 'medication' && (response > 0 || field_name == 'item_id')) {
						if(typeof still_reloading_tab != 'undefined') {
							clearInterval(still_reloading_tab);
						}
						reload_checkin($(field).closest('.tab-section'));
						reload_summary();
					} else if(field_name == 'sign_off_signature') {
						$.ajax({
							async: false,
							url: '../Ticket/ticket_ajax_all.php?action=complete&ticketid='+current_ticketid+($('[name=complete_force]').val() > 0 || finishing_ticket ? '&force=true' : ''),
							dataType: 'json',
							success: function(response) {
								alert(response.message);
								if(response.success == 'false') {
									$('.sign_off_click').off(sign_off_complete).click(sign_off_complete);
									$('.force_sign_off_click').off(sign_off_complete_force).click(sign_off_complete_force);
									$('[name=complete_force]').val(0);
								} else {
									window.location.replace(decodeURIComponent(from_url));
								}
							}
						});
					} else if(field_name == 'ticket_type' && $('[name=ticket_type][data-initial]').data('initial') != save_value) {
						if($('#calendar_view').val() == 'true' && $('#new_ticket_from_calendar').val() == '1') {
							window.parent.$('[name="calendar_iframe"]').off('load');
							window.parent.$('[name="calendar_iframe"]').attr('src','../blank_loading_page.php');
							window.parent.overlayIFrameSlider('../Ticket/index.php?from='+from_url+new_ticket_url+'&ticketid='+current_ticketid+'&edit='+current_ticketid+'&calendar_view=true&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name));
						} else if($('#calendar_view').val() == 'true') {
							var new_url = '../Ticket/index.php?from='+from_url+new_ticket_url+'&ticketid='+current_ticketid+'&edit='+current_ticketid+'&calendar_view=true&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
							setTimeout(function() { reloadSidebarOnSaved(new_url); }, 250);
						} else {
							var new_url = '?from='+from_url+new_ticket_url+'&ticketid='+current_ticketid+'&edit='+current_ticketid+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
							setTimeout(function() { reloadOnSaved(new_url); }, 250);
						}
					} else if(field_name == 'piece_num') {
						$(field).closest('.form-group').find('.setMultiDims').change();
					} else if(field_name == 'serviceid') {
						$('[name=service_qty]').first().change();
					} else if(field_name == 'piece_work') {
						$.ajax({
							url: '../Ticket/ticket_ajax_all.php?fill=add_edit_project&ticketid='+current_ticketid,
							success: function(response1) {
								$('[name=projectid]').val(response1);

								$.ajax({
									url: '../Ticket/ticket_ajax_all.php?fill=project_paths&projectid='+response1,
									success: function(response) {
										var milestone = $('[name=milestone_timeline]').val();
										$('[name=milestone_timeline]').html(response).val(milestone).trigger('change.select2');
									}
								});

							}
						});
					} else if(field_name == 'projectid') {
						$.ajax({
							url: '../Ticket/ticket_ajax_all.php?fill=project_paths&projectid='+save_value,
							success: function(response) {
								var milestone = $('[name=milestone_timeline]').val();
								$('[name=milestone_timeline]').html(response).val(milestone).trigger('change.select2');
							}
						});
					} else if($(field).data('set-address') != '' && $(field).data('set-address') != undefined || (field.name == 'businessid' && $('[data-table=ticket_schedule][name=address]').length > 0 && set_business_delivery)) {
						// Get the address attached to the business and update the delivery address with it
						var target = $(field).data('set-address');
						if(target == '' || target == undefined) {
							target = 'data-table=ticket_schedule';
						}
						if(!($('['+target+'][name=address]').first().data('id') > 0) && $('['+target+'][name=address]').first().data('getting-id') != 'true') {
							$('['+target+'][name=address]').first().data('getting-id','true').change();
						}
						var setAddressInterval = setInterval(function() {
							if($('['+target+'][name=address]').first().data('id') > 0) {
								$('['+target+'][name=address]').first().data('getting-id','');
								clearInterval(setAddressInterval);
								$.ajax({
									url: '../Ticket/ticket_ajax_all.php?action=get_address',
									method: 'POST',
									data: {
										contactid: field.value,
										address: 'shipping'
									},
									dataType: 'json',
									success: function(response) {
										if(response != null) {
											$('['+target+'][name=address]').first().val(response.address != '' && response.address != null && response.address != undefined ? response.address : response.street).change();
											$('['+target+'][name=city]').first().val(response.city).change();
											$('['+target+'][name=postal_code]').first().val(response.postal).change();
											$('['+target+'][name=province]').first().val(response.province).change();
											$('['+target+'][name=country]').first().val(response.country).change();
											$('['+target+'][name=map_link]').first().val(response.google_link).change();
										}
									}
								});
							}
						}, 250);
					}
					if(field_name == 'businessid' && table_name == 'tickets' && save_value != current_business) {
                        current_business = save_value;
                        $.get('../Ticket/ticket_ajax_all.php', {
                            action: 'business_services',
                            business: $('select[name=businessid]').val()
                        }, function(response) {
                            if(reload_services) {
                                $('[name=serviceid]').empty().html(response);
                            }
                            $.get('../Ticket/ticket_ajax_all.php', {
                                contactid: $('select[name=businessid]').val(),
                                action: 'list_customer_service_templates'
                            }, function(response) {
                                $('[name=default_services]').val(response);
                                if($('[name=default_services]').length > 0) {
                                    $('[name=default_services]').each(function() {
                                        var services = this.value.trim(',').split(',');
                                        for(var i = 0; i < services.length; i++) {
                                            $('.scheduled_stop [name=serviceid]').last().each(function() {
                                                if(services[i] > 0) {
                                                    if(i > 0) {
                                                        addMulti(this);
                                                    }
                                                    $(this).val(services[i]).change();
                                                }
                                            });
                                        }
                                    });
                                } else {
                                    var services = response.trim(',').split(',');
                                    for(var i = 0; i < services.length; i++) {
                                        $('.serviceid').last().each(function() {
                                            if(services[i] > 0) {
                                                if(i > 0) {
                                                    addMulti(this);
                                                }
                                                $(this).val(services[i]).change();
                                            }
                                        });
                                    }
                                }
                            });
                        });
                    } else if(field_name == 'serviceid' || field_name == 'service_qty') {
						clearTimeout(ticket_reloading_service_checklist);
						ticket_reloading_service_checklist = setTimeout(function() {
							reload_service_checklist();
						},1000);
					}
					if(data_type == 'inventory_general' && (response > 0 || field_name == 'piece_type')) {
						reload_inventory();
					} else if(table_name == 'ticket_schedule' && field_name == 'to_do_start_time') {
						var deliver_window = $(field).data('window');
						if(deliver_window > 0) {
							if($(field).val() != undefined && $(field).val() != '') {
								$(field).closest('.scheduled_stop').find('[name=start_available]').val($(field).val()).change();
								$(field).closest('.scheduled_stop').find('[name=end_available]').val(addTimes($(field).val(), deliver_window)).change();
							}
						}
					} else if(data_type == 'inventory_shipment' && field_name == 'weight_units') {
						setInventoryWeights(save_value);
					} else if(data_type == 'inventory_shipment' && field_name == 'qty') {
						multiPieces(field);
						var total_count = $('[name=piece_type][data-type=inventory_general]').filter(function() { return $(this).find('option:selected').filter(function() { return this.value != ''; }).length > 0 }).length;
						$('[name=total_shipment_count]').val(total_count);
						$('[name=total_shipment_count]').closest('div').find('span').remove();
						if(total_count != $('[name=qty][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_count]').after('<span class="text-red">The shipment count was '+$('[name=qty][data-type=inventory_shipment]').val()+'</span>');
						}
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					} else if(data_type == 'inventory_shipment' && field_name == 'weight') {
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					}
					if(field_name == 'start_time' || field_name == 'end_time' || field_name == 'member_start_time' || field_name == 'member_end_time' || (field_name == 'hours_set' && !$(field).closest('table').hasClass('summary_table'))) {
						reload_summary();
					} else if(field_name == 'projectid') {
						reload_project_documents();
					} else if(field_name == 'businessid' || field_name == 'clientid') {
						reload_contact_documents();
					}
					if(field_name != 'heading') {
						setHeading();
					}
					if(['serviceid','service_qty'].indexOf(field_name) >= 0) {
						reload_billing();
					} else if('ticket_attached' == table_name) {
						reload_billing();
					} else if(['address','city','postal_code','sort'].indexOf(field_name) >= 0) {
						$('.route_map_div').load('add_ticket_maps.php?map_action=pickup_delivery&ticketid='+ticketid);
					}
					if(field_name == 'contactid') {
						clearTimeout(ticket_reloading_service_checklist);
						ticket_reloading_service_checklist = setTimeout(function() {
							reload_service_checklist();
						},1000);
					}
					if(field_name == 'clientid' && typeof limitServiceCategory == 'function') {
						limitServiceCategory();
					}
					if(table_name == 'ticket_attached' && field_name == 'arrived' && $('[name="reload_checkout_on_checkin"]').val() != undefined && $('[name="reload_checkout_on_checkin"]').val() == 1) {
						if(typeof still_reloading_tab != 'undefined') {
							clearInterval(still_reloading_tab);
						}
						reload_checkin($(field).closest('.tab-section'));
					}
					if(table_name == 'ticket_attached' && field_name == 'arrived' && $('[name="reload_checklist_on_checkin"]').val() != undefined && $('[name="reload_checklist_on_checkin"]').val() == 1) {
						reload_service_checklist();
					}
					if(table_name == 'ticket_schedule' && field_name == 'status' && $(field).data('id') > 0) {
						$('[name="status"][data-table="ticket_schedule"][data-id="'+$(field).data('id')+'"]').val(save_value).trigger('change.select2');
					}
					if($('[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').first().val() != undefined && $('[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').first().val() > 0 && !($('[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').first().data('id') > 0)) {
						$('[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').first().change();
					}
					if(table_name == 'mileage' && field_name == 'mileage') {
						$(field).closest('.multi-block').find('[name="double_mileage"]').val(parseFloat(save_value)*2).change();
					}
					if(table_name == 'ticket_attached' && field_name == 'completed') {
						if($(field).data('exit-ticket') != undefined && $(field).data('exit-ticket') == 1) {
							if($(field).data('iframe') != undefined && $(field).data('iframe') == 1) {
								window.top.$('iframe').attr('src','../blank_loading_page.php');
								window.location.replace('../blank_loading_page.php');
							} else {
								window.location.replace($(field).data('back-url'));
							}
						} else if($(field).data('iframe') != undefined && $(field).data('iframe') == 1) {
							window.location.replace('../blank_loading_page.php');
						}
					}
					if(field.name == 'to_do_date') {
						filterStaffNoShift();
						checkStaffShifts().success(function(response) {
							var response = JSON.parse(response);
							if(response.success == false) {
								alert(response.message);
							}
						});
          }
					if((table_name == 'tickets' && field_name == 'purchase_order') || (table_name == 'ticket_attached' && field_name == 'po_num')) {
						reload_po_num_dropdown();
					}
					if(field_name == 'clientid' && typeof filterSitesByClient == 'function') {
						filterSitesByClient();
					}
					if(table_name == 'ticket_attached' && field_name == 'hours_set' && $(field).data('type') == 'Multiple_Timesheet_Row' && $(field).closest('.tab-section') != undefined && $(field).closest('.tab-section').prop('id') == 'tab_section_ticket_summary') {
						$('.tab-section:not(#tab_section_ticket_summary').find('[name="hours_set"][data-table="ticket_attached"][data-type="Multiple_Timesheet_Row"][data-id="'+$(field).data('id')+'"]').val(field.value);
					}
                    if(!(id_num > 0) && !(response > 0)) {
                        doneSaving();
                    }
				}
			});
		} else {
			doneSaving();
		}
	});
}

function validate_address(address, block) {
    if(delay_notify) {
        setTimeout(function() {
            validate_address(address, block);
        }, 1000);
        return;
    }
    delay_notify = true;
    if(address.join('') != '' && (address[0] != block.find('[name=address]').val() || address[1] != block.find('[name=city]').val() || address[2] != block.find('[name=postal_code]').val()) && (use_google_suggest === true || confirm('We suggest the following corrections to your address: '+address.join(', ')+'. Would you like to use this suggestion? Using the current address may fail to display in Google Maps.'))) {
        block.find('[name=address]').val(address[0]).change();
        block.find('[name=city]').val(address[1]).change();
        block.find('[name=postal_code]').val(address[2]).change();
        use_google_suggest = true;
        suggest_from_google = false;
        setTimeout(function() {
            suggest_from_google = true;
        }, 15000);
    } else if(address.join('') == '' && use_google_suggest != 'no_update') {
        alert('The address provided may not be valid. It will not be found in Google Maps.');
        use_google_suggest = 'no_update';
    }
    delay_notify = false;
}

function reloadOnSaved(url) {
	if(current_fields.length == 0) {
		no_verify = true;
		window.location.replace(url);
	} else {
		setTimeout(function() { reloadOnSaved(url); }, 250);
	}
}
function reloadSidebarOnSaved(url) {
	if(current_fields.length == 0) {
		no_verify = true;
		window.parent.$('[name="calendar_iframe"]').off('load');
		window.parent.$('[name="calendar_iframe"]').attr('src','../blank_loading_page.php');
		window.parent.overlayIFrameSlider(url);
	} else {
		setTimeout(function() { reloadSidebarOnSaved(url); }, 250);
	}
}
function saveMethod(field) {
	clearTimeout(ticket_lock_interval);
	// ticket_lock_interval = setTimeout(releaseLock, 300000);
	if(field.currentTarget != undefined) {
		field = field.currentTarget;
	}
	if(ticket_wait && !($(field).data('id') > 0)) {
		setTimeout(function() { $(field).change(); }, 250);
		return;
	}
	if($(field).data('table') != 'tickets' && (ticketid == '' || ticketid == 0)) {
		var ticketTypeInterval = setInterval(function() {
			if($('#new_ticket_from_calendar').val() != '1' || $('#new_ticket_from_calendar') == undefined) {
				clearInterval(ticketTypeInterval);
				$('[data-table=tickets]').not('[name=ticket_type]').first().change();
				setTimeout(function() { $(field).change(); }, 250);
				return;
			}
		}, 250);
	} else if($(field).data('table') == 'tickets' && field.name != 'ticket_type' && (ticketid == '' || ticketid == 0) && $('[name=ticket_type]').length > 0) {
		var ticketTypeInterval = setInterval(function() {
			if($('#new_ticket_from_calendar').val() != '1' || $('#new_ticket_from_calendar') == undefined) {
				clearInterval(ticketTypeInterval);
				$('[name=ticket_type]').change();
				setTimeout(function() { $(field).change(); }, 250);
				return;
			}
		}, 250);
	}
	if($('#new_ticket_from_calendar').val() == '1') {
		$('#new_ticket_from_calendar').val('0');
		saveNewTicketFromCalendar(field);
		return;
	}
	if($('#customer_rate_services').val() == '1') {
		$('#customer_rate_services').val('0');
		$('[name=billing_discount_type]').filter(function() { return this.value != ''; }).first().change();
		$('[name=billing_discount]').filter(function() { return this.value != ''; }).first().change();
		return;
	}
	ticket_wait = true;
	var ticket = ticketid;
	if(ticket > 0) {
		ticketid_list = [ticket];
	} else if(ticket == '' || ticket == 0) {
		ticketid_list = [0];
	}
	ticketid_list.forEach(function(ticket) {
		var current_ticketid = ticket;
		if(field.type == 'file' && field.name == 'attached_image') {
			var file = new FormData();
			file.append('file', field.files[0]);
			file.append('ticket',current_ticketid);
			field_name = field.name;
			if(field_name.split('__')[1] > 0) {
				field_name = field_name.split('__')[0];
			}
			statusSaving();
			$.ajax({
				url: 'ticket_ajax_all.php?action=attached_image',
				method: 'POST',
				processData: false,
				contentType: false,
				data: file,
				success: function(response) {
					statusDone(field);
					reload_attached_image();
					ticket_wait = false;
				}
			})
		} else if(field.type == 'file') {
			var files = new FormData();
			for(var i = 0; i < field.files.length; i++) {
				files.append('files[]',field.files[i]);
			}
			files.append('table',$(field).data('table'));
			files.append('table_id',$(field).data('id'));
			files.append('field',field.name);
			files.append('ticket',current_ticketid);
			field_name = field.name;
			field.value = '';
			statusSaving();
			$.ajax({
				url: 'ticket_ajax_all.php?action=add_file',
				method: 'POST',
				processData: false,
				contentType: false,
				data: files,
				success: function(response) {
					statusDone(field);
					reload_documents();
					ticket_wait = false;
				}
			});
		} else if(field.value == 'ADD_NEW') {
			overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit=new&category='+$(field).data('category')+(field.name == 'clientid' ? '&businessid='+$('select[name=businessid]').val() : ''), '75%', true, true);
			iframe_contactid = 0;
			var this_category = $(field).data('category');
			var iframe_check = setInterval(function() {
				if(!$('.iframe_overlay iframe').is(':visible')) {
					if(iframe_contactid > 0) {
						$.post('ticket_ajax_all.php?action=get_category_list', { category: this_category }, function(response) {
							$(field).html(response);
							$(field).append('<option value="ADD_NEW">Add New '+this_category+'</option>');
							$(field).append('<option value="ADD_NEW">One Time '+this_category+'</option>');
							$(field).trigger('change.select2');
							$(field).val(iframe_contactid).change();
						});
					}
					clearInterval(iframe_check);
				} else if(!(iframe_contactid > 0)) {
					iframe_contactid = $($('.iframe_overlay iframe').get(0).contentDocument).find('[name=contactid]').val();
				}
			}, 500);
			ticket_wait = false;
		} else if(field.value != 'MANUAL') {
			var block = $(field).closest('.multi-block,.scheduled_stop');
			var id_num = $(field).data('id');
			var table_name = $(field).data('table');
			var data_type = $(field).data('type');
			var save_value = field.value;
			var field_name = field.name.replace('[]','');
			if(field_name.split('__')[1] > 0) {
				field_name = field_name.split('__')[0];
			}
			if(table_name == 'tickets' && $(field).data('id-field') == 'ticketid' && $.inArray(field_name,['pickup_name','pickup_address','pickup_city','pickup_postal_code','pickup_link','pickup_volume','to_do_date','to_do_start_time','pickup_order']) < 0) {
				id_num = current_ticketid;
			}
			if(field.name.substr(-2) == '[]' && $(field).find('option').length > 0) {
				var value = [];
				$(field).find('option:selected').each(function() {
					value.push(this.value);
				});
				save_value = ','+value.join(',')+',';
				if(field_name == 'contactid') {
					value.forEach(function(id) {
						if($('#collapse_staff,#tab_section_ticket_staff_list').length > 0 && $('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).val() == id; }).length == 0) {
							if($('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).find('option:selected').length == 0; }).length == 0) {
								addMulti($('#collapse_staff .multi-block img,#tab_section_ticket_staff_list .multi-block img'));
							}
							$('#collapse_staff [name=item_id][data-type=Staff],#tab_section_ticket_staff_list [name=item_id][data-type=Staff]').filter(function() { return $(field).find('option:selected').length == 0; }).last().val(id).trigger('change.select2').change();
						}
					});
				}
			} else if(field.name == 'other_ind') {
				var value = [];
				$('.individual_present').each(function() {
					var item = $(this).find('select[name=other_ind]').val();
					if(item == 'MANUAL') {
						item = $(this).find('input[name=other_ind]').val();
					}
					value.push(item);
				});
				save_value = value.join('#*#');
			} else if($(field).is('[data-concat]')) {
				var value = [];
				$('[name='+field.name+'][data-concat="'+$(field).data('concat')+'"]').filter(function() { return $(this).data('id') == $(field).data('id'); }).each(function() {
					if(this.type != 'checkbox' || this.checked) {
						value.push(this.value);
					}
				});
				if(value.length == 0 && table_name == 'tickets') {
					$('[name='+field.name+'][data-table=tickets]').each(function() {
						if(this.type != 'checkbox' || this.checked) {
							value.push(this.value);
						}
					});
				}
				save_value = value.join($(field).data('concat'));
			} else if(field.type == 'checkbox' && !field.checked) {
				save_value = '';
			}
			if((field_name == 'item_id' || field_name == 'deleted') && $(field).data('type') == 'Staff') {
				var staff_ids = [];
				$('#collapse_staff [name=item_id] option:selected,#tab_section_ticket_staff_list [name=item_id] option:selected').each(function() {
					if(this.value > 0) {
						staff_ids.push(this.value);
					}
				});
				$('[name=contactid]').val(','+staff_ids.join(',')+',').change();
			} else if(['address','city','postal_code','sort'].indexOf(field_name) >= 0) {
				$.ajax({
					url: 'add_ticket_maps.php?map_action=pickup_delivery&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
					method: 'POST',
					data: {
						ticketid: ticketid
					},
					success: function(response) {
						$('.route_map_div').html(response);
					}
				});
			} else if(['serviceid','service_qty'].indexOf(field_name) >= 0) {
				reload_billing();
			}
			if($(field).attr('id') == 'assigned_equipment') {
				if(!confirm('Changing the Equipment for this Work Order will attempt to find an Equipment Assignment for the date set in this Work Order. If found, the details in this Work Order will be updated to match the Equipment Assignment. Press OK to continue.')) {
					return;
				}
			}
			statusSaving();
			$.ajax({
				url: 'ticket_ajax_all.php?action=update_fields'+($('[name=no_time_sheet]').val() > 0 ? '&time_sheet=none' : ''),
				method: 'POST',
				data: {
					table: table_name,
					field: field_name,
					value: save_value,
					id: id_num,
					id_field: $(field).data('id-field'),
					ticketid: current_ticketid,
					type: data_type,
					type_field: $(field).data('type-field'),
					append_note: $(field).closest('[data-append-note]').data('attach'),
					attach: $(field).data('attach'),
					attach_field: $(field).data('attach-field'),
					detail: $(field).data('detail'),
					detail_field: $(field).data('detail-field'),
					auto_checkin: $(field).data('auto-checkin'),
					auto_checkout: $(field).data('auto-checkout'),
					manually_set: $(field).data('manual'),
					manual_field: $(field).data('manual-field'),
					one_time: $(field).data('one-time'),
					category: $(field).data('category'),
					tile_name: tile_name,
					tile_group: tile_group,
					auto_create_unscheduled: $('[name="auto_create_unscheduled"]').val(),
					track_timesheet: $(field).data('track-timesheet'),
					sync_recurring_data: $('#sync_recurrences').val()
				},
				success: function(response) {console.log(response);
					updateTicketLabel();
					if(field_name == 'status' && response == 'created_unscheduled_stop') {
						reload_delivery();
					} else if(table_name == 'ticket_attached' && field_name == 'piece_type') {
						var i = 1;
						$('#tab_section_ticket_inventory_general .multi-block h4').each(function() {
							var val = $(this).closest('.multi-block[data-type=general]').find('[name=piece_type]').val()
							$(this).text('Shipment Piece '+(i++)+(val != '' ? ': '+val : ''));
						});
						if(data_type == 'inventory_general') {
							var total_count = $('[name=piece_type][data-type=inventory_general]').filter(function() { return $(this).find('option:selected').filter(function() { return this.value != ''; }).length > 0 }).length;
							$('[name=total_shipment_count]').val(total_count);
							$('[name=total_shipment_count]').closest('div').find('span').remove();
							if(total_count != $('[name=qty][data-type=inventory_shipment]').val() * 1) {
								$('[name=total_shipment_count]').after('<span class="text-red">The shipment count was '+$('[name=qty][data-type=inventory_shipment]').val()+'</span>');
							}
						}
					} else if(table_name == 'ticket_attached' && field_name == 'weight' && data_type == 'inventory_general') {
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					}
					if(response > 0) {
						if(table_name == 'contacts' && field_name == 'site_name') {
							$('[name=siteid],[name="siteid[]"]').find('option[value="MANUAL"]').prop('selected', false);
							$('[name=siteid],[name="siteid[]"]').append('<option selected data-police="911" value="'+response+'">'+save_value+'</option>').trigger('change.select2').change();
						} else if(block.length > 0 && table_name != 'tickets' && data_type != undefined) {
							block.find('[data-table='+table_name+'][data-type='+data_type+']').data('id',response);
						} else if(block.length > 0 && table_name != 'tickets') {
							block.find('[data-table='+table_name+']').data('id',response);
							if(table_name == 'inventory' && field_name != 'item_id') {
								block.find('[name=item_id]').val(response).change();
							}
						} else if(id_num > 0 && (field_name == 'arrived' || field_name == 'completed')) {
							$('#collapse_summary,#tab_section_ticket_summary').find('[name=hours_tracked][data-id='+id_num+']').val(Number(response).toFixed(2));
						} else if(table_name == 'ticket_schedule' && (field_name == 'vendor' || field_name == 'carrier' || field_name == 'warehouse_location') || table_name == 'tickets' && (field_name == 'businessid' || field_name == 'clientid')) {
							if(table_name != 'tickets' && data_type != undefined) {
								$('[data-table='+table_name+'][data-type='+data_type+']').data('id',response);
							}
							if(table_name == 'ticket_schedule') {
								$(field).data('table','contacts').data('id',response).data('id-field','contactid').data('attach',$(field).data('category')).data('attach-field','category');
							}
							var selects = $('[data-category="'+$(field).data('category')+'"]').closest('.form-group').find('select');
							if(selects.length > 0) {
								$.ajax({
									url: 'ticket_ajax_all.php?action=get_category_list',
									method: 'POST',
									data: {
										category: $(field).data('category')
									},
									success: function(response) {
										selects.each(function() {
											var current = this.value;
											$(this).empty().append(response).val(current).trigger('select2.change');
										});
									}
								});
							}
						} else if(table_name == 'inventory') {
							$(field).closest('.multi-block').find('[name=item_id]').append('<option value='+response+'>New Inventory</option>').val(response).change();
						} else if(table_name != 'tickets' && table_name != '' && data_type != '') {
							$('[data-table="'+table_name+'"][data-type="'+data_type+'"]').filter(function() { return !($(this).data('id') > 0); }).data('id',response);
						}
						if(table_name == 'tickets') {
							if(data_type != '' && data_type != undefined) {
								$('[data-table=tickets][data-type='+data_type+']').data('id',response);
							} else {
								$('[data-table='+table_name+']').data('id',response);
							}
							if(ticketid == 'multi') {
								if(table_name == 'tickets') {
									ticketid_list.push(response);
									current_ticketid = response;
								}
							} else {
								ticketid = (table_name == 'tickets' ? response : ticketid);
								ticketid_list = [response];
								current_ticketid = ticketid;
							}
							$('[name=ticketid]').val(ticketid);
							window.history.replaceState('',"Software", window.location.href.replace('edit=0','edit='+ticketid));
							$('.ticket_timer_div').show();
							if((field_name != 'projectid' && field_name != 'businessid' && $('[name=projectid]').val() > 0 && $('[name=projectid]').data('id') > 0) || $('[name=projectid]').length == 0) {
								$('[name=projectid]').filter(function() { return this.value != ''; }).change();
								$('select[name=businessid]').filter(function() { return this.value != ''; }).change();
								$('[name=clientid]').filter(function() { return this.value != ''; }).change();
								$('[name=milestone_timeline]').filter(function() { return this.value != ''; }).change();
								$('[name=ticket_type]').filter(function() { return this.value != ''; }).change();
								$('[name=serviceid]').filter(function() { return this.value != ''; }).first().change();
							} else if(field_name != 'businessid' && field_name != 'clientid' && field_name != 'projectid') {
								$('select[name=businessid]').filter(function() { return this.value != ''; }).last().change();
								$('[name=clientid]').filter(function() { return this.value != ''; }).last().change();
								$('[name=serviceid]').filter(function() { return this.value != ''; }).first().change();
							}
							if($(field).data('id-field') == 'ticketid') {
								$('a').each(function() {
									$(this).prop('href',$(this).prop('href').replace('ticketid=','ticketid='+response).replace('ticketid%3D','ticketid%3D'+response));
								});
							}
						} else if(table_name == 'ticket_attached' && $(field).closest('.tab-section').attr('id') != undefined) {
							if($(field).closest('.tab-section').attr('id').substr(0,27) == 'tab_section_general_detail_') {
								$(field).closest('.tab-section').attr('id','tab_section_general_detail_'+response);
							}
						}
					} else if(response.split('#*#')[0] == 'ERROR') {
						alert(response.split('#*#')[1]);
					} else if(response != '' && (field_name == 'signature' || field_name == 'witnessed')) {
						$(field).closest('.form-group').find('.img-div').show().find('img').after('<img src="'+response+'">').remove();
						$(field).closest('.form-group').find('.sig-div').hide();
					}
					if(table_name == 'ticket_attached' && data_type == 'equipment' && (field_name == 'rate' || field_name == 'hours_estimated')) {
						var cost = block.find('[name=hours_estimated]').val() * block.find('[name=rate]').val();
						if(block.find('[name=cost]').val() != cost) {
							block.find('[name=cost]').val(cost).change();
						}
					} else if(table_name == 'ticket_attached' && field_name == 'hours_tracked' && $(field).closest('.summary').find('[name=checked_in]').val() != '') {
						var line = $(field).closest('.summary');
						var start = new Date('1969-12-31 '+line.find('[name=checked_in]').val());
						var diff = save_value.split(':');
						diff = diff[0] * 1 + diff[1] / 60;
						var end_date = new Date(start.valueOf + diff * 60 * 60 * 1000);
						line.find('[name=checked_out]').val((end_date.getHours() > 12 ? end_date.getHours() - 12 : (end_date.getHours() == 0 ? '12' : end_date.getHours()))+':'('00'+end_date.getMinutes()).slice('-2')+(end_date.getHours() >= 12 ? ' pm' : ' am'));
					} else if(table_name == 'ticket_attached' && field_name == 'checked_out' && $(field).closest('.summary').find('[name=checked_in]').val() != '') {
						var line = $(field).closest('.summary');
						var start = new Date('1969-12-31 '+line.find('[name=checked_in]').val());
						var end = new Date('1969-12-31 '+line.find('[name=checked_out]').val());
						var hours = end.getHours() - start.getHours();
						var minutes = end.getMinutes() - start.getMinutes();
						while(minutes > 60) {
							hours++;
							minutes -= 60;
						}
						line.find('[name=hours_tracked]').val(hours+':'+('00'+minutes).slice(-2));
					} else if(table_name == 'ticket_attached' && field_name == 'map_link') {
						$(field).closest('div').find('a').remove();
						$(field).after('<a href="'+field.value+'">'+field.value+'</a>');
					}
					if(table_name == 'ticket_attached' && data_type != 'medication' && (response > 0 || field_name == 'item_id')) {
						if(typeof still_reloading_tab != 'undefined') {
							clearInterval(still_reloading_tab);
						}
						reload_checkin($(field).closest('.tab-section'));
						reload_summary();
					} else if(field_name == 'sign_off_signature') {
						$.ajax({
							async: false,
							url: '../Ticket/ticket_ajax_all.php?action=complete&ticketid='+current_ticketid+($('[name=complete_force]').val() > 0 ? '&force=true' : ''),
							dataType: 'json',
							success: function(response) {
								alert(response.message);
								if(response.success == 'false') {
									$('.sign_off_click').off(sign_off_complete).click(sign_off_complete);
									$('.force_sign_off_click').off(sign_off_complete_force).click(sign_off_complete_force);
									$('[name=complete_force]').val(0);
								} else {
									window.location.replace(decodeURIComponent(from_url));
								}
							}
						});
					} else if(field_name == 'ticket_type' && $('[name=ticket_type][data-initial]').data('initial') != save_value) {
						if($('#calendar_view').val() == 'true') {
							window.parent.$('[name="calendar_iframe"]').off('load');
							window.parent.$('[name="calendar_iframe"]').attr('src','../blank_loading_page.php');
							window.parent.overlayIFrameSlider('../Ticket/index.php?from='+from_url+new_ticket_url+'&ticketid='+current_ticketid+'&edit='+current_ticketid+'&calendar_view=true&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name));
						} else {
							no_verify = true;
							window.location.replace('?from='+from_url+new_ticket_url+'&ticketid='+current_ticketid+'&edit='+current_ticketid+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name));
						}
					} else if(field_name == 'piece_num') {
						$(field).closest('.form-group').find('.setMultiDims').change();
					} else if(field_name == 'serviceid') {
						$('[name=service_qty]').first().change();
					} else if(field_name == 'projectid') {
						$.ajax({
							url: '../Ticket/ticket_ajax_all.php?fill=project_paths&projectid='+save_value,
							success: function(response) {
								var milestone = $('[name=milestone_timeline]').val();
								$('[name=milestone_timeline]').html(response).val(milestone).trigger('change.select2');
							}
						});
					} else if($(field).data('set-address') != '' && $(field).data('set-address') != undefined || (field.name == 'businessid' && $('[data-table=ticket_schedule][name=address]').length > 0)) {
						// Get the address attached to the business and update the delivery address with it
						var target = $(field).data('set-address');
						if(target == '' || target == undefined) {
							target = 'data-table=ticket_schedule';
						}
						if(!($('['+target+'][name=address]').first().data('id') > 0) && $('['+target+'][name=address]').first().data('getting-id') != 'true') {
							$('['+target+'][name=address]').first().data('getting-id','true').change();
						}
						var setAddressInterval = setInterval(function() {
							if($('['+target+'][name=address]').first().data('id') > 0) {
								$('['+target+'][name=address]').first().data('getting-id','');
								clearInterval(setAddressInterval);
								$.ajax({
									url: '../Ticket/ticket_ajax_all.php?action=get_address',
									method: 'POST',
									data: {
										contactid: field.value,
										address: 'shipping'
									},
									dataType: 'json',
									success: function(response) {
										if(response != null && response.address != '') {
											$('['+target+'][name=address]').first().val(response.address != '' && response.address != null && response.address != undefined ? response.address : response.street).change();
											$('['+target+'][name=city]').first().val(response.city).change();
											$('['+target+'][name=postal_code]').first().val(response.postal).change();
											$('['+target+'][name=province]').first().val(response.province).change();
											$('['+target+'][name=country]').first().val(response.country).change();
											$('['+target+'][name=map_link]').first().val(response.google_link).change();
										}
									}
								});
							}
						}, 250);
					}
					if(field_name == 'serviceid' || field_name == 'service_qty') {
						clearTimeout(ticket_reloading_service_checklist);
						ticket_reloading_service_checklist = setTimeout(function() {
							reload_service_checklist();
						},1000);
					}
					if(ticketid_list.length <= 1 || current_ticketid == ticketid_list[ticketid_list.length - 1]) {
						ticket_wait = false;
						statusDone(field);
					}
					if(data_type == 'inventory_general' && (response > 0 || field_name == 'piece_type')) {
						reload_inventory();
					} else if(table_name == 'ticket_schedule' && field_name == 'to_do_start_time') {
						var deliver_window = $(field).data('window');
						if(deliver_window > 0) {
							if($(field).val() != undefined && $(field).val() != '') {
								$(field).closest('.scheduled_stop').find('[name=start_available]').val($(field).val()).change();
								$(field).closest('.scheduled_stop').find('[name=end_available]').val(addTimes($(field).val(), deliver_window)).change();
							}
						}
					} else if(data_type == 'inventory_shipment' && field_name == 'weight_units') {
						setInventoryWeights(save_value);
					} else if(data_type == 'inventory_shipment' && field_name == 'qty') {
						multiPieces(field);
						var total_count = $('[name=piece_type][data-type=inventory_general]').filter(function() { return $(this).find('option:selected').filter(function() { return this.value != ''; }).length > 0 }).length;
						$('[name=total_shipment_count]').val(total_count);
						$('[name=total_shipment_count]').closest('div').find('span').remove();
						if(total_count != $('[name=qty][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_count]').after('<span class="text-red">The shipment count was '+$('[name=qty][data-type=inventory_shipment]').val()+'</span>');
						}
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					} else if(data_type == 'inventory_shipment' && field_name == 'weight') {
						var total_weight = 0;
						$('[name=weight][data-type=inventory_general]:visible').each(function() {
							total_weight += this.value * 1;
						});
						$('[name=total_shipment_weight]').val(total_weight);
						$('[name=total_shipment_weight]').closest('div').find('span').remove();
						if(total_weight != $('[name=weight][data-type=inventory_shipment]').val() * 1) {
							$('[name=total_shipment_weight]').after('<span class="text-red">The shipment weight was '+$('[name=weight][data-type=inventory_shipment]').val()+'</span>');
						}
					}
					if(field_name != 'heading') {
						setHeading();
					}
					if(['serviceid','service_qty'].indexOf(field_name) >= 0) {
						reload_billing();
					}
					if(field_name == 'contactid') {
						clearTimeout(ticket_reloading_service_checklist);
						ticket_reloading_service_checklist = setTimeout(function() {
							reload_service_checklist();
						},1000);
					}
					if(field_name == 'clientid' && typeof limitServiceCategory == 'function') {
						limitServiceCategory();
					}
					if(table_name == 'ticket_attached' && field_name == 'arrived' && $('[name="reload_checkout_on_checkin"]').val() != undefined && $('[name="reload_checkout_on_checkin"]').val() == 1) {
						if(typeof still_reloading_tab != 'undefined') {
							clearInterval(still_reloading_tab);
						}
						reload_checkin($(field).closest('.tab-section'));
					}
				}
			});
		} else {
			ticket_wait = false;
		}
	});
}
function setInventoryWeights(value) {
	$('[name=total_shipment_weight_units]').val(value);
	$('[data-type=inventory_general][name=weight_units]').each(function() {
		setInventoryWeight(value, this);
	});
}
function setInventoryWeight(value, target) {
	if($(target).data('id') > 0) {
		$(target).val(value).change();
	} else {
		setTimeout(function() {
			setInventoryWeight(value, target);
		}, 250);
	}
}
var updateTicketLabelTimer = null;
function updateTicketLabel() {
	if(updateLabel && ticketid > 0 || ticketid == 'multi') {
        clearTimeout(updateTicketLabelTimer);
        updateTicketLabelTimer = setTimeout(function() {
            ticketid_list.forEach(function(ticket) {
                $.ajax({
                    url: 'ticket_ajax_all.php?action=get_ticket_label&ticketid='+ticket,
                    success: function(response) {
                        if(response != '') {
                            $('.ticketid_span').html(response);
                        }
                    }
                });
            });
        }, 1000);
	}
}
function add_stop() {
	$.ajax({
		url: 'ticket_ajax_all.php?action=add_stop',
		method: 'POST',
		data: {
			ticketid: (ticketid == 'multi' ? ticketid_list[0] : ticketid)
		},
		success: function(response) {
			if(response > 0) {
				if(ticketid == 'multi') {
					ticketid_list.push(response);
				} else {
					ticketid_list.push(ticketid);
					ticketid_list.push(response);
					ticketid = 'multi';
				}
				destroyInputs($('.delivery_stop_group'));
				var clone = $('.delivery_stop_group').last().clone();
				clone.find('[data-id]').data('id',response);
				clone.find('input').not('[name=pickup_order]').val('');
				$('.delivery_stop_group').last().after(clone);
				initInputs('.delivery_stop_group');
				setSave();
			}
		}
	});
}
function sign_off_complete() {
	$(this).prop('disabled',true);
	$(this).off('click',sign_off_complete);
	$('[name=sign_off_id]').val(user_id).trigger('change.select2').change();
	$('[name=sign_off_signature]').change();
	return false;
}
function sign_off_complete_force() {
	$(this).prop('disabled',true);
	$('[name=complete_force]').val(1);
	$(this).off('click',sign_off_complete_force);
	$('[name=sign_off_id]').val(user_id).trigger('change.select2').change();
	$('[name=sign_off_signature]').change();
	return false;
}
function reloadTab(name) {
	if(name != undefined && name != '') {
		$('#tab_section_'+name).each(function() {
			stopid_query = '';
			if(typeof stopid != 'undefined') {
				stopid_query = '&stop='+stopid;
			}
			$(this).load('edit_ticket_tab.php?ticketid='+ticketid+'&tab='+name+stopid_query);
		});
	}
}
function reload_billing() {
	$('#collapse_billing .panel-body,#tab_section_ticket_billing').load('../Ticket/edit_ticket_tab.php?tab=ticket_billing&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_billing');
		initInputs('#tab_section_ticket_billing');
		setBilling();
	});
	$('.inventory_billing_summary').load('../Ticket/edit_ticket_tab.php?tab=ticket_billing&tab_only=inventory&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_billing');
		initInputs('#tab_section_ticket_billing');
		setBilling();
	});
	$('.staff_billing_summary').load('../Ticket/edit_ticket_tab.php?tab=ticket_billing&tab_only=staff_list&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_billing');
		initInputs('#tab_section_ticket_billing');
		setBilling();
	});
	$('.misc_billing_summary').load('../Ticket/edit_ticket_tab.php?tab=ticket_billing&tab_only=misc_item&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_billing');
		initInputs('#tab_section_ticket_billing');
		setBilling();
	});
}
if(typeof checkin_ajax == 'undefined') {
	checkin_ajax = [];
}
if(typeof still_reloading_tab == 'undefined') {
	still_reloading_tab = '';
} else if(checkin_ajax.length == 0) {
	clearInterval(still_reloading_tab);
}
if(typeof checkin_running == 'undefined') {
	checkin_running = false;
}
function reload_checkin(tab_section = '') {
	var tab_id = '';
	if(tab_section != '') {
		var tab_id = $(tab_section).attr('id');
	}
	clearInterval(still_reloading_tab);
	checkin_running = false;
	checkin_ajax = [];
	if(tab_id != 'tab_section_ticket_checkin' && tab_id != 'tab_section_ticket_staff_list') {
		checkin_ajax.push(function() { reload_checkin_checkin(); });
		checkin_ajax.push(function() { reload_checkin_staff_checkin(); });
	}
	if(tab_id != 'tab_section_ticket_checkout' && tab_id != 'tab_section_ticket_checkout_staff') {
		checkin_ajax.push(function() { reload_checkin_checkout(); });
		checkin_ajax.push(function() { reload_checkin_staff_checkout(); });
	}

	still_reloading_tab = setInterval(function() {
		if(checkin_ajax.length == 0) {
			clearInterval(still_reloading_tab);
			setSave();
			initSelectOnChanges();
			reload_complete();
		} else if(!checkin_running) {
			checkin_running = true;
			checkin_ajax.shift()();
		}
	}, 50);
}
function reload_checkin_checkin() {
	console.log('checkin');
	destroyInputs($('#collapse_ticket_checkin,#tab_section_ticket_checkin'));
	$.ajax({
		url: '../Ticket/add_ticket_checkin.php?folder='+folder_name+'&ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('#collapse_ticket_checkin .panel-body,#tab_section_ticket_checkin').html(response);
			initInputs('#collapse_ticket_checkin');
			initInputs('#tab_section_ticket_checkin');
			checkin_running = false;
		}
	});
}
function reload_checkin_checkout() {
	console.log('checkout');
	destroyInputs($('#collapse_ticket_checkout,#tab_section_ticket_checkout'));
	$.ajax({
		url: '../Ticket/add_ticket_checkout.php?folder='+folder_name+'&ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('#collapse_ticket_checkout .panel-body,#tab_section_ticket_checkout').html(response);
			initInputs('#collapse_ticket_checkout');
			initInputs('#tab_section_ticket_checkout');
			checkin_running = false;
		}
	});
}
function reload_checkin_staff_checkin() {
	console.log('staff_checkin');
	if($('#collapse_ticket_staff_list,#tab_section_ticket_staff_list').find('.toggleSwitch').length > 0) {
		destroyInputs('#collapse_ticket_staff_list,#tab_section_ticket_staff_list');
		$('#collapse_ticket_staff_list .panel-body,#tab_section_ticket_staff_list').load('../Ticket/edit_ticket_tab.php?tab=ticket_staff_list&ticketid='+ticketid, function() {
			initInputs('#collapse_ticket_staff_list');
			initInputs('#tab_section_ticket_staff_list');
			checkin_running = false;
		});
	} else {
		checkin_running = false;
	}
}
function reload_checkin_staff_checkout() {
	console.log('staff_checkout');
	destroyInputs($('#collapse_ticket_checkout_staff,#tab_section_ticket_checkout_staff'));
	$.ajax({
		url: '../Ticket/add_ticket_checkout.php?folder='+folder_name+'&ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&staffcheckout=true'+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('#collapse_ticket_checkout_staff .panel-body,#tab_section_ticket_checkout_staff').html(response);
			initInputs('#collapse_ticket_checkout');
			initInputs('#tab_section_ticket_checkout_staff');
			checkin_running = false;
		}
	});
}
function reload_summary() {
	if(!finishing_ticket) {
		destroyInputs($('#collapse_summary,#tab_section_ticket_summary'));
		$.ajax({
			url: '../Ticket/add_ticket_summary.php?folder='+folder_name+'&ticketid='+ticketid+'&date='+$('[name=load_for_date]').val()+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
			dataType: 'html',
			success: function(response) {
				$('#collapse_summary .panel-body,#tab_section_ticket_summary').html(response);
				initInputs('#tab_section_ticket_summary');
				initInputs('#collapse_summary');
				initSelectOnChanges();
				reload_complete();
			}
		});
	}
}
function reload_documents() {
	destroyInputs($('.document_table'));
	$.ajax({
		url: '../Ticket/add_ticket_view_documents.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.document_table').html(response);
			initInputs('.document_table');
			initSelectOnChanges();
		}
	});
}
function reload_project_documents() {
	$.ajax({
		url: '../Ticket/add_ticket_view_documents.php?doc_type=project&ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.project_doc_table').html(response);
		}
	});
}
function reload_contact_documents() {
	$.ajax({
		url: '../Ticket/add_ticket_view_documents.php?doc_type=contact&ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.contact_doc_table').html(response);
		}
	});
}
function reload_customer_images() {
	$('.reload_customer_images').each(function() {
		var field = $(this);
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=get_customer_image',
			method: 'POST',
			data: {
				id: $(field).data('id'),
				field: $(field).attr('name')
			},
			success: function(response) {
				if(response != '') {
					$(field).next('.uploaded_image').html(response).show();
				}
			}
		});
	});
}
function reload_related() {
	$.ajax({
		url: '../Ticket/ticket_connected_list.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.connected_table').html(response);
		}
	});
}
function reload_delivery() {
	$.ajax({
		url: '../Ticket/add_ticket_delivery.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('#tab_section_ticket_delivery').html(response);
			destroyInputs('#tab_section_ticket_delivery');
			initInputs('#tab_section_ticket_delivery');
			setSave();
		}
	});
}
function reload_service_checklist() {
	destroyInputs($('.service_checklist'));
	$.ajax({
		url: '../Ticket/add_view_ticket_service_checklist.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.service_checklist').html(response);
			initInputs('.service_checklist');
			initSelectOnChanges();
			if(response != '' && $('.service_checklist').length > 0 && typeof calculateTimeEstimate == 'function') {
				calculateTimeEstimate();
			}
			reload_hidden_services();
		}
	});
}
function reload_hidden_services() {
	destroyInputs($('.hidden_services'));
	$.ajax({
		url: '../Ticket/add_ticket_info_service.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&reload_hidden_services=1'+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.hidden_services').html(response);
			initInputs('.hidden_services');
			initSelectOnChanges();
			if(response != '' && $('.hidden_services').length > 0 && typeof calculateTimeEstimate == 'function') {
				calculateTimeEstimate();
			}
		}
	});
}
function reload_service_extra_billing() {
	$('#collapse_ticket_service_extra_billing .panel-body,#tab_section_ticket_service_extra_billing').load('../Ticket/edit_ticket_tab.php?tab=ticket_service_extra_billing&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_ticket_service_extra_billing');
		initInputs('#tab_section_ticket_service_extra_billing');
	});
}
function reload_contact_notes() {
	$('#collapse_ticket_contact_notes .panel-body,#tab_section_ticket_contact_notes').load('../Ticket/edit_ticket_tab.php?tab=ticket_contact_notes&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_ticket_contact_notes');
		initInputs('#tab_section_ticket_contact_notes');
	});
}
function reload_site() {
	$('#collapse_ticket_location .panel-body,#tab_section_ticket_location').load('../Ticket/edit_ticket_tab.php?tab=ticket_location&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_ticket_contact_notes');
		initInputs('#tab_section_ticket_contact_notes');
	});
}
function reload_complete() {
	destroyInputs($('#collapse_ticket_complete,#tab_section_ticket_complete'));
	$('#collapse_ticket_complete .panel-body,#tab_section_ticket_complete').load('../Ticket/edit_ticket_tab.php?tab=ticket_complete&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_ticket_complete');
		initInputs('#tab_section_ticket_complete');
		if(typeof initPad == 'function') {
			initPad();
		}
	});
}
function clearNote(type, block) {
	block.find('input').not('.email_div input').data('id','').val('');
	block.find('textarea').data('id','').val('');
	tinymce.get(block.find('textarea').attr('id')).setContent('');
	block.find('select').data('id','').find('option').removeAttr('selected').trigger('change.select2');
	reload_notes(type, $('.ticket_comments[data-type="'+type+'"]'));
}
function reloadCommunications() {
	$('.ticket_communication.reload').each(function() {
		$(this).removeClass('reload');
		reload_communication($(this).data('type'),$(this).data('method'),this);
	});
}
function reload_communication(type, method, target) {
	$(target).load('../Ticket/add_ticket_view_communication.php?ticketid='+ticketid+'&comm_type='+type+'&comm_mode='+method);
}
function reload_needed_notes() {
	$('.ticket_comments.reload').each(function() {
		$(this).removeClass('reload');
		reload_notes($(this).data('type'),this);
	});
	$('.extra_billing.reload').each(function() {
		$(this).removeClass('reload');
		reload_service_extra_billing();
	});
}
function reload_notes(type, target) {
	$.ajax({
		url: '../Ticket/add_ticket_view_notes.php?ticketid='+ticketid+'&note_type='+type+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$(target).html(response);
			setSave();
		}
	});
}
function reload_attached_image() {
	$.ajax({
		url: '../Ticket/add_ticket_view_attached_image.php?ticketid='+ticketid+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val(),
		dataType: 'html',
		success: function(response) {
			$('.attached_image').html(response);
		}
	});
}
function reload_inventory() {
	$('#tab_section_ticket_inventory_detailed').load('../Ticket/edit_ticket_tab.php?tab=ticket_inventory_detailed&ticketid='+ticketid, function() {
		initInputs('#tab_section_ticket_inventory_detailed');
		setSave();
	});
	$('#tab_section_ticket_inventory_return').load('../Ticket/edit_ticket_tab.php?tab=ticket_inventory_return&ticketid='+ticketid, function() {
		initInputs('#tab_section_ticket_inventory_return');
		setSave();
	});
	reload_sidebar();
}
function reload_sidebar() {
	$('.tile-sidebar ul').load('../Ticket/edit_sidebar.php?ticketid='+ticketid+($('#action_mode').val() != '' ? '&action_mode='+$('#action_mode').val() : '')+'&estimate_mode='+$('#estimate_mode').val(), function() {
		$('.tile-sidebar ul').first().find('.standard-collapsible-link').closest('a').remove();
		$('.tile-sidebar ul').first().prepend('<a href="" onclick="collapseStandardSidebar(); return false;"><li class="standard-collapsible-link"><h5><< Hide Menu</h5></li></a>');
		$('[data-tab-target]').click(function() {
			$('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
			return false;
		});
		$('.main-screen .main-screen').scroll();
	});
}
function reload_checklists() {
	$('#collapse_ticket_checklist,#tab_section_ticket_view_checklist').load('../Ticket/edit_ticket_tab.php?tab=ticket_view_checklist&ticketid='+ticketid, function() {
		setSave();
		initSelectOnChanges();
		initInputs('#collapse_ticket_checklist');
		initInputs('#tab_section_ticket_view_checklist');
	});
}
function reload_po_num_dropdown() {
	var ticketid = $('#ticketid').val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=reload_po_num_dropdown&ticketid='+ticketid,
		method: 'GET',
		dataType: 'json',
		success: function(response) {
			var po_list = response;
			var po_html = '<option></option><option value="MANUAL">Custom PO#</option>';
			po_list.forEach(function(po_num_line) {
				po_html += '<option value="'+po_num_line+'">'+po_num_line+'</option>';
			});
			$('.po_num_dropdown').each(function() {
				var po_num = $(this).val();
				$(this).html(po_html);
				if(po_num != undefined && po_num != '' && $(this).find('option[value="'+po_num+'"]').length == 0) {
					$(this).append('<option value="'+po_num+'" selected>'+po_num+'</option>');
				} else {
					$(this).val(po_num);
				}
				$(this).trigger('change.select2');
			});
		}
	});
}
function startTicketStaff() {
    var block = $('div.start-ticket-staff').last();
    destroyInputs('.start-ticket-staff');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('.start-ticket-staff');
	setSave();
}
function deletestartTicketStaff(button) {
    if($('div.start-ticket-staff').length <= 1) {
        startTicketStaff();
    }
    $(button).closest('div.start-ticket-staff').remove();
    $('div.start-ticket-staff').first().find('[name="contactid"]').change();
}


function internalTicketStaff() {
    var block = $('div.internal-ticket-staff').last();
    destroyInputs('.internal-ticket-staff');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('.internal-ticket-staff');
	setSave();
}
function deleteinternalTicketStaff(button) {
    if($('div.internal-ticket-staff').length <= 1) {
        internalTicketStaff();
    }
    $(button).closest('div.internal-ticket-staff').remove();
    $('div.internal-ticket-staff').first().find('[name="internal_qa_contactid"]').change();
}

function customerTicketStaff() {
    var block = $('div.customer-ticket-staff').last();
    destroyInputs('.customer-ticket-staff');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('.customer-ticket-staff');
	setSave();
}
function deletecustomerTicketStaff(button) {
    if($('div.customer-ticket-staff').length <= 1) {
        customerTicketStaff();
    }
    $(button).closest('div.customer-ticket-staff').remove();
    $('div.customer-ticket-staff').first().find('[name="deliverable_contactid"]').change();
}

function addMulti(img, style, clone_location = '') {
	var multi_block = $(img).closest('.multi-block');
	var type = multi_block.data('type');
	var panel = multi_block.parents('.multi-block,.panel-body,.tab-section,.has-main-screen .main-screen').first();
	var source = '';
	if(type != '' && type != undefined) {
		source = panel.find('.multi-block[data-type="'+type+'"]').last();
	} else {
		source = panel.find('.multi-block:visible').last();
	}
	if(style == 'inline') {
		source = $(img).closest('.multi-block:visible')
	}
	destroyInputs(panel);
	var block = source.clone();
	block.find('[data-id]').each(function() {
        if($(this).data('id') == '') {
            $(this).data('id',source.find('[data-table="'+$(this).data('table')+'"][name="'+this.name+'"][data-id]').data('id'));
        }
    });
    block.find('.staff_multiple_times').find('.staff-multi-time:not(:first)').remove();
	block.find('input,select,textarea').val('');
	block.find('[data-default]').each(function() {
		$(this).val($(this).data('default'));
	});
	block.find('[type=checkbox]').removeAttr('checked');
	block.find('.general_piece_details').hide();
	block.find('[id]').each(function() {
		var id = this.id.split('_');
		if(!isNaN(id[id.length-1])) {
			id[id.length-1]++;
		}
		this.id = id.join('_');
	});

	block.find('[data-parent]').each(function() {
		var id = $(this).data('parent').split('_');
		if(!isNaN(id[id.length-1])) {
			id[id.length-1]++;
		}
		$(this).data('parent',id.join('_'));
		var href = $(this).attr('href').split('_');
		if(!isNaN(href[href.length-1])) {
			href[href.length-1]++;
		}
		$(this).attr('href',href.join('_'));
	});
	block.find('[data-table=tickets][data-id]').each(function() {
		$(this).data('id',(source.find('[name='+this.name+']').data('id')));
	});
	block.not('.no_id_reset').find('[data-id][data-table][data-table!=tickets][data-table!=contacts_medical]').data('id','');
	block.find('[name="ticket_comment_email_sender[]"]').val(user_email);
	block.find('[name*=qty]').val(1);
	block.find('[data-verified-shift]').data('verified-shift','');
	block.find('textarea').removeAttr('id');
	block.find('.select-div, .sig-div').show();
	block.find('.manual-div, .img-div').hide();
	if(clone_location == 'after') {
		multi_block.after(block).after('<hr />');
	} else {
		source.after(block).after('<hr />');
	}
	var count = 0;
	panel.find('.multi-block:visible').each(function() {
		$(this).find('.block_count').html(++count);
	});
	if(panel.attr('id') != '' && panel.attr('id') != undefined) {
		initInputs('#'+panel.attr('id'));
	} else {
		initInputs();
	}
	initPad('#'+panel.closest('.panel-collapse,.tab-section,.has-main-screen').attr('id'));
	setSave();
	initSelectOnChanges();
}
function remMulti(img) {
	var block = $(img).closest('.multi-block');
	var panel = block.parents('.panel-body,.tab-section,.has-main-screen .main-screen').first();
	if(panel.find('.multi-block:visible').length <= 1) {
		addMulti(img);
	}
	block.find('[name=deleted]').val(1).change();
	block.prev('hr').remove();
	if(block.find('[name=deleted]').is('[data-table]')) {
		block.remove();
	} else if($(img).data('remove') == "1") {
		var first_block = block.find('[data-table]').first();
		var field_name = first_block.attr('name');
		var field_table = first_block.data('table');
		block.remove();
		panel.find('[name='+field_name+'][data-table='+field_table+']').first().change();
        if(panel.find('[name='+field_name+'][data-table='+field_table+']').length < 1) {
            $('[name='+field_name+'][data-table='+field_table+']').first().change();
        }
	} else {
		block.find('[data-table]').first().change();
		block.hide();
	}
}
function addMultiPO(img) {
	destroyInputs('.po_nums_div');
	var block = $(img).closest('.po_nums_div').find('.multi-block').last();
	var clone = $(block).clone();

	$(clone).find('.custom_po_num').hide();
	$(clone).find('.po_num_group').show();
	$(clone).find('input,textarea,select').val('');
	$(clone).find('.po_num_group [name="po_num_disabled"]').attr('name','po_num');
	$(clone).find('.custom_po_num [name="po_num"]').attr('name','po_num_disabled');

	$(block).after(clone);
	reload_po_num_dropdown();
	initInputs('.po_nums_div');
	setSave();
	initSelectOnChanges();
}
function remMultiPO(img) {
	if($(img).closest('.po_nums_div').find('.multi-block').length <= 1) {
		addMultiPO(img);
	}

	var block = $(img).closest('.po_nums_div');
	$(img).closest('.multi-block').remove();
	$(block).find('[name="po_num"]').first().change();
}
function addMultiPOLine(img) {
	destroyInputs('.po_lines_div');
	var block = $(img).closest('.po_lines_div').find('.multi-block').last();
	var clone = $(block).clone();

	$(clone).find('.po_line_range').hide();
	$(clone).find('.po_line_single').show();
	$(clone).find('input,textarea,select').val('');
	$(clone).find('.po_line_range .po_line_value').attr('name','po_line_disabled');
	$(clone).find('.po_line_single .po_line_value').attr('name','po_line');

	$(block).after(clone);
	initInputs('.po_lines_div');
	setSave();
	initSelectOnChanges();
}
function remMultiPOLine(img) {
	if($(img).closest('.po_lines_div').find('.multi-block').length <= 1) {
		addMultiPOLine(img);
	}

	var block = $(img).closest('.po_lines_div');
	$(img).closest('.multi-block').remove();
	$(block).find('[name="po_line"]').first().change();
}
function rangeMultiPOLine(img) {
	var block = $(img).closest('.multi-block');
	if($(block).find('[name="po_line"]').closest('.po_line_div').hasClass('po_line_single')) {
		$(block).find('.po_line_single').hide();
		$(block).find('.po_line_range').show();
		$(block).find('.po_line_single').find('[name="po_line"]').attr('name', 'po_line_disabled');
		$(block).find('.po_line_range').find('[name="po_line_disabled"]').attr('name', 'po_line');
	} else {
		$(block).find('.po_line_single').show();
		$(block).find('.po_line_range').hide();
		$(block).find('.po_line_single').find('[name="po_line_disabled"]').attr('name', 'po_line');
		$(block).find('.po_line_range').find('[name="po_line"]').attr('name', 'po_line_disabled');
	}
	$(block).find('[name="po_line"]').change();
}
function poLineRangeChange(select) {
	var block = $(select).closest('.po_line_range');
	var min_range = $(block).find('[name="po_line_range_min"]').val();
	var max_range = $(block).find('[name="po_line_range_max"]').val();
	$(block).find('[name="po_line"]').val(min_range+'-'+max_range).change();
}
function addStaffMultiTime(img) {
	var multi_time = $(img).closest('.staff-multi-time');
	var panel = multi_time.parents('.multi-block,.panel-body,.tab-section,.has-main-screen .main-screen').first();
	destroyInputs(panel);
	var block = $(multi_time).clone();
	block.find('input,select,textarea').val('');
	block.find('[data-id][data-table][data-table!=tickets][data-table!=contacts_medical]').data('id','');
	multi_time.after(block);
	if(panel.attr('id') != '' && panel.attr('id') != undefined) {
		initInputs('#'+panel.attr('id'));
	} else {
		initInputs();
	}
	initPad('#'+panel.closest('.panel-collapse,.tab-section,.has-main-screen').attr('id'));
	setSave();
	initSelectOnChanges();
}
function remStaffMultiTIme(img) {
	var block = $(img).closest('.staff-multi-time');
	var panel = block.parents('.panel-body,.tab-section,.has-main-screen .main-screen').first();
	if(panel.find('.multi-block:visible').length <= 1) {
		addStaffMultiMulti(img);
	}
	block.find('[name=deleted]').val(1).change();
	if(block.find('[name=deleted]').is('[data-table]')) {
		block.remove();
	} else {
		block.find('[data-table]').first().change();
		block.hide();
	}
}
function noMeds(img) {
	var row = $(img).closest('.multi-block');
	row.find('[name=arrived]').val(2).change();
	row.find('.toggleSwitch').off('click').find('span').hide().last().show();
	row.find('.administer,.witness').hide();
	row.find('.comment').show();
}

function setFocusInPanel(elem) {
	if(elem.closest('.panel-body').css('height') == '0px') {
		elem.closest('.panel').find('.panel-title a').click();
	}
	elem.focus();
}
function addition() {
	$('.addition_button').addClass('disabled').prop('onclick',null).off('click').click(function() { return false; });
	$.ajax({
		url: 'ticket_ajax_all.php?action=addition&src_id='+$('[data-table="tickets"][data-id]').data('id'),
		success: function(response) {
			if($('#calendar_view').val() == 'true') {
				window.parent.$('[name="calendar_iframe"]').off('load');
				window.parent.$('[name="calendar_iframe"]').attr('src','../blank_loading_page.php');
				window.parent.overlayIFrameSlider('../Ticket/index.php?ticketid='+response+'&edit='+response+'&calendar_view=true&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val());
			} else {
				window.location.href = '?ticketid='+response+'&edit='+response+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
			}
		}
	});
	return false;
}
function multiple_tickets(count, id) {
	$('.multiple_button').addClass('disabled').prop('onclick',null).off('click').click(function() { return false; });
	$.ajax({
		url: 'ticket_ajax_all.php?action=multiple&ticket='+id+'&count='+count,
		success: function(response) {
			$('.multiple_button').removeClass('disabled').off('click').prop('onclick',"return multiple_tickets($('[name=multiple_ticket_count]').val(), ticketid);");
			reload_related();
		}
	});
	return false;
}
function showStaff(src) {
	if(!$(src).hasClass('counterclockwise')) {
		close_staff_iframe();
	} else {
		close_staff_iframe();
		$(src).removeClass('counterclockwise');
		$(src).closest('.multi-block').find('.iframe_div').show().find('span').show();
		$(src).closest('.multi-block').find('[name=staff_iframe]').off('load').load(function() {
			var iframe = $(this);
			iframe.off('load').load(close_staff_iframe);
			iframe.closest('.iframe_div').find('span').hide();
			$(this.contentWindow.document.body).click(function() {
				var body = this;
				iframe.height(body.scrollHeight);
				setTimeout(function() { iframe.height(body.scrollHeight); }, 500);
			}).click();
		});
		$(src).closest('.multi-block').find('[name=staff_iframe]').get(0).src = '../Staff/staff_details.php?ticketid='+ticketid+'&staffid='+$(src).closest('.multi-block').find('[name=item_id]').val();
	}
}
function close_staff_iframe() {
	$('#collapse_staff .iframe_div,#tab_section_ticket_staff_list .iframe_div').hide().find('iframe').height(0);
	$('#collapse_staff,#tab_section_ticket_staff_list').find('img.black-color').addClass('counterclockwise');
}
function memberPanels(src) {
	var panel = $(src).closest('.panel,.tab-section');
	panel.find('iframe').off('load').load(function() {
		var iframe = $(this);
		iframe.off('load').load(close_member_iframe);
		iframe.closest('.iframe_div').find('span').hide();
		$(this.contentWindow.document.body).find('.row *').not('form, form .panel-group, form .panel-group *,ul,ul *').hide();
		$(this.contentWindow.document.body).find('.panel').show();
		$(this.contentWindow.document.body).click(function() {
			var body = this;
			iframe.height(body.scrollHeight);
			setTimeout(function() { iframe.height(body.scrollHeight); }, 500);
		}).click();
	});
	panel.find('iframe').get(0).src = '../Members/contact_viewable.php?tab='+panel.data('tab')+'&edit='+$(src).closest('.multi-block').find('[name=item_id]').val();
}
function showMember(src) {
	if(!$(src).hasClass('counterclockwise')) {
		close_member_iframe();
	} else {
		close_member_iframe();
		$(src).removeClass('counterclockwise');
		$(src).closest('.multi-block').find('.iframe_div').show();
		$(src).closest('.multi-block').find('.full-target').prop('href','../Members/contacts_inbox.php?category=Members&edit='+$(src).closest('.multi-block').find('[name=item_id]').val());
	}
}
function close_member_iframe() {
	$('#collapse_members .iframe_div,#tab_section_ticket_members .iframe_div').hide();
	$('#collapse_members,#tab_section_ticket_members').find('img.black-color').addClass('counterclockwise');
}
function showClient(src) {
	if(!$(src).hasClass('counterclockwise')) {
		close_client_iframe();
	} else {
		close_client_iframe();
		$(src).removeClass('counterclockwise');
		$(src).closest('.multi-block').find('.iframe_div').show().find('span').show();
		$(src).closest('.multi-block').find('[name=client_iframe]').off('load').load(function() {
			var iframe = $(this);
			iframe.off('load').load(close_client_iframe);
			iframe.closest('.iframe_div').find('span').hide();
			$(this.contentWindow.document.body).find('.row *').not('form, form .panel-group, form .panel-group *').hide();
			$(this.contentWindow.document.body).find('.panel').show();
			$(this.contentWindow.document.body).click(function() {
				var body = this;
				iframe.height(body.scrollHeight);
				setTimeout(function() { iframe.height(body.scrollHeight); }, 500);
			}).click();
		});
		$(src).closest('.multi-block').find('[name=client_iframe]').get(0).src = '../ClientInfo/add_contacts.php?category=Clients&contactid='+$(src).closest('.multi-block').find('[name=item_id]').val();
	}
}
function close_client_iframe() {
	$('#collapse_client .iframe_div,#tab_section_ticket_clients .iframe_div').hide().find('iframe').height(0);
	$('#collapse_client,#tab_section_ticket_clients').find('img.black-color').addClass('counterclockwise');
}
function linkMeds(a) {
	var contact = $(a).closest('.multi-block').find('[name=item_id]').val();
	if(contact > 0) {
		window.open('../Members/contacts_inbox.php?category=Members&profile=false&section=medications&edit='+contact);
	} else {
		alert('No Member Selected.');
	}
}
function signNameUpdate(input) {
	if($(input).val() == 'MANUAL') {
		$(input).closest('.sig-div').find('.select-div').hide();
		$(input).closest('.sig-div').find('.manual-div').show().find('[name=sign_name]').val('').change().focus();
	} else {
		$(input).closest('.form-group').find('.img-div [name=sign_name]').val($(input).val());
	}
}
function witnessNameUpdate(input) {
	if($(input).val() == 'MANUAL') {
		$(input).closest('.sig-div').find('.select-div').hide();
		$(input).closest('.sig-div').find('.manual-div').show().find('[name=witness_name]').val('').change().focus();
	} else {
		$(input).closest('.form-group').find('.img-div [name=witness_name]').val($(input).val());
	}
}

function filterPositions() {
	var allowed = $(this).find('option:selected').data('positions-allowed');
	if(allowed != '' && allowed != undefined) {
		allowed = allowed.split(',');
		$(this).closest('.multi-block').find('[name=position] option').each(function() {
			var id = $(this).data('id');
			if(id != undefined && allowed.indexOf(id.toString()) < 0) {
				$(this).hide();
			} else {
				$(this).show();
			}
		});
		$(this).closest('.multi-block').find('[name=position]').trigger('select2.change');
	} else {
		$(this).closest('.multi-block').find('[name=position] option').show();
		$(this).closest('.multi-block').find('[name=position]').trigger('select2.change');
	}
	var position = $(this).find('option:selected').data('position');
	if(position != '' && position != undefined) {
		var cur_pos = $(this).closest('.multi-block').find('[name=position]').val();
		if(cur_pos != position) {
			$(this).closest('.multi-block').find('[name=position]').val(position).change();
		}
	}
}
function filterEquipment(select) {
	var block = $(select).closest('.multi-block').first();
	if(select.name == 'item_id') {
		var option = $(select).find('option:selected');
		block.find('[name=eq_category]').val(option.data('category')).trigger('change.select2');
		block.find('[name=eq_make]').val(option.data('make')).trigger('change.select2');
		block.find('[name=eq_model]').val(option.data('model')).trigger('change.select2');
		block.find('select[name=rate]').each(function() {
			$(this).find('option:selected').removeAttr('selected');
			$(this).find('option[data-type=daily]').val(option.data('daily'));
			$(this).find('option[data-type=hourly]').val(option.data('hourly'));
			$(this).trigger('change.select2');
		});
	} else if(select.name == 'eq_category') {
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	} else if(select.name == 'eq_make') {
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('make') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	} else if(select.name == 'eq_model') {
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('model') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	}
}
function filterMaterials(select) {
	var block = $(select).closest('.multi-block');
	if(select.name == 'item_id') {
		var option = $(select).find('option:selected');
		if(option.val() == 'MANUAL') {
			$(select).val(0).change();
			block.find('.select-div').hide();
			block.find('.manual-div').show();
		} else {
			block.find('[name=mat_category]').val(option.data('category')).trigger('change.select2');
			block.find('[name=mat_sub]').val(option.data('sub-category')).trigger('change.select2');
		}
	} else if(select.name == 'mat_category') {
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	} else if(select.name == 'mat_sub') {
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('sub-category') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	}
}
function materialRate(block) {
	var block = $(block).closest('.multi-block');
	var qty = block.find('[name=qty]').val();
	var rate = block.find('[name=rate]').val();
	var markup = qty * rate * 1.15;
	block.find('[name=material_markup]').val(markup.toFixed(2));
}
function filterInventory() {
	var block = $(this).closest('.multi-block');
	var select = this;
	if(select.name == 'item_id') {
		var option = $(select).find('option:selected');
		if(option.val() == 'MANUAL') {
			$(select).val(0).change();
			block.find('.select-div').hide();
			block.find('.manual-div').show();
		} else {
			block.find('[name=inv_category]').val(option.data('category')).trigger('change.select2');
			block.find('[name=inv_sub]').val(option.data('sub-category')).trigger('change.select2');
		}
		block.find('[name=part]').val(this.value).trigger('change.select2');
	} else if(select.name == 'inv_category') {
		block.find('[name=part] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=part]').trigger('change.select2');
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
		block.find('[name=inv_sub] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=inv_sub]').trigger('change.select2');
	} else if(select.name == 'inv_sub') {
		block.find('[name=part] option').show().filter(function() { return $(this).data('sub-category') != select.value; }).hide();
		block.find('[name=part]').trigger('change.select2');
		block.find('[name=item_id] option').show().filter(function() { return $(this).data('sub-category') != select.value; }).hide();
		block.find('[name=item_id]').trigger('change.select2');
	}
}
function manualSelect() {
	var div = $(this).closest('.form-group');
	if(this.value == 'MANUAL') {
		div.find('.select-div').hide();
		div.find('.manual-div').show().find('input').first().focus();
	} else {
		div.find('.select-div').show();
		div.find('.manual-div').hide();
	}
}
function filterAssignedEquipment(select) {
	if(select.type == 'change') {
		select = select.currentTarget;
	}
	if(select == undefined) {
		select = this;
	}
	var block = $(select).closest('.multi-block-assign,.scheduled_stop').first();
	if(select.name == 'equipmentid') {
		var option = $(select).find('option:selected');
		block.find('[name$=_eq_category]').val(option.data('category')).trigger('change.select2');
		block.find('[name$=_eq_make]').val(option.data('make')).trigger('change.select2');
		block.find('[name$=_eq_model]').val(option.data('model')).trigger('change.select2');
	} else if(select.name.substr(-12) == '_eq_category') {
		block.find('[name$=_eq_make] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name$=_eq_model] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name=equipmentid] option').show().filter(function() { return $(this).data('category') != select.value; }).hide();
		block.find('[name$=_eq_make]').trigger('change.select2');
		block.find('[name$=_eq_model]').trigger('change.select2');
		block.find('[name=equipmentid]').trigger('change.select2');
	} else if(select.name.substr(-8) == '_eq_make') {
		var option = $(select).find('option:selected');
		block.find('[name$=_eq_category]').val(option.data('category')).trigger('change.select2');
		block.find('[name$=_eq_model] option').show().filter(function() { return $(this).data('make') != select.value; }).hide();
		block.find('[name=equipmentid] option').show().filter(function() { return $(this).data('make') != select.value; }).hide();
		block.find('[name$=_eq_model]').trigger('change.select2');
		block.find('[name=equipmentid]').trigger('change.select2');
	} else if(select.name.substr(-9) == '_eq_model') {
		var option = $(select).find('option:selected');
		block.find('[name$=_eq_category]').val(option.data('category')).trigger('change.select2');
		block.find('[name$=_eq_make]').val(option.data('make')).trigger('change.select2');
		block.find('[name=equipmentid] option').show().filter(function() { return $(this).data('model') != select.value; }).hide();
		block.find('[name=equipmentid]').trigger('change.select2');
	}
}
function setDeliveryOrder() {
	if(ticketid > 0 || ticketid == 'multi') {
		var invoice = $('[name=salesorderid]').val();
		$('[name=pickup_order],[name=order_number]:visible').val(invoice).trigger('change.select2').change();
		$('[name=salesorderid]').val(invoice);
	} else {
		setTimeout(setDeliveryOrder, 250);
	}
}
function sortScheduledStops() {
	var i = 0;
	$('.scheduled_stop:visible [name=sort]').each(function() {
		$(this).val(i++).change();
		$(this).closest('.scheduled_stop').find('.block_count').html(i);
	});
}
function remScheduledStop(input) {
	var block = $(input).closest('.scheduled_stop');
	if($('.scheduled_stop:visible').length <= 1) {
		addScheduledStop();
	}
	block.find('[name=deleted]').val(1).change();
	block.remove();
}
function addScheduledStop() {
	destroyInputs($('.scheduled_stop'));
	var clone = $('.scheduled_stop:visible').last().clone();
	clone.find('input[type="radio"]').each(function() {
		if($(this).data('index') != undefined && $(this).data('index') != '') {
			var index = $(this).data('index');
			$(this).attr('data-index', parseInt(index)+1);
			$(this).prop('name', $(this).data('field-name')+'_'+(parseInt(index)+1));
		}
	});
	clone.find('[data-id][data-table]').data('id','');
	clone.find('[data-table]').not('[name=equipmentid],[name=to_do_date],[name=order_number]').val('');
	if($(clone).find('[name="type"]').data('default-value') != undefined && $(clone).find('[name="type"]').data('default-value')) {
		$(clone).find('[name="type"]').val($(clone).find('[name="type"]').data('default-value'));
	}
	$('.scheduled_stop:visible').last().after(clone).after('<hr>');
	initInputs('.scheduled_stop');
	setSave();
	clone.find('[name=equipmentid],[name=to_do_date],[name=order_number]').change();
	if(defaultStatus != '') {
		clone.find('[name=status]').val(defaultStatus).trigger('change.select2');
	}
    $('[name=default_services]').each(function() {
        var services = this.value.trim(',').split(',');
        for(var i = 0; i < services.length; i++) {
            clone.find('[name=serviceid]').last().each(function() {
                if(services[i] > 0) {
                    if(i > 0) {
                        addMulti(this);
                    }
                    $(this).val(services[i]).change();
                }
            });
        }
    });
	sortScheduledStops();
	$('.scheduled_stop').last().find('input').first().focus();
}
function siteSelect(select) {
	var value = $(select).find('option:selected').val();
	if(value == 'MANUAL') {
		$('.site_info').hide();
		$(select).closest('.site_group').find('.site_name').show().find('input').focus();
	} else {
		$('.site_info').show();
		$('.site_name').hide();
		$('.site_info').find('[data-id][data-table*=contacts]').val('').data('id',value);
		var opt = $('select[name="siteid"],select[name="siteid[]"]').not('#po_siteid').find('option:selected');
		$('.site_info [name=business_address]').val(opt.data('address'));
		$('.site_info [name=site_name]').val(opt.data('site'));
		$('.site_info [name=display_name]').val(opt.data('display'));
		$('.site_info [name=lsd]').val(opt.data('lsd'));
		$('.site_info [name=address]').val(opt.data('street'));
		$('.site_info [name=city]').val(opt.data('city'));
		$('.site_info [name=province]').val(opt.data('province'));
		$('.site_info [name=postal_code]').val(opt.data('postal'));
		$('.site_info [name=country]').val(opt.data('country'));
		if(opt.data('google') != '' && opt.data('google') != undefined) {
			$('.site_info [name=google_maps_address]').val(opt.data('google'));
			$('.site_info a:contains("Google Maps")').attr('href',opt.data('google')).attr('onclick','');
		} else if(opt.data('street') != '' && opt.data('street') != undefined) {
			var maps_link = 'http://maps.google.com/maps/place/'+opt.data('street');
			$('.site_info [name=google_maps_address]').val(maps_link).change();
			$('.site_info a:contains("Google Maps")').attr('href',maps_link).attr('onclick','');
			$()
		} else {
			$('.site_info [name=google_maps_address]').val('');
			$('.site_info a:contains("Google Maps")').attr('href','').attr('onclick','return false;');
		}
		$('.site_info [name=police_contact]').val(opt.data('police'));
		$('.site_info [name=poison_control]').val(opt.data('poison'));
		$('.site_info [name=non_emergency]').val(opt.data('nonEmerg'));
		$('.site_info [name=site_emergency_contact]').val(opt.data('emerg'));
		$('.site_info [name=key_number]').val(opt.data('key-number'));
		$('.site_info [name=door_code_number]').val(opt.data('door-code-number'));
		$('.site_info [name=alarm_code_number]').val(opt.data('alarm-code-number'));
		$('.site_info [name=mailing_address]').val(opt.data('street'));
		var notes = opt.data('notes');
		if(notes == undefined) {
			notes = '';
		}
		var site_notes_id = $('.site_info [name=notes]').attr('id');
		if(site_notes_id != '' && site_notes_id != undefined) {
			tinymce.get(site_notes_id).setContent(notes);
		}
		var emerg = opt.data('emergNotes');
		if(emerg == undefined) {
			emerg = '';
		}
		var site_notes_id = $('.site_info [name=emergency_notes]').attr('id');
		if(site_notes_id != '' && site_notes_id != undefined) {
			tinymce.get(site_notes_id).setContent(emerg);
		}
	}
}
function poNumSelect(select) {
	var value = $(select).find('option:selected').val();
	if(value == 'MANUAL') {
		$(select).closest('.po_num_group').hide();
		$(select).closest('.po_num_group').next('.custom_po_num').show();
		$(select).closest('.po_num_group').find('[name="po_num"]').prop('name', 'po_num_disabled');
		$(select).closest('.po_num_group').next('.custom_po_num').find('[name="po_num_disabled"]').prop('name', 'po_num');
	} else {
		$(select).closest('.po_num_group').show();
		$(select).closest('.po_num_group').next('.custom_po_num').hide();
		$(select).closest('.po_num_group').next('.custom_po_num').find('input').val('');
		$(select).closest('.po_num_group').find('[name="po_num_disabled"]').prop('name', 'po_num');
		$(select).closest('.po_num_group').next('.custom_po_num').find('[name="po_num"]').prop('name', 'po_num_disabled');
	}
}
function staff_list_add(input) {
	for(var i = staff_list.length; i >= 0; i--) {
		if(staff_list[i] == input.value) {
			staff_list.splice(i,1);
		}
	}
	if(input.checked) {
		staff_list.push(input.value);
	}
}
function task_list_add(input, restricted) {
	var value = input.value;
	if(restricted) {
		$('.billing_group [data-task-type=extra]').closest('label').hide();
		$(input).closest('.billing_group').find('label').show();
		$('.billing_group').each(function() {
			if($(this).find('label:visible').length == 0) {
				$(this).hide();
			}
		});
		$('.assign_staff_task').data('task-group', $(input).data('task-group'));
	}
	if($(input).data('task-type') == 'extra') {
		value = value+'|EXTRA';
	}
	for(var i = task_list.length; i >= 0; i--) {
		if(task_list[i] == value) {
			task_list.splice(i,1);
		}
	}
	if(input.checked) {
		task_list.push(value);
	}
}
var extra_ticket_inserted = 0;
function add_staff_task(checkin) {
	while(staff_list.length > task_list.length) {
		task_list.push(task_list[task_list.length - 1]);
	}
	while(staff_list.length < task_list.length) {
		staff_list.push(staff_list[staff_list.length - 1]);
	}
	if(task_list.length > 0 && staff_list.length > 0) {
		var extra_billing = [];
		var extra_id = ticket = ticketid;
		if(ticketid == 'multi') {
			extra_id = ticketid_list[0];
			ticket = ticketid_list[0];
		}
		var extra_task_group = $('.assign_staff_task').data('task-group');
		var completed = 0;
		var total_count = staff_list.length;
		for(var i = 0; i < staff_list.length; i++) {
			var staff = staff_list[i];
			var task = task_list[i].replace('|EXTRA','');
			if(task != task_list[i]) {
				extra_billing.push(task);
			}
			if(checkin == undefined) {
				$('#collapse_staff_task,#tab_section_ticket_staff_tasks').find('hr').last().before('<label class="col-sm-6">Staff: '+$('[name=staff_task_contact][value='+staff+']').closest('label').text()+'</label><label class="col-sm-6">Task: '+task+'</label>');
			}
			$.ajax({
				url: 'ticket_ajax_all.php?action=update_fields'+($('[name=no_time_sheet]').val() > 0 ? '&time_sheet=none' : ''),
				method: 'POST',
				data: {
					table: 'ticket_attached',
					field: 'item_id',
					value: staff,
					id_field: 'id',
					ticketid: ticket,
					type: 'Staff_Tasks',
					type_field: 'src_table',
					attach: task,
					attach_field: 'position',
					extra: (task != task_list[i] ? 'true' : 'false'),
					extra_id: extra_id,
					task_group: extra_task_group,
					task_list: task_list,
					extra_ticket_inserted: extra_ticket_inserted,
					sync_recurring_data: $('#sync_recurrences').val()
				},
				success: function(response) {
					console.log(response);
					if(response.split('|')[1] == 'extra') {
						extra_id = response.split('|')[2];
					}
					if(checkin == undefined) {
						if(typeof still_reloading_tab != 'undefined') {
							clearInterval(still_reloading_tab);
						}
						reload_checkin();
						reload_summary();
					} else {
						$.ajax({
							url: 'ticket_ajax_all.php?action=update_fields'+($('[name=no_time_sheet]').val() > 0 ? '&time_sheet=none' : ''),
							method: 'POST',
							data: {
								table: 'ticket_attached',
								field: 'arrived',
								value: 1,
								id: response,
								id_field: 'id',
								ticketid: ticketid,
								sync_recurring_data: $('#sync_recurrences').val()
							}
						});
					}
					completed++;
					if(total_count == completed) {
						if(extra_billing.length > 0) {
							send_billing_email(extra_billing, extra_id);
							$.ajax({
								url: 'ticket_ajax_all.php?action=update_fields',
								method: 'POST',
								data: {
									table: 'ticket_comment',
									field: 'comment',
									value: '<p>Tasks requiring Extra Billing have been added to this '+ticket_name+'.</p><p>The tasks are:<br />'+extra_billing.join('<br />')+'</p>',
									id: '',
									id_field: 'ticketcommid',
									ticketid: ticket,
									type: 'addendum',
									sync_recurring_data: $('#sync_recurrences').val()
								}
							});
						}
						if(extra_id != ticket && checkin != 'checkin') {
							if($('#calendar_view').val() == 'true') {
								window.parent.$('[name="calendar_iframe"]').off('load');
								window.parent.$('[name="calendar_iframe"]').attr('src','../blank_loading_page.php');
								window.parent.overlayIFrameSlider('../Ticket/index.php?edit='+extra_id+'&ticketid='+extra_id+'&from='+from_url+'&calendar_view=true&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val());
							} else {
								window.location.href = '?edit='+extra_id+'&ticketid='+extra_id+'&from='+from_url+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
							}
						} else {
							window.onbeforeunload = function() { }
							window.location.href = '/home.php';
						}
					}
				}
			});
			if(task != task_list[i]) {
				extra_ticket_inserted = 1;
			}
		}
		$('[name=staff_assigned_task]:checked').removeAttr('checked');
		$('[name=staff_task_contact]:checked').removeAttr('checked')
		task_list = [];
		staff_list = [];
	} else if(staff_list.length > 0) {
		alert('Please select a task');
	} else {
		alert('Please select a staff');
	}
}
function toggleAll(button) {
	$(button).closest('.panel-body,.tab-section,.has-main-screen .main-screen').find('.toggle[value=0]').closest('.toggleSwitch').click();
}
var sorting_done = 0;
function getDriveTime(button, date, equipment) {
    if(sorting_done == 0) {
        sorting_done == 1;
        $(button).text('Updating Drive Time...');
        sorting_done = 1;
        sort_by_map(date, equipment, '', '', true);
    }
    if(sorting_done == 1) {
        setTimeout(function() {
            $(button).click();
        }, 250);
        return false;
    }
}
function checkoutAll(button) {
	if($(button).hasClass('finish_btn')) {
		finishing_ticket = true;
	}
	reload_summary();
	$('[name="summary_signature"]').each(function() {
		if(this.value != "") {
			$('[name="summary_signature"][value=""]').val(this.value);
		}
	});
	$('[name="sign_off_signature"]').each(function() {
		if(this.value != "") {
			$('[name="sign_off_signature"][value=""]').val(this.value);
		}
	});
	if($(button).data('require-signature') != undefined && $(button).data('require-signature') == 1 && ($('[name="summary_signature"]').val() == undefined || $('[name="summary_signature"]').val() == '') && ($('[name="sign_off_signature"]').val() == undefined || $('[name="sign_off_signature"]').val() == '')) {
		alert("A signature is required.");
		finishing_ticket = false;
		return false;
	} else {
		if(confirm("Are you sure you want to check out all Staff?")) {
            $(button).text('Checking Out Staff...');
			$('#collapse_ticket_checkout,#tab_section_ticket_checkout,#collapse_ticket_complete,#tab_section_ticket_complete').find('.toggle[value=0]').closest('.toggleSwitch').click();
			if($(button).data('recurring-ticket') != undefined && $(button).data('recurring-ticket') == 1) {
				createRecurringTicket();
			}
			if($(button).data('require-signature') != undefined && $(button).data('require-signature') == 1) {
				if($('[name="sign_off_signature"]') != undefined) {
					sign_off_complete_force();
					return false;
				} else if($('[name="summary_signature"]') != undefined) {
					$('[name="summary_signature"]').change();
				}
			}
		}
	}
}
function checkinAll(button) {
	reload_summary();
	$('#collapse_ticket_checkin,#tab_section_ticket_checkin').find('.toggle[value=0]').closest('.toggleSwitch').click();
	$.ajax({
		url: 'ticket_ajax_all.php?action=update_fields'+($('[name=no_time_sheet]').val() > 0 ? '&time_sheet=none' : ''),
		method: 'POST',
		data: {
			table: 'ticket_comment',
			field: 'comment',
			value: 'Everyone has been checked in.',
			id: '',
			id_field: 'ticketcommid',
			ticketid: ticketid,
			type: 'completion_notes',
			type_field: '',
			attach: '',
			attach_field: '',
			sync_recurring_data: $('#sync_recurrences').val()
		}
	});
}
function createRecurringTicket() {
	$.ajax({
		async: false,
		url: 'ticket_ajax_all.php?action=create_recurring_ticket',
		method: 'POST',
		data: { ticketid: ticketid },
		success: function(response) {
			if(response != '') {
				alert(response);
			}
		}
	});
}
function saveNewTicketFromCalendar(element) {
	var to_do_date = $('[name=to_do_date]').val();
	var to_do_end_date= $('[name=to_do_end_date]').val();
	var to_do_start_time = $('[name=to_do_start_time]').val();
	var to_do_end_time= $('[name=to_do_end_time]').val();
	var equipmentid = $('#assigned_equipment').val();
	var contactid = $('[name="contactid[]"]').val();
	var region = $('[name=region]').val();
	var location = $('[name=con_location]').val();
	var classification = $('[name=classification]').val();
	var status = $('[name="status"]').val();
	var ticket_type = $('[name=ticket_type]').val();
	var businessid = $('[name="businessid"]').val();
	var projectid = $('[name="projectid"]').val();
	var milestone = $('[name="milestone_timeline"]').val();

	var scheduled_stop = 0;
	var stop_equipmentid = '';
	var stop_to_do_date = '';
	var stop_to_do_start_time = '';
	var stop_address = '';
	var stop_city = '';
	var stop_postal_code = '';
	if($('.scheduled_stop').length > 0) {
		var block = $('.scheduled_stop').first();
		scheduled_stop = 1;
		stop_equipmentid = $(block).find('[name="equipmentid"]').val();
		stop_to_do_date = $(block).find('[name="to_do_date"]').val();
		stop_to_do_start_time = $(block).find('[name="to_do_start_time"]').val();
		stop_address = $(block).find('[name="address"]').val();
		stop_city = $(block).find('[name="city"]').val();
		stop_postal_code = $(block).find('[name="postal_code"]').val();
	}

	var data = { status: status, to_do_date: to_do_date, to_do_end_date: to_do_end_date, to_do_start_time: to_do_start_time, to_do_end_time: to_do_end_time, equipmentid: equipmentid, contactid: contactid, region: region, location: location, classification: classification, scheduled_stop: scheduled_stop, stop_equipmentid: stop_equipmentid, stop_to_do_date: stop_to_do_date, stop_to_do_start_time: stop_to_do_start_time, stop_address: stop_address, stop_city: stop_city, stop_postal: stop_postal_code, ticket_type: ticket_type, businessid: businessid, projectid: projectid, milestone: milestone };
	console.log(data);
	$.ajax({
		url: 'ticket_ajax_all.php?action=new_ticket_from_calendar',
		method: 'POST',
		data: data,
		success: function(response) {
			ticketid = response.split('*#*')[0];
			$('[name="ticketid"]').val(ticketid);
			$('[data-table=tickets]').data('id',ticketid);
			updateTicketLabel();
			if($('.scheduled_stop').length > 0) {
				var block = $('.scheduled_stop').first();
				block.find('[data-table=ticket_schedule]').data('id',response.split('*#*')[1]);
				$(block).find('[name="to_do_start_time"]').change();
			}
			$(element).change();
			if($('select[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').length > 0) {
				$('select[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').each(function() {
					if($(this).val() != undefined && $(this).val() != '' && $(this).val() != 0) {
						$(this).change();
					}
				});
			}
			if($('select[name="item_id"][data-table="ticket_attached"][data-type="equipment"]').length > 0) {
				$('select[name="item_id"][data-table="ticket_attached"][data-type="equipment"]').each(function() {
					if($(this).val() != undefined && $(this).val() != '' && $(this).val() != 0) {
						$(this).change();
					}
				});
			}
			doneSaving();
		}
	});
}
function filterClassifications() {
	var region = $('select[name=region]').val();
	$('select[name=classification] option').each(function() {
		var classification = this;
		var class_regions = $(this).data('regions');
		$(this).show();
		class_regions.forEach(function(class_region) {
			if(region != '' && region != undefined && region != class_region) {
				$(classification).hide();
			}
		});
	});
	$('select[name=classification]').trigger('change.select2');
}
function filterNotiBusinessContacts() {
	var businessid = $('#noti_businessid').val();
	$('[name="noti_contacts[]"]').find('option').hide()
	$('[name="noti_contacts[]"]').find('option[data-businessid='+businessid+']').show();
}
function addTreatmentChart(link) {
	var patientformid = $(link).data('patientformid');
	window.location.href = "../Treatment/add_manual.php?patientformid="+patientformid+"&ticketid="+ticketid+"&action=view&from_url="+window.location.protocol+"//"+window.location.hostname+"/Ticket/index.php?edit="+ticketid;
}
function sendInventoryReminder(id) {
	$.post('ticket_ajax_all.php?action=inventory_reminder', { id: id }, function(response) { alert('A reminder has been sent!'); });
}
function archive(override = '', recurrence_confirm_skip = 0, delete_recurrences = 0) {
	if($('#sync_recurrences').val() == 1 && recurrence_confirm_skip == 0) {
		if(confirm('Would you like to delete all Recurrences?')) {
			return archive(override, 1, 1);
		} else {
			return archive(override, 1, 0);
		}
	} else {
		var confirmed = true;
		if(override != 'override') {
			confirmed = confirm('Are you sure you want to delete this?');
		}
		if(confirmed) {
			if(ticketid > 0) {
				ticketid_list.push(ticketid);
			}
			ticketid_list.forEach(function(ticket) {
				if(ticket > 0) {
					$.ajax({
						url: 'ticket_ajax_all.php?action=archive',
						method: 'POST',
						data: {
							ticketid: ticket,
							delete_recurrences: delete_recurrences
						},
						success: function(response) {
							if(typeof window.top.reload_all_data == 'function') {
								window.top.reload_all_data();
							} else if(typeof window.top.reload_all_data_month == 'function') {
								window.top.reload_all_data_month();
							}
							return true;
						}
					});
				}
			});
		} else {
			return false;
		}
	}
}
function dialogDeleteNote(a) {
	back_url = $(a).attr('href');
	$('#dialog_delete_note').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		open: function() {
			destroyInputs('#dialog_delete_note');
			initInputs('#dialog_delete_note');
		},
		buttons: {
			"Add Note and Delete": function() {
				var ticket = ticketid;
				var note = $('[name="delete_note"]').val();
				$.ajax({
					url: '../Ticket/ticket_ajax_all.php?action=add_delete_note',
					method: 'POST',
					data: {
						ticketid: ticket,
						note: note
					},
					success: function(response) {
						archive('override');
						window.location.replace(back_url);
						$(this).dialog('close');
					}
				});
			},
			"Delete Without Note": function() {
				archive('override');
				window.location.replace(back_url);
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
}
function saveAddress(idClass, contactid) {
	$.ajax({
		url: 'ticket_ajax_all.php?action=contact_address',
		method: 'POST',
		data: {
			contactid: contactid,
			address: $(idClass+'[name=address]').val(),
			city: $(idClass+'[name=city]').val(),
			province: $(idClass+'[name=province]').val(),
			postal: $(idClass+'[name=postal_code]').val(),
			country: $(idClass+'[name=country]').val(),
			google_link: $(idClass+'[name=map_link]').val()
		}
	})
}
function setBilling() {
	var total = 0;
	$('.billing tr').each(function() {
		var line = $(this).find('[name=billing_sub]').val() * 1;
		if(line > 0) {
            var surcharge = $(this).find('.surcharge').val();
            if(surcharge > 0) {
                line += (line * surcharge / 100);
            }
			var discount = 0;
			if($(this).find('.discount_type').val() == '%') {
				discount = $(this).find('.discount').val() * line / 100;
			} else {
				discount = $(this).find('.discount').val();
			}
			if(discount == '' || discount == undefined) {
				discount = 0;
			}
			line -= discount;
			$(this).find('[data-title=Total]').html('$'+round2Fixed(line));
			total += line;
		}
	});
	var total_discount = 0;
	if($('[name=billing_discount_type]').first().val() == '%') {
		total_discount = $('[name=billing_discount]').first().val() * total / 100;
	} else {
		total_discount = $('[name=billing_discount]').first().val();
	}
	total -= total_discount;
	$('[name=services_cost]').filter(function() { return $(this).data('manual') < 1 && (this.value != total || this.value.indexOf(0,'') === false || total != 0); }).val(total).change();
}
function dialogQuickReminder() {
	$('#dialog_quick_reminder').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"Add Reminder": function() {
				var ticket = ticketid;
				var reminder_staff = $('[name="quick_reminder_staff[]"]').val();
				var reminder_text = $('[name="quick_reminder_text"]').val();
				var reminder_date = $('[name="quick_reminder_date"]').val();
				$.ajax({
					url: '../Ticket/ticket_ajax_all.php?action=add_ticket_reminder',
					method: 'POST',
					data: { ticket: ticket, staff: reminder_staff, reminder: reminder_text, reminder_date: reminder_date },
					success: function(response) {
					}
				});
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
}
function initLocks() {
	$('.tab_lock_toggle_link:visible').closest('li').off('click').click(function() {
		$('#tab_section_'+$(this).closest('a').data('tab-target')).find('.tab_lock_toggle').click();
		return false;
	});
	$('.tab_lock_toggle').off('click').click(function() {
		$(this).closest('.tab-section').find('.locked,.lockable').toggle();
		$(this).closest('.tab-section').find('[name=lock_tabs]').data('toggle',1);
		$('[name=unlocked_tabs]').val('');
		$('[name=lock_tabs]').filter(function() { return $(this).data('toggle') == 1; }).each(function() {
			$('[name=unlocked_tabs]').val($('[name=unlocked_tabs]').val()+','+this.value);
		});
		$('[name=unlocked_tabs]').change();
		$('.tile-sidebar a[data-tab-target="'+$(this).closest('.tab-section').find('[name=lock_tabs]').val()+'"]').next('ul').show();
		$('.tile-sidebar a[data-tab-target="'+$(this).closest('.tab-section').find('[name=lock_tabs]').val()+'"]').find('li').off('click').find('.tab_lock_toggle_link').hide();
		$('.main-screen').scroll();
	});
}
function addNote(type, btn, force_allow = 0) {
	if(ticketid > 0) {
		$(btn).nextAll().find('.ticket_comments,.extra_billing').first().addClass('reload');
		overlayIFrameSlider('../Ticket/edit_ticket_tab.php?ticketid='+ticketid+'&edit='+ticketid+'&tab=ticket_comment&comment='+type+'&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+'&force_allow='+force_allow,'75%',false,true);
	} else {
		alert('Please create the '+ticket_name+' before adding notes.');
	}
}
function addCommunication(type, method) {
	if(ticketid > 0) {
		$('.ticket_communication[data-type='+type+'][data-method='+method+']').first().addClass('reload');
		overlayIFrameSlider('../Email Communication/add_email.php?type='+type+'&ticketid='+ticketid,'75%',false,true,'auto',true);
	} else {
		alert('Please create the '+ticket_name+' before adding communications.');
	}
}
function addContactNote(btn, clientid = '', force_allow = 0) {
	if($('#clientid').val() != undefined) {
		clientid = $('#clientid').val();
	}
	if(clientid > 0) {
		overlayIFrameSlider('../Ticket/edit_ticket_tab.php?ticketid='+ticketid+'&edit='+ticketid+'&tab=ticket_comment&contact_note=1&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+'&clientid='+clientid+'&force_allow='+force_allow,'75%',false,true);
	} else {
		alert('Please select a Contact before adding notes.');
	}
}
function addSiteNote(btn, siteid = '', force_allow = 0) {
	if(('#siteid').val() != undefined) {
		siteid = $('#siteid').val();
	}
	if(siteid > 0) {
		overlayIFrameSlider('../Ticket/edit_ticket_tab.php?ticketid='+ticketid+'&edit='+ticketid+'&tab=ticket_comment&contact_note=1&action_mode='+$('#action_mode').val()+'&estimate_mode='+$('#estimate_mode').val()+'&clientid='+siteid+'&force_allow='+force_allow,'75%',false,true);
	} else {
		alert('Please select a Site before adding notes.');
	}
}
function addServices(btn) {
	if(ticketid > 0) {
		overlayIFrameSlider('../Ticket/edit_ticket_tab.php?ticketid='+ticketid+'&edit='+ticketid+'&tab=ticket_info&add_service_iframe=1','75%',false,true);
		$(document).on("overlayIFrameSliderLoad", function() {
			window.parent.$('.iframe_overlay').click(function() {
				reload_service_checklist();
			});
		});
	} else {
		alert('Please create the '+ticket_name+' before adding notes.');
	}
}
function displayCustomerHistory() {
	overlayIFrameSlider('../Ticket/ticket_history.php?ticketid='+ticketid,'auto',true,true);
}
function cancelClick() {
	alert('Please mark yourself as arrived first.');
	return false;
}
function openFullView() {
	window.top.location.href = location.href.replace(/([&]*mode=iframe|[&]*calendar_view=(true|false))/g,'');
}
function submitApproval(status, email) {
	
}
function approve(field, status) {
	
}
function dialogCreateRecurrence(a, edit_recurrences = '') {
	if($('#sync_recurrences').val() == 1 && edit_recurrences == '') {
		$('#dialog_recurrence_settings').dialog({
			resizable: true,
			height: "auto",
			width: ($(window).width() <= 800 ? $(window).width() : 800),
			modal: true,
			buttons: {
				"Edit Recurrence Settings": function() {
					dialogCreateRecurrence(a, 'edit');
					$(this).dialog('close');
				},
				"Create New Recurrences": function() {
					dialogCreateRecurrence(a, 'create');
					$(this).dialog('close');
				}
			}
		});
	} else {
		if(ticketid > 0) {
			back_url = $(a).attr('href');
			var buttons = '';
			if(edit_recurrences == 'edit') {
				$('#dialog_create_recurrence').prop('title', 'Edit Recurrence Settings');
				$.ajax({
					url: '../Ticket/ticket_ajax_all.php?action=get_recurrence_settings',
					method: 'POST',
					data: { ticketid: ticketid },
					success: function(response) {
						var response = JSON.parse(response);
						$('[name="recurrence_start_date"]').val(response.start_date).change();
						$('[name="recurrence_end_date"]').val(response.end_date).change();
						$('[name="recurrence_repeat_type"]').val(response.repeat_type).change();
						$('[name="recurrence_repeat_monthly"]').val(response.repeat_monthly).change();
						$('[name="recurrence_repeat_interval"]').val(response.repeat_interval).change();
						$('[name="recurrence_repeat_days[]"]').prop('checked', false);
						$('[name="recurrence_repeat_days[]"]').each(function() {
							if(response.repeat_days.indexOf(this.value) != -1) {
								$(this).prop('checked', true);
							}
						});
					}
				});
				buttons = {
					"Edit Recurrence Settings": function() {
						var ticket = $('#ticketid').val();
						var recurrence_start_date = $('[name="recurrence_start_date"]').val();
						var recurrence_end_date = $('[name="recurrence_end_date"]').val();
						var recurrence_repeat_type = $('[name="recurrence_repeat_type"]').val();
						var recurrence_repeat_monthly = $('[name="recurrence_repeat_monthly_type"]').val();
						var recurrence_repeat_interval = $('[name="recurrence_repeat_interval"]').val();
						var recurrence_repeat_days = [];
						$('[name="recurrence_repeat_days[]"]:checked').each(function() {
							recurrence_repeat_days.push(this.value);
						});
						var recurrence_data = { ticketid: ticket, start_date: recurrence_start_date, end_date: recurrence_end_date, repeat_type: recurrence_repeat_type, repeat_monthly: recurrence_repeat_monthly, repeat_interval: recurrence_repeat_interval, repeat_days: recurrence_repeat_days };
						$.ajax({
							url: '../Ticket/ticket_ajax_all.php?action=create_recurrence_tickets&validate=1&edit=1',
							method: 'POST',
							data: recurrence_data,
							success: function(response) {
								var response = JSON.parse(response);
								if(response.success == false) {
									alert(response.message);
								} else if(response.success == true) {
									if(confirm(response.message)) {
										$('#dialog_create_recurrence').closest('.ui-dialog').find('button:contains(\"Create Recurrence\")').prop('disabled', true).text('Creating...');
										$.ajax({
											url: '../Ticket/ticket_ajax_all.php?action=create_recurrence_tickets&edit=1',
											method: 'POST',
											data: recurrence_data,
											success: function(response) {
												alert(response);
												window.location.reload();
												$('#sync_recurrences').val(1);
												$('.sync_recurrences_note').show();
												$('#dialog_create_recurrence').dialog('close');
											}
										});
									}
								}
							}
						});
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				}
			} else {
				$('#dialog_create_recurrence').prop('title', 'Create New Recurrences');
				$('[name="recurrence_start_date"]').val($('[name="recurrence_start_date"]').data('current-day'));
				$('[name="recurrence_end_date"]').val('');
				$('[name="recurrence_repeat_interval"]').val(1);
				$('[name="recurrence_repeat_type"]').val('week').change();
				$('[name="recurrence_repeat_monthly_type"]').val('day').change();
				$('[name="recurrence_repeat_days[]"]').prop('checked', false);
				buttons = {
					"Create New Recurrences": function() {
						var ticket = $('#ticketid').val();
						var recurrence_start_date = $('[name="recurrence_start_date"]').val();
						var recurrence_end_date = $('[name="recurrence_end_date"]').val();
						var recurrence_repeat_type = $('[name="recurrence_repeat_type"]').val();
						var recurrence_repeat_monthly = $('[name="recurrence_repeat_monthly_type"]').val();
						var recurrence_repeat_interval = $('[name="recurrence_repeat_interval"]').val();
						var recurrence_repeat_days = [];
						$('[name="recurrence_repeat_days[]"]:checked').each(function() {
							recurrence_repeat_days.push(this.value);
						});
						var recurrence_data = { ticketid: ticket, start_date: recurrence_start_date, end_date: recurrence_end_date, repeat_type: recurrence_repeat_type, repeat_monthly: recurrence_repeat_monthly, repeat_interval: recurrence_repeat_interval, repeat_days: recurrence_repeat_days };
						$.ajax({
							url: '../Ticket/ticket_ajax_all.php?action=create_recurrence_tickets&validate=1',
							method: 'POST',
							data: recurrence_data,
							success: function(response) {
								var response = JSON.parse(response);
								if(response.success == false) {
									alert(response.message);
								} else if(response.success == true) {
									if(confirm(response.message)) {
										$('#dialog_create_recurrence').closest('.ui-dialog').find('button:contains(\"Create Recurrence\")').prop('disabled', true).text('Creating...');
										$.ajax({
											url: '../Ticket/ticket_ajax_all.php?action=create_recurrence_tickets',
											method: 'POST',
											data: recurrence_data,
											success: function(response) {
												alert(response);
												window.location.reload();
												$('#sync_recurrences').val(1);
												$('.sync_recurrences_note').show();
												$('#dialog_create_recurrence').dialog('close');
											}
										});
									}
								}
							}
						});
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				}
			}

			$('#dialog_create_recurrence').dialog({
				resizable: true,
				height: "auto",
				width: ($(window).width() <= 800 ? $(window).width() : 800),
				modal: true,
				open: function() {
					destroyInputs('#dialog_create_recurrence');
					initInputs('#dialog_create_recurrence');
				},
				buttons: buttons
			});
		} else {
			alert('Please put at least one detail in this '+$('[name="global_ticket_noun"]').val()+' before creating recurrences.');
		}
	}
}
function checkStaffShifts(staffid = '') {
	var ticketid = $('#ticketid').val();
	return $.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=check_staff_shifts',
		method: 'POST',
		data: { ticketid: ticketid, staffid: staffid }
	});
}
function filterStaffNoShift() {
	var ticketid = $('#ticketid').val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=get_staff_no_shifts',
		method: 'POST',
		data: { ticketid: ticketid },
		success: function(response) {
			var response = JSON.parse(response);
			$('select[name="item_id"][data-table="ticket_attached"][data-type="Staff"] option').show();
			response.staff_list.forEach(function(staffid) {
				$('select[name="item_id"][data-table="ticket_attached"][data-type="Staff"] option[value="'+staffid+'"]').hide();
			});
			$('select[name="item_id"][data-table="ticket_attached"][data-type="Staff"]').trigger('change.select2');
		}
	});
}
function setHoursOfOperation(select) {
	if(ticketid > 0) {
		if($(select).data('hours-of-operation') == 1) {
			var block = $(select).closest('.scheduled_stop');
			if($(block).find('[name="to_do_start_time"]').data('disabled') != 1) {
				if($(select).find('option:selected').data('warehouseid') > 0) {
					var warehouseid = $(select).find('option:selected').data('warehouseid');
					var to_do_date = $(block).find('[name="to_do_date"]').val();
					var to_do_start_time = $(block).find('[name="to_do_start_time"]').val();
					$.ajax({
						url :'../Ticket/ticket_ajax_all.php?action=get_hours_of_operation',
						method: 'POST',
						data: {
							ticketid: ticketid,
							warehouseid: warehouseid,
							to_do_date: to_do_date,
							to_do_start_time: to_do_start_time
						},
						success: function(response) {
							var response = JSON.parse(response);
							if(response.start_time == 'closed') {
								$(block).find('[name="to_do_start_time"]').removeClass('datetimepicker-15');
								$(block).find('[name="to_do_start_time"]').val('Closed').change().prop('disabled', true);
							} else {
								if(response.start_time == '') {
									response.start_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-mintime');
									response.end_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-maxtime');
								}
								destroyInputs('.stop_scheduled_time');
								$(block).find('[name="to_do_start_time"]').addClass('datetimepicker-15');
								$(block).find('[name="to_do_start_time"]').prop('disabled', false);
								$(block).find('[name="to_do_start_time"]').data('datetimepicker-mintime', response.start_time);
								$(block).find('[name="to_do_start_time"]').data('datetimepicker-maxtime', response.end_time);
								initInputs('.stop_scheduled_time');
								if(response.to_do_start_time != to_do_start_time && response.to_do_start_time != '') {
									$(block).find('[name="to_do_start_time"]').val(response.to_do_start_time).change();
								}
								setRestrictedHours($(block).find('[name="to_do_start_time"]'), response.start_time, response.end_time);
							}
						}
					});
				} else {
					start_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-mintime');
					end_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-maxtime');
					destroyInputs('.stop_scheduled_time');
					$(block).find('[name="to_do_start_time"]').prop('disabled', false);
					$(block).find('[name="to_do_start_time"]').data('datetimepicker-mintime', start_time);
					$(block).find('[name="to_do_start_time"]').data('datetimepicker-maxtime', end_time);
					initInputs('.stop_scheduled_time');
					setRestrictedHours($(block).find('[name="to_do_start_time"]'), start_time, end_time);
				}
			}
		}
	}
}
function setRestrictedHours(input, start_time = '', end_time = '') {
	if(ticketid > 0) {
		var block = $(input).closest('.scheduled_stop');
		if($(block).find('[name="to_do_start_time"]').data('disabled') != 1 && $(block).find('[name="to_do_start_time"]').val() != 'Closed') {
			if(start_time == '') {
				start_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-mintime');
			}
			if(end_time == '') {
				end_time = $(block).find('[name="to_do_start_time"]').data('default-datetimepicker-maxtime');
			}
			var to_do_date = $(block).find('[name="to_do_date"]').val();
            if(to_do_date != '' && to_do_date != undefined) {
                var to_do_start_time = $(block).find('[name="to_do_start_time"]').val();
                $.ajax({
                    url :'../Ticket/ticket_ajax_all.php?action=get_restricted_hours',
                    method: 'POST',
                    data: {
                        ticketid: ticketid,
                        to_do_date: to_do_date,
                        to_do_start_time: to_do_start_time,
                        start_time: start_time,
                        end_time: end_time
                    },
                    success: function(response) {
                        var response = JSON.parse(response);
                        if(response.start_time == '') {
                            response.start_time = start_time;
                            response.end_time = end_time;
                        }
                        destroyInputs('.stop_scheduled_time');
                        $(block).find('[name="to_do_start_time"]').prop('disabled', false);
                        $(block).find('[name="to_do_start_time"]').data('datetimepicker-mintime', response.start_time);
                        $(block).find('[name="to_do_start_time"]').data('datetimepicker-maxtime', response.end_time);
                        initInputs('.stop_scheduled_time');
                        if(response.to_do_start_time != to_do_start_time && response.to_do_start_time != '') {
                            $(block).find('[name="to_do_start_time"]').val(response.to_do_start_time).change();
                        }
                    }
                });
            }
		}
	}
}
function setHoursOfOperationToDoDate(input) {
	var stop_type = $(input).closest('.scheduled_stop').find('select[name="type"][data-table="ticket_schedule"]');
	setHoursOfOperation(stop_type);
	if($(stop_type).data('hours-of-operation') != 1) {
		setRestrictedHours($(input).closest('.scheduled_stop').find('[name="to_do_start_time"]'));
	}
}
function toggleIncognito(img) {
    if($(img).hasClass('black-color')) {
        $(img).removeClass('black-color').addClass('red-color');
        $('img[src*=incognito][data-field="'+$(img).data('field')+'"]').closest('span').find('[name=incognito_fields]').val($(img).data('field')).change();
    } else {
        $(img).addClass('black-color').removeClass('red-color');
        $('img[src*=incognito][data-field="'+$(img).data('field')+'"]').closest('span').find('[name=incognito_fields]').val('').change();
    }
}
function initSelectOnChanges() {
	try {
		setServiceFilters();
	} catch(e) { }
	setSave();
	if($('select[name=businessid]').length > 0) {
		$('select[name=businessid]').off('change',businessFilter).change(businessFilter);
	}
	if($('select#clientid').length > 0) {
		$('select#clientid').off('change',clientFilter).change(clientFilter);
	}
	if($('select#projectid').length > 0) {
		$('select#projectid').off('change',projectFilter).change(projectFilter);
	}
	$('select[name="siteid"],select[name="siteid[]"]').not('#po_siteid').change(function() {
		siteSelect(this);
	});
	if($('#salesorderid').length > 0) {
		$('#salesorderid').off('change',setDeliveryOrder).change(setDeliveryOrder);
	}
	if($('select[name$="_eq_category"]').length > 0) {
		$('select[name$="_eq_category"]').off('change',filterAssignedEquipment).change(filterAssignedEquipment);
		$('select[name$="_eq_make"]').off('change',filterAssignedEquipment).change(filterAssignedEquipment);
		$('select[name$="_eq_model"]').off('change',filterAssignedEquipment).change(filterAssignedEquipment);
		$('select[name="equipmentid"]').off('change',filterAssignedEquipment).change(filterAssignedEquipment);
	}
	$('select[name="eq_category"]').change(function() {
		filterEquipment(this);
	});
	$('select[name="eq_make"]').change(function() {
		filterEquipment(this);
	});
	$('select[name="eq_model"]').change(function() {
		filterEquipment(this);
	});
	$('select[name="item_id"][data-type="equipment"]').change(function() {
		filterEquipment(this);
	});
	$('select[name="mat_category"]').change(function() {
		filterMaterials(this);
	});
	$('select[name="mat_sub"]').change(function() {
		filterMaterials(this);
	});
	$('select[name="item_id"][data-type="material"]').change(function() {
		filterMaterials(this);
	});
	$('select[name="sign_name"]').change(function() {
		return signNameUpdate(this);
	});
	$('select[name="witness_name"]').change(function() {
		return witnessNameUpdate(this);
	})
	$('select[name="recur_frequency"]').change(function() {
		if(this.value == 'weekly') {
			$('.recur_days').show();
		} else {
			$('.recur_days').hide();
		}
	});
	$('select[name="task_category123"]').change(function() {
		selectBoard(this);
	});
	$('select[name="task_reassign123"]').change(function() {
		selectCheckliststaff(this);
	});
	$('select[name="task_status123"]').change(function() {
		selectTaskliststatus(this);
	});
	$('select#po_siteid').change(function() {
		site_select($(this).val());
	});
	$('select#po_contactid').change(function() {
		set_new_who();
	});
	$('select[name=carrier],select[name=agentid],select[name=notifyid],select[name=vendor],select[name=warehouse_location],select[name=item_id][data-type=shipping_list],select[name=item_id][data-type=other_list],select[name=item_id][data-type=residue]').off('change',manualSelect).change(manualSelect);
	$('#collapse_inventory select,#tab_section_ticket_inventory select,#collapse_inventory_detailed select,#tab_section_ticket_inventory_detailed select').off('change',filterInventory).change(filterInventory);
	$('.sign_off_click').off('click',sign_off_complete).click(sign_off_complete);
	$('.force_sign_off_click').off('click',sign_off_complete_force).click(sign_off_complete_force);
	$('#noti_businessid').off('change',filterNotiBusinessContacts).change(filterNotiBusinessContacts);
	$('select[name=region]').off('change',filterClassifications).change(filterClassifications);
	$('[name=one_time]').off('change').change(function() {
		var one_time = (this.checked ? 'true' : 'false');
		$(this).closest('div').find('input').first().data('one-time',one_time).change();
	});
	$('[name=item_id][data-type="Staff"]').off('change',filterPositions).change(filterPositions);
	$('.billing input,.billing select,[name=billing_discount],[name=billing_discount_type]').off('change',setBilling).change(setBilling);
	$('[name="region"],[name="con_location"],[name="classification"]').change(function() {
        if(typeof filterRegLocClass == 'function') {
            filterRegLocClass();
        }
	});
	if($('[name="customer_service_template"]').length > 0) {
		try {
			$('[name="customer_service_template"]').off('change',addCustomerServiceTemplate).change(function() {
				addCustomerServiceTemplate(this);
			});
		} catch(e) {
			$('[name="customer_service_template"]').change(function() {
				addCustomerServiceTemplate(this);
			});
		}
	}
	if($('[name="load_service_template"]').length > 0) {
		try {
			$('[name="load_service_template"]').off('change',loadServiceTemplate).change(function() {
				loadServiceTemplate(this);
			});
		} catch(e) {
			$('[name="load_service_template"]').change(function() {
				loadServiceTemplate(this);
			});
		}
	}
	$('select.po_num_dropdown').change(function() {
		poNumSelect(this);
	});
	$('select[name="po_line_range_min"],select[name="po_line_range_max"]').change(function() {
		poLineRangeChange(this);
	});
	$('select[name="type"][data-table="ticket_schedule"]').off('change',setHoursOfOperation).change(function() {
		setHoursOfOperation(this);
	});
	$('[name="to_do_date"][data-table="ticket_schedule"]').off('change',setHoursOfOperationToDoDate).change(function() {
		setHoursOfOperationToDoDate(this);
	});
}
