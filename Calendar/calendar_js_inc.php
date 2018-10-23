<?php $calendar_hide_left_time = get_config($dbc, 'calendar_hide_left_time'); ?>
<script>
var offline_mode = <?= $_GET['offline'] > 0 ? 1 : 0 ?>;
var scroll_to_today = true;
var today_date = '<?= date('Y-m-d') ?>';
var end_of_list = false;
var start_of_list = false;
var lastScrollLeft = 0;
$(document).ready(function() {
	global_class_users = {};
	$('.search-text').keyup(function() { searchTextAll(this); });
	$('body').keyup(function(e) {
		if(e.key == 'Escape') {
			hideMenu(e);
		}
	});
	$('body').click(hideMenu);
	setSelectOnChange();
	initTicketHoverStaff();

	<?php if($_GET['mobile_tab'] == 'list') { ?>
		$('span.calendar_menu_list').click();
	<?php } ?>
	$('.block-button.legend-block').on('mouseover', function() { toggleTicketLegend('show') });
	$('.block-button.legend-block').on('mouseout', function() { toggleTicketLegend('hide') });
	<?php if($_GET['view'] != 'monthly' && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'day_summary' && $_GET['mode'] != 'summary') { ?>
		calendarScrollLoad();
	<?php } ?>

	setAutoRefresh();
	setUrlWithCurrentDate();
	setLastActive();
	getLastActive();
	initLastActive();
});
$(document).on('click','#calendar_div a',function() { loadUrlWithCurrentDate(this); });
$(document).on("overlayIFrameSliderLoad", function(e) {
	$('[name="calendar_iframe"]').contents().find('html').css('background-color', '#fff');
	var no_confirm = e.originalEvent.detail.no_confirm;
	window.parent.$('.iframe_overlay').off('click').click(function() {
		if(no_confirm || confirm('Closing out of this window will discard your changes. Are you sure you want to close the window?')) {
			$('.iframe_overlay').hide();
			$('.iframe_overlay .iframe iframe').off('load').attr('src', '/blank_loading_page.php');
			$('html').prop('onclick',null).off('click');
			var calendar_view = $('#calendar_view').val();
			var calendar_mode = $('#calendar_mode').val();
			if(calendar_view == 'monthly' || calendar_mode == 'ticket_summary') {
				reload_all_data_month();
			} else {
				reload_all_data();
			}
		}
	});
	window.parent.$('[name="calendar_iframe"]').off('load').load(function() {
		$('.iframe_overlay').hide();
		$('.hide_on_iframe').show();
		$(this).off('load').attr('src', '/blank_loading_page.php');
		var calendar_view = $('#calendar_view').val();
		var calendar_mode = $('#calendar_mode').val();
		if(calendar_view == 'monthly' || calendar_mode == 'ticket_summary') {
			reload_all_data_month();
		} else {
			reload_all_data();
		}
	});
});
$(document).on('click', '.active_blocks .block-item.active', function() {
	var parent = $(this).closest('.active_blocks');
	var accordion = $(parent).data('accordion');
	var activevalue = $(this).data('activevalue');
	$('#'+accordion).find('.block-item.active[data-activevalue="'+activevalue+'"]').closest('a').click();
});
var auto_refresh_calendar = '';
function setAutoRefresh() {
	clearTimeout(auto_refresh_calendar);
	var calendar_view = $('#calendar_view').val();
	var refresh_time = parseInt($('#calendar_auto_refresh').val()) * 1000;
	if(refresh_time != '' && refresh_time > 0) {
		if(calendar_view == 'monthly') {
			auto_refresh_calendar = setTimeout(function() { reload_all_data_month(); }, refresh_time);
		} else {
			auto_refresh_calendar = setTimeout(function() { reload_all_data(); }, refresh_time);
		}
	}
}
function setLastActive() {
	var calendar_type = $('#calendar_type').val();
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=set_last_active',
		method: 'POST',
		data: { type: calendar_type, contactid: '<?= $_SESSION['contactid'] ?>' },
		success: function(response) {
			autoSetLastActive();
		}
	});
}
var auto_set_last_active = '';
function autoSetLastActive() {
	clearTimeout(auto_set_last_active);
	auto_set_last_active = setTimeout(function() {
		setLastActive();
	}, 60000);
}
function getLastActive() {
	var calendar_type = $('#calendar_type').val();
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_last_active',
		method: 'POST',
		data: { type: calendar_type },
		success: function(response) {
			var response = response.split('*#*');
			var active_count = response[0];
			var active_html = response[1];
			$('.online-users-count').text(active_count);
			$('.online-users-block').html(active_html);
			autoGetLastActive();
		}
	});
}
var auto_get_last_active = '';
function autoGetLastActive() {
	clearTimeout(auto_get_last_active);
	auto_get_last_active = setTimeout(function() {
		getLastActive();
	}, 60000);
}
function initLastActive() {
	$('.online-users').off('mouseover').on('mouseover', function(e) {
		getLastActive();
		$('.online-users-block').show();
	});
	$('.online-users').off('mouseout').on('mouseout', function() {
		$('.online-users-block').hide();
	});
}
function loadUrlWithCurrentDate(a) {
	var href = $(a).attr('href');
	if(href != undefined && href != '' && href != '?' && $(a).data('toggle') != 'collapse') {
		var params = getQueryStringArray(href);
		if(params["date_override"] == 1) {
			delete params["date_override"];
		} else {
			params["date"] = $('#calendar_start').val();
		}
		if(href.indexOf('?') != -1) {
			href = href.split('?')[0];
		}
		var newUrl = decodeURIComponent(href+"?"+$.param(params));

		$(a).prop('href', newUrl);
	}
	return;
}
function setUrlWithCurrentDate() {
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
	query_string_arr["date"] = $('#calendar_start').val();
	query_string_arr["view"] = $('#calendar_view').val();
	var new_url = decodeURIComponent("?"+$.param(query_string_arr));
	window.history.replaceState(null, '', new_url);
}
<?php $calendar_ticket_hover_staff = get_config($dbc, 'calendar_ticket_hover_staff'); ?>
function initTicketHoverStaff() {
	<?php if($calendar_ticket_hover_staff == 1 && $_GET['type'] != 'schedule' && $_GET['type'] != 'event') { ?>
		$('.calendar_view .used-block,.calendar_table .sortable-blocks').off('mouseover').on('mouseover', function(e) {
			if(!$(e.target).hasClass('drag-handle') && !$(e.target).hasClass('ui-resizable-handle')) {
				displayTicketStaff(this);
			}
		});
		$('.calendar_view .used-block,.calendar_table .sortable-blocks').off('mouseout').on('mouseout', function() {
			hideTicketStaff();
		});
	<?php } ?>
}
function displayTicketStaff(block) {
    var left  = (event.clientX + 25) + "px";
    var top  = event.clientY + "px";

	var ticketid = $(block).data('ticket');
	if(ticketid != undefined && ticketid > 0) {
		if($('#ticket_assigned_staff').not(':visible')) {
		    $('#ticket_assigned_staff').css('left', left);
		    $('#ticket_assigned_staff').css('top', top);
			$('#ticket_assigned_staff').show().html('Loading...');
			$.ajax({
				url: '../Calendar/calendar_ajax_all.php?fill=get_ticket_staff',
				method: 'POST',
				data: { ticketid: ticketid },
				success: function(response) {
				    $('#ticket_assigned_staff').html(response);
				}
			});
		}
	} else {
		hideTicketStaff();
	}
}
function hideTicketStaff() {
	$('#ticket_assigned_staff').hide().html('Loading...');
}
function toggleTicketLegend(display) {
	if(display == 'show') {
		$('.ticket-status-legend').show();
	} else {
		$('.ticket-status-legend').hide();
	}
}
function hideMenu(e) {
	if((e.type == 'click' && $(e.target).closest('.subtab-menu').length == 0) || e.type == 'keyup') {
		$('.menu-show').each(function() {
			$(this).removeClass('menu-show');
			$(this).find('img').toggleClass('counterclockwise');
		});
	}
}
function toggleMenu(link) {
	$(link).closest('.subtab-menu').toggleClass('menu-show');
	$(link).find('img').toggleClass('counterclockwise');
	if($(link).closest('.subtab-menu').hasClass('menu-show')) {
		$('.menu-show').not($(link).closest('.subtab-menu')).find('img').toggleClass('counterclockwise');
		$('.menu-show').not($(link).closest('.subtab-menu')).removeClass('menu-show');
	}
}
function overlayIFrame(url) {
	$('.iframe_overlay .iframe iframe').height(0);
	$('.iframe_overlay .iframe .iframe_loading').show();
	$('.iframe_overlay .iframe iframe').prop('src',url);
	$('.iframe_overlay .iframe').height($('.main-screen').height());
	$('.iframe_overlay').show();
	$('.iframe_overlay .iframe iframe').load(function() {
		$('.iframe_overlay iframe').height($('.main-screen').height());
		$('.iframe_overlay').height($('.main-screen').height());
		$('.iframe_overlay .iframe .iframe_loading').hide();
		$(this).off('load').load(function() {
			$('.iframe_overlay').hide();
			$(this).off('load').attr('src', '');
			window.location.reload();
		});
	});
}
function universalAdd(link) {
    $( "#dialog-universal" ).dialog({
		resizable: false,
		height: "auto",
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
			<?php if(strpos(','.$wait_list.',', ',ticket,') !== FALSE) { ?>
		        "<?= TICKET_NOUN ?>": function() {
		        	overlayIFrameSlider($(link).data('ticketurl'));
		        	$(this).dialog('close');
		        },
	        <?php } ?>
			<?php if(strpos(','.$wait_list.',', ',appt,') !== FALSE) { ?>
		        "Appointment": function() {
		        	overlayIFrameSlider($(link).data('appturl'));
		        	$(this).dialog('close');
		        },
	        <?php } ?>
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
}
function searchTextAll(input) {
	var searchText = $(input).val();
	$('.sidebar.block-panels .panel-body a,.used-block').each(function() {
		if($(this).text().toString().toLowerCase().indexOf(searchText) == -1) {
			$(this).hide();
		} else {
			$(this).show();
		}
	});
}
function dispatchNewWorkOrder(data) {
	var region = $('#collapse_region .block-item.active').first().data('region');
	var location = $('#collapse_locations .block-item.active').first().data('location');
	var classification = $('#collapse_classifications .block-item.active').first().data('classification');
	var equipmentid = $(data).data('equipmentid');
	if(equipmentid == '' || equipmentid == undefined) {
		equipmentid = $('[id^=collapse_equipment] .block-item.active').first().data('equipment');
	}
	var equipment_assignmentid = $(data).data('equipment_assignmentid');
	var current_time = $(data).data('currenttime') != undefined ? $(data).data('currenttime') : '';
	var end_time = $(data).data('endtime') != undefined ? $(data).data('endtime') : '';
	var current_date = $(data).data('currentdate') != undefined ? $(data).data('currentdate') : '';
	overlayIFrameSlider("<?= WEBSITE_URL ?>/Ticket/index.php?calendar_view=true&equipmentid="+equipmentid+"&equipment_assignmentid="+equipment_assignmentid+"&current_time="+current_time+"&current_date="+current_date+"&calendar_region="+region+"&calendar_location="+location+"&calendar_classification="+classification+"&end_time="+end_time+"&new_ticket_calendar=true&edit=0");
}
function retrieveClassificationUsers(class_block) {
	var classification = $(class_block).data('classification');
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=retrieve_classification_users',
		method: 'POST',
		data: { classification: classification },
		success: function(response) {
			response_arr = response.split('*#*');
			user_count = response_arr[0];
			user_html = response_arr[1];
			$(class_block).find('.active_user_count').text(user_count);
			if(user_count > 0) {
				$(class_block).find('.active_user_count').show();
			} else {
				$(class_block).find('.active_user_count').hide();
			}
			global_class_users[classification] = user_html;
		}
	});
}
function displayActiveUsers(span) {
	var classification = $(span).closest('.block-item').data('classification');

    var left  = (event.clientX + 25) + "px";
    var top  = event.clientY + "px";

    $('#active_class_users').css('left', left);
    $('#active_class_users').css('top', top);
    $('#active_class_users').html(global_class_users[classification]).show();
}
function hideActiveUsers() {
	$('#active_class_users').html('').hide();
}
function checkTicketLastUpdated(ticket_table, ticketid, ticket_scheduleid, timestamp) {
	return $.ajax({
		async: false,
		url: '../Calendar/calendar_ajax_all.php?fill=check_ticket_last_updated',
		method: 'POST',
		data: { ticket_table: ticket_table, ticketid: ticketid, ticket_scheduleid: ticket_scheduleid, timestamp: timestamp }
	});
}
var reset_active = $.Deferred();
reset_active.resolve();
function changeDate(date, type = '') {
	reset_active.resolve();
	scroll_to_today = true;
	var summary_view = $('#retrieve_summary').val();
	var view = $('#calendar_view').val();
	var calendar_type = $('#calendar_type').val();
	var config_type = $('#calendar_config_type').val();
	if(date == '') {
		date = $('#calendar_start').val();
	}
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_calendar_dates',
		method: 'POST',
		data: { view: view, date: date, type: type, config_type: config_type },
		success: function(response) {
			response_arr = JSON.parse(response);
			$('#calendar_start').val(response_arr[0]);
			$('#calendar_date_heading').html('&nbsp;&nbsp;'+response_arr[1]);
			$('#calendar_dates').val(JSON.stringify(response_arr[2]));
			$('#calendar_dates_month').val(JSON.stringify(response_arr[2]));
			reset_active = $.Deferred();
			reload_equipment_assignment();
			$.when(reset_active).done(function(){
				if(summary_view == 1) {
					retrieve_whole_month();
				} else if(view == 'monthly') {
					clear_all_data_month();
					var reload_calendar = reload_calendar_month(response_arr[0]);
					reload_calendar.success(function() {
						if((calendar_type == 'ticket' || calendar_type == 'uni') && $('#collapse_teams').length > 0) {
							reload_teams();
						}
						reload_all_data_month();
					});
				} else {
					still_loading = 0;
					clear_all_data();
					if(typeof dispatchDraggable == 'function') {
						dispatchDraggable();
					}
					if(typeof teamsDraggable == 'function') {
						teamsDraggable();
					}
					if((calendar_type == 'ticket' || calendar_type == 'uni') && $('#collapse_teams').length > 0) {
						reload_teams();
					}
					reload_all_data();
				}
				setUrlWithCurrentDate();
			});
		}
	});
}
function changeView(view, anchor) {
	$('#time_html').remove();
	$('.block-button.view_button').removeClass('active blue');
	$(anchor).find('.block-button.view_button').addClass('active blue');
	$('.view_button_string').text($(anchor).text());

	$('#calendar_view').val(view);
	var promises = [];
	<?php if($_GET['type'] == 'schedule') { ?>
		promises.push($.ajax({
			url: '../Calendar/schedule_sidebar.php?<?= http_build_query($_GET) ?>&view='+view,
			success: function(response) {
				$('.collapsible').html(response);
				$('.sidebar.panel-group').css('padding-right','0');
				setTimeout(function() { toggle_columns() },500);
			}
		}));
	<?php } ?>
	promises.push($.ajax({
		url: '../Calendar/load_calendar_empty.php?<?= http_build_query($_GET) ?>&view='+view,
		success: function(response) {
			$('.calendar_view').html(response);
		}
	}));

	//When all ajax promises are done, reload the calendar data
	$.when.apply(null, promises).done(function(){
		if(typeof initDraggable == 'function') {
			initDraggable();
		}
		var calendar_start = $('#calendar_start').val();
		changeDate(calendar_start);
	});
}
function setSelectOnChange() {
	//Unbooked
	destroyInputs('.block-group.unbooked');
	$('select.unbooked_ticket_projecttype').off('change').change(function() { filterProjects(); filterTickets(); });
	$('select.unbooked_ticket_project').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_region').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_location').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_classification').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_customer').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_client').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_staff').off('change').change(function() { filterTickets(); });
	$('select.unbooked_ticket_status').off('change').change(function() { filterTickets(); });
	$('select.unbooked_wo_project').off('change').change(function() { filterWorkOrders(); });
	$('select.unbooked_wo_customer').off('change').change(function() { filterWorkOrders(); });
	$('select.unbooked_wo_staff').off('change').change(function() { filterWorkOrders(); });
	$('select.waitlist_patient').off('change').change(function() { filterInjuries(this); });
	$('select.unbooked_waitlist_patient').off('change').change(function() { filterWaitList(); });
	$('select.unbooked_waitlist_injury').off('change').change(function() { filterWaitList(); });
	$('select.unbooked_waitlist_type').off('change').change(function() { filterWaitList(); });
	initInputs('.block-group.unbooked');

	//Shifts
    $('select[name="shift_repeat_type"]').change(function() { changeDaysOfWeek(this); });
    $('select[name="shift_availability"]').change(function() { changeShiftAvailability(); });

    //Mobile
    $('select[name="calendar_type"]').change(function() { changeCalendarType(this); });
    $('select[name="calendar_contact"]').change(function() { changeCalendarContact(this); });
    $('select[name="calendar_region"]').change(function() { changeCalendarRegion(this); });
}

//Mobile Functions
function changeMobileMonth(link) {
	var url = $(link).attr('href');
	if(current_tab == 'list') {
		url += '&mobile_tab=list';
	}
	loadingOverlayShow('', '', '', true);
	window.location.href = url;
}
function changeCalendarType(sel) {
	loadingOverlayShow('', '', '', true);
	window.location.href = sel.value;
}
function changeCalendarContact(sel) {
	loadingOverlayShow('', '', '', true);
	window.location.href = sel.value;
}
function changeCalendarRegion(sel) {
	var region = '*#*'+sel.value+'*#*';
	$('[name="calendar_contact"] option').each(function() {
		var contact_region = '*#*'+$(this).data('region')+'*#*';
		if(contact_region.indexOf(region) != -1 || $(this).data('region') == '') {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}
function toggleCalendarAdd() {
	<?php if(count($mobile_calendar_add) > 1) { ?>
		$('#dialog-calendaradd').dialog({
			resizable: false,
			height: 'auto',
			width: ($(window).width() <= 500 ? $(window).width() : 500),
			modal: true,
			buttons: {
				<?php foreach ($mobile_calendar_add as $calendar_btn) { ?>
					"<?= $calendar_add_urls[$calendar_btn][0] ?>": function() {
						overlayIFrameSlider('<?= $calendar_add_urls[$calendar_btn][1] ?>');
						$(this).dialog('close');
					},
				<?php } ?>
		        Cancel: function() {
		        	$(this).dialog('close');
		        }
			}
		});
	<?php } else { ?>
		overlayIFrameSlider('<?= $calendar_add_urls[$mobile_calendar_add[0]][1] ?>');
	<?php } ?>
}
function toggleCalendarType() {
	$('#dialog-calendartype').dialog({
		resizable: false,
		height: 'auto',
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	})
}
function toggleCalendarView() {
	$('#dialog-calendarview').dialog({
		resizable: false,
		height: 'auto',
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
			<?php foreach ($mobile_calendar_views as $calendar_btn => $calendar_btn_label) { ?>
				"<?= $calendar_btn_label ?>": function () {
					window.location.href = '?type=<?= $_GET['type'] ?>&mode=<?= $calendar_btn ?>&region=<?= $_GET['region'] ?>';
					$(this).dialog('close');
				},
			<?php } ?>
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
}
function toggleCalendarContact() {
	$('#dialog-calendarcontact').dialog({
		resizable: false,
		height: 'auto',
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
}
function toggleMobileView(cell = '') {
	$('.calendar-mobile-block-item .menu-item').removeClass('active').addClass('inactive');
	$('#calendar-view-month').hide();
	$('#calendar-view-list').hide();
	$('#calendar-view-day').hide();
	$('.calendar-day-block').hide();
	$('#calendar-month-block').hide();
	if($(cell).hasClass('calendar_menu_list')) {
		current_tab = 'list';
		$('#calendar-view-list').show();
		$('.calendar_menu_list').addClass('active');
		$('#calendar-view-list tr').show();
		$('#calendar-month-block').show();
		var active_date = $('.calendar-mobile-date.active').closest('td').data('date');
	} else if($(cell).hasClass('calendar_menu_date')) {
		current_tab = 'date';
		$('#calendar-view-month').show();
		$('#calendar-view-list').show();
		$('.calendar_menu_date').addClass('active');
		var active_date = $('.calendar-mobile-date.active').closest('td').data('date');
		$('#calendar-view-list tr').hide();
		$('#calendar-view-list tr[data-date="'+active_date+'"]').show();
		$('#calendar-month-block').show();
	} else {
		if($(cell).find('.calendar-mobile-date').hasClass('active') || $(cell).hasClass('list-row') || $(cell).hasClass('calendar_menu_day')) {
			current_tab = 'day';
			if($(cell).hasClass('calendar_menu_day')) {
				if($(cell).data('type') == 'prev') {
					active_date = $(cell).closest('.calendar-day-block').data('prev');
				} else if($(cell).data('type') == 'next') {
					active_date = $(cell).closest('.calendar-day-block').data('next');
				}
				if(active_date == '' || active_date == undefined) {
					active_date = $('.calendar-mobile-date.active').closest('td').data('date');
				}
			} else if($(cell).hasClass('list-row')) {
				active_date = $(cell).data('date');
			} else {
				active_date = $('.calendar-mobile-date.active').closest('td').data('date');
			}
			$('#calendar-view-day').show();
			$('.calendar-mobile-date').removeClass('active');
			$('.calendar-mobile-date').filter(function() { return $(this).closest('td').data('date') == active_date }).addClass('active');
			$('#calendar-view-day table td,#calendar-view-day table th').filter(function() { return $(this).data('date') != 0 }).hide();
			$('#calendar-view-day table td,#calendar-view-day table th').filter(function() { return $(this).data('date') == active_date }).show();
			$('.calendar-day-block').filter(function() { return $(this).data('date') == active_date }).show();
			resizeBlocks();
			if($(window).scrollTop() > $('#calendar-view').offset().top - $('#calendar-menu').height()) {
				$('html,body').animate({
					scrollTop: $('#calendar-view').offset().top - $('#calendar-menu').height()
				}, 100);
			}
		} else {
			current_tab = 'date';
			$('#calendar-view-month').show();
			$('#calendar-view-list').show();
			$('.calendar-mobile-date').removeClass('active');
			$('.calendar_menu_date').addClass('active');
			$(cell).find('.calendar-mobile-date').addClass('active');
			var active_date = $('.calendar-mobile-date.active').closest('td').data('date');
			$('#calendar-view-list tr').hide();
			$('#calendar-view-list tr[data-date="'+active_date+'"]').show();
			$('#calendar-month-block').show();
		}
		$('#calendar_dates').val('["'+active_date+'"]');
		if(reloadDragResize != undefined) {
			reloadDragResize();
		}
	}
}

//SHIFT VIEWS
var calendar_type_main = '<?= $_GET['type'] ?>';
var calendar_config_type_main = '<?= $config_type ?>';
function loadShiftView() {
	if($('.shift_anchor').hasClass('active')) {
		$('#calendar_type').val(calendar_type_main);
		$('#calendar_config_type').val(calendar_config_type_main);
		$('.shift_btn').hide();
		$('.shift_anchor').removeClass('active');
	} else {
		calendar_type_main = $('#calendar_type').val();
		calendar_config_type_main = $('#calendar_config_type').val();
		$('#calendar_type').val('shift');
		$('#calendar_config_type').val('shift');
		$('.shift_btn').show();
		$('.shift_anchor').addClass('active');
	}
	var calendar_type = $('#calendar_type').val();
	var calendar_view = $('#calendar_view').val();
	var calendar_mode = $('#calendar_mode').val();
	var calendar_start = $('#calendar_start').val();
	<?php if($_GET['view'] == 'monthly') { ?>
		$.ajax({
			url: '../Calendar/monthly_display.php?type='+calendar_type+'&view='+calendar_view+'&mode='+calendar_mode+'&date='+calendar_start,
			method: 'GET',
			success: function(response) {
				$('.calendar_view').html(response);
				reload_resize_all_month();
				clear_all_data_month();
				reload_all_data_month();
				setLastActive();
				getLastActive();
			}
		});
	<?php } else { ?>
		$.ajax({
			url: '../Calendar/load_calendar_empty.php?type='+calendar_type+'&view='+calendar_view+'&mode='+calendar_mode+'&date='+calendar_start,
			method: 'GET',
			success: function(response) {
				$('.calendar_view').html(response);
				reload_resize_all();
				clear_all_data();
				reload_all_data();
				setLastActive();
				getLastActive();
			}
		});
	<?php } ?>
}

//RETRIEVE DATA AND LOAD ITEMS
function reload_equipment_assignment(equipmentid = '') {
	<?php if($_GET['type'] == 'schedule' && $is_customer) { ?>
		var view = $('#calendar_view').val();
		var date = $('#calendar_start').val();
		$.ajax({
			url: '../Calendar/schedule_sidebar.php?<?= http_build_query($_GET) ?>&date='+date+'&view='+view,
			success: function(response) {
				$('.collapsible').html(response);
				$('.sidebar.panel-group').css('padding-right','0');
				setTimeout(function() { toggle_columns() },500);
				reset_active.resolve();
			}
		});
	<?php } else if($_GET['type'] == 'schedule' && $_GET['mode'] != 'staff' && $_GET['mode'] != 'contractors' && $_GET['view'] != 'monthly' && $_GET['mode'] != 'summary') { ?>
		var equipmentids = [];
		if(equipmentid != '') {
			equipmentids.push(equipmentid);
		} else {
			$('[id^=collapse_equipment] .block-item').each(function() {
				if($(this).data('equipment') != undefined && $(this).data('equipment') > 0) {
					equipmentids.push($(this).data('equipment'));
				}
			});
		}
		var date = $('#calendar_start').val();
		var view = $('#calendar_view').val();
		var promises = [];
		equipmentids.forEach(function(equipmentid) {
			promises.push($.ajax({
				url: '../Calendar/calendar_ajax_all.php?fill=get_equipment_assignment_block',
				method: 'POST',
				data: { equipmentid: equipmentid, date: date, view: view },
				dataType: 'html',
				success: function(response) {
					$('[id^=collapse_equipment] .block-item[data-equipment='+equipmentid+']').closest('a').replaceWith(response);
				}
			}));
		});
		$.when.apply(null, promises).done(function(){
			initIconColors();
			toggle_columns();
			reset_active.resolve();
		});
	<?php } else { ?>
		reset_active.resolve();
	<?php } ?>
}
function reload_teams(teamid = '') {
	var date = $('#calendar_start').val();
	var view = $('#calendar_view').val();
	$.ajax({
		url: '../Calendar/teams_sidebar.php?<?= http_build_query($_GET) ?>&reload_sidebar=1&date='+date+'&teamid='+teamid+'&view='+view,
		method: 'GET',
		dataType: 'html',
		success: function(response) {
			$('#collapse_teams .panel-body').html(response);
			toggle_columns('', 1);
		}
	});
}
function calendarScrollLoad() {
	$('.calendar_view').scroll(function() {
		if($(this).scrollLeft() != lastScrollLeft) {
			if($(this).scrollLeft() + $(this).innerWidth() >= $(this)[0].scrollWidth && !end_of_list) {
				loadingOverlayShow('.calendar_view');
				var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).last();
				var ref_date = column.data('date');
				var ref_contact = column.data('contact');
				var ref_blocktype = column.data('blocktype');
				var columns_to_load = retrieve_columns_to_load(item_list, ref_date, ref_contact, 'next', 5, ref_blocktype);
				columns_to_load.forEach(function(col) {
					var col_arr = col.split('#*#');
					var item_row = $.grep(item_list[col_arr[0]], function(row) {
						return row.contactid == col_arr[1] && row.block_type == col_arr[2];
					});
					if(!item_row[0].loaded) {
						load_items(item_row[0], col_arr[0], col_arr[1], 'next', col_arr[2], item_row[0].region);
						item_row[0].loaded = true;
					}
					clear_excess_data('prev');
				});
				reload_resize_all();
				if(columns_to_load.length == 1 && columns_to_load[0] == ref_date+'#*#'+ref_contact+'#*#'+ref_blocktype) {
					end_of_list = true;
				}
				start_of_list = false;
				$(this).scrollLeft($(this).scrollLeft() - 30);
				loadingOverlayHide();
			} else if($(this).scrollLeft() == 0 && !start_of_list) {
				loadingOverlayShow('.calendar_view');
				var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).first();
				var ref_date = column.data('date');
				var ref_contact = column.data('contact');
				var ref_blocktype = column.data('blocktype');
				var columns_to_load = retrieve_columns_to_load(item_list, ref_date, ref_contact, 'prev', 5, ref_blocktype);
				columns_to_load.forEach(function(col) {
					var col_arr = col.split('#*#');
					var item_row = $.grep(item_list[col_arr[0]], function(row) {
						return row.contactid == col_arr[1] && row.block_type == col_arr[2];
					});
					if(!item_row[0].loaded) {
						load_items(item_row[0], col_arr[0], col_arr[1], 'prev', col_arr[2], item_row[0].region);
						item_row[0].loaded = true;
					}
					clear_excess_data('next');
				});
				reload_resize_all();
				if(columns_to_load.length == 1 && columns_to_load[0] == ref_date+'#*#'+ref_contact+'#*#'+ref_blocktype) {
					start_of_list = true;
				}
				end_of_list = false;
				$(this).scrollLeft(30);
				loadingOverlayHide();
			}
			lastScrollLeft = $(this).scrollLeft();
		}
	});
}
function reload_all_data() {
	var retrieve_collapse2_filter = '';
	var retrieve_collapse = $('#retrieve_collapse').val();
	var retrieve_collapse2 = $('#retrieve_collapse2').val();
	if(retrieve_collapse2 != undefined && retrieve_collapse2 != '') {
		retrieve_collapse2_filter = ',[id^='+retrieve_collapse2+']';
	}
	var calendar_type = $('#calendar_type').val();
	if(window.location.pathname == '/Calendar/calendars_mobile.php') {
		retrieve_items($('#mobile_active_contact').closest('a'), '', true);
	} else {
		if((calendar_type == 'ticket' || calendar_type == 'uni') && $('#collapse_teams .block-item.active').length > 0) {
			reload_teams();
		} else {
			$('[id^='+retrieve_collapse+']'+retrieve_collapse2_filter).find('.block-item.active').each(function() {
				retrieve_items($(this).closest('a'));
			});
		}
	}
}
function clear_excess_data(remove_type) {
	if($('.calendar_view table:not(#time_html) th').length >= 15) {
		var date = '';
		var contact = '';
		var block_type = '';
		if(remove_type == 'next') {
			var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).last();
			date = column.data('date');
			contact = column.data('contact');
			block_type = column.data('blocktype');
		} else if(remove_type == 'prev') {
			var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).first();
			date = column.data('date');
			contact = column.data('contact');
			block_type = column.data('blocktype');
		}
		$('.calendar_view th[data-date='+date+'][data-contact='+contact+'],.calendar_view td[data-date='+date+'][data-contact='+contact+'][data-blocktype='+block_type+']').remove();
		item_row = $.grep(item_list[date], function(row) {
			return (row.contactid == contact && row.block_type == block_type);
		});
		item_row[0].loaded = false;
	}
}
function clear_all_data() {
	$('.calendar_view th,.calendar_view td').filter(function() { return $(this).data('contact') > 0 }).remove();
	item_list = [];
	result_list = [];
	still_loading_item = false;
	still_loading = 0;
}
var still_loading = 0;
var item_list = [];
var still_loading_item = false;
var result_list = [];
function retrieve_items(anchor, calendar_date = '', force_show = false, retrieve_type = '', teamid = '') {
	if(still_loading_item) {
		var next_item = function() { retrieve_items(anchor, calendar_date, force_show, retrieve_type, teamid) };
		result_list.push(next_item);
	} else {
		still_loading_item = true;
		end_of_list = false;
		start_of_list = false;
		var block = $(anchor).find('.block-item');
		var type = $('#calendar_type').val();
		var config_type = $('#calendar_config_type').val();
		var block_type = $('#retrieve_block_type').val();
		if($(block).data('blocktype') != undefined && $(anchor).data('blocktype') != '') {
			block_type = $(block).data('blocktype');
		}
		var contact = $(block).data($('#retrieve_contact').val());
		if($(block).data('retrieve-contact') != undefined && $(block).data('retrieve-contact') != '') {
			contact = $(block).data($(block).data('retrieve-contact'));
		}
		var region = $(block).data('region-group');
		if(teamid != '' && teamid > 0) {
			contact = teamid;
			block_type = 'team';
		}
		var calendar_view = $('#calendar_view').val();

		var calendar_dates = JSON.parse($('#calendar_dates').val());
		if(calendar_date != '') {
			calendar_dates = [calendar_date];
		}

		var ref_date = '';
		var ref_contact = '';
		var ref_blocktype = '';
		if(scroll_to_today && calendar_dates.indexOf(today_date) > -1) {
			ref_date = today_date;
			ref_contact = contact;
			ref_blocktype = block_type;
		}
		loadingOverlayShow('.calendar_view');

		var promises = [];
		if($(block).hasClass('active') || force_show) {
			still_loading++;
			calendar_dates.forEach(function(calendar_date) {
				//If contact doesn't exist yet, initialize the contact
				if(item_list[calendar_date] == undefined) {
					item_list[calendar_date] = [];
				}
				//For each date of this contact, retrieve items
				var load_request = $.ajax({
					url: '../Calendar/load_calendar_item.php?<?= http_build_query($_GET) ?>&type='+type+'&block_type='+block_type+'&view='+calendar_view,
					method: 'POST',
					data: {
						contact_id: contact,
						calendar_date: calendar_date,
						config_type: config_type
					},
					success: function(response) {
						var item_data = JSON.parse(response);
						item_data['contactid'] = contact;
						item_data['block_type'] = block_type;
						item_data['region'] = region;
						item_data['loaded'] = false;
						var item_index = -1;
						$.each(item_list[calendar_date], function(i,v){
						    if(v.contactid == contact && v.block_type == block_type){
						        item_index = i;
						        return true;
						    }
						});
						var splice_index = -1;
						if(!(item_index) > -1) {
							$.each(item_list[calendar_date], function(i,v){
							    if(v.region == region){
							        splice_index = i;
							        return true;
							    }
							});
						}
						if(item_index > -1) {
							item_list[calendar_date][item_index] = item_data;
						} else if(splice_index > -1) {
							item_list[calendar_date].splice((splice_index+1),0,item_data);
						} else {
							item_list[calendar_date].push(item_data);
						}
						if(window.location.pathname == '/Calendar/calendars_mobile.php') {
							load_items(item_data, calendar_date, contact);
						}
					}
				});

				promises.push(load_request);
			});

			//When all ajax promises are done, display items and reload js and resize calendar
			$.when.apply(null, promises).done(function(){
				// loadingOverlayShow('.calendar_view');
				var cal_dates = JSON.parse($('#calendar_dates').val());
				var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).first();
				if(column.length < 1 && ref_date == '') {
					ref_date = cal_dates[0];
					ref_contact = contact;
					ref_blocktype = block_type;
				} else if(column.length >= 1) {
					ref_date = column.data('date');
					ref_contact = column.data('contact');
					ref_blocktype = column.data('blocktype');
				}
				var columns_to_load = retrieve_columns_to_load(item_list, ref_date, ref_contact, 'next', 10, ref_blocktype);
				var i = 0;
				var promises_items = calendar_dates.forEach(function(calendar_date) {
					var deferred = $.Deferred();
					var load_item = null;
					if(columns_to_load.indexOf(calendar_date+'#*#'+contact+'#*#'+block_type) >= 0) {
						item_row = $.grep(item_list[calendar_date], function(row) {
							return (row.contactid == contact && row.block_type == block_type);
						});
						if(!item_row[0].loaded) {
							load_item = load_items(item_row[0], calendar_date, contact, 'next', block_type, region);
							item_row[0].loaded = true;
							i++;
						}
						clear_excess_data('next');
					}
					$.when.apply(null, load_item).done(function() {
						return deferred.promise();
					});
				});
				$.when.apply(null, promises_items).done(function() {
					still_loading--;
					if(i > 0) {
						reload_resize_all();
					}
					still_loading_item = false;
					if(result_list.length > 0) {
						result_list.shift()();
					} else {
						reload_resize_all();
					}
				});
			});
		} else {
			destroy_items(contact, block_type);
			reload_resize_all();
			still_loading_item = false;
			if(result_list.length > 0) {
				result_list.shift()();
			} else {
				reload_resize_all();
			}
		}
	}
}
function retrieve_columns_to_load(item_list, date, contact, retrieve_type, limit = 10, block_type = '') {
	var calendar_dates = JSON.parse($('#calendar_dates').val());
	var include_list = [];
	var first_item_found = false;
	var i = 0;
	if(retrieve_type == 'next') {
		calendar_dates.forEach(function(calendar_date) {
			if(item_list[calendar_date] != undefined) {
				item_list[calendar_date].forEach(function(row) {
					if(calendar_date == date && contact == row.contactid && block_type == row.block_type) {
						i = 0;
						first_item_found = true;
					}
					if(first_item_found && i < limit) {
						include_list.push(calendar_date+'#*#'+row.contactid+'#*#'+row.block_type);
					}
					i++;
				});
			}
		});
	} else if(retrieve_type == 'prev') {
		calendar_dates.slice().reverse().forEach(function(calendar_date) {
			if(item_list[calendar_date] != undefined) {
				item_list[calendar_date].slice().reverse().forEach(function(row) {
					if(calendar_date == date && contact == row.contactid && block_type == row.block_type) {
						i = 0;
						first_item_found = true;
					}
					if(first_item_found && i < limit) {
						include_list.push(calendar_date+'#*#'+row.contactid+'#*#'+row.block_type);
					}
					i++;
				});
			}
		});
	}
	return include_list;
}
function load_items(item_row, date, contact, insert_type = 'next', block_type = '', region = '') {
	loadingOverlayShow('.calendar_view');
	var deferred = $.Deferred();
	//Does this column already exist?
	var contact_title = $('.calendar_view table:not(#time_html) th[data-contact='+contact+'][data-date='+date+'][data-blocktype='+block_type+']');

	var filter_query = '';
	if (contact_title.length > 0) {
		//If column already exists, replace html here
		filter_query += '[data-contact='+contact+'][data-date='+date+'][data-blocktype='+block_type+']';
		contact_title.replaceWith(item_row['title']);
		$('.calendar_view table:not(#time_html) tr[data-rowtype=shifts] td'+filter_query).replaceWith(item_row['shifts']);
		$('.calendar_view table:not(#time_html) tr[data-rowtype=notes] td'+filter_query).replaceWith(item_row['notes']);
		$('.calendar_view table:not(#time_html) tr[data-rowtype=reminders] td'+filter_query).replaceWith(item_row['reminders']);
		$('.calendar_view table:not(#time_html) tr[data-rowtype=warnings] td'+filter_query).replaceWith(item_row['warnings']);
		$('.calendar_view table:not(#time_html) tr[data-rowtype=ticket_summary] td'+filter_query).replaceWith(item_row['ticket_summary']);
		item_row['rows'].forEach(function(item) {
			var row_time = item.time;
			var row_html = item.html;
			$('.calendar_view table:not(#time_html) tr[data-rowtype='+row_time+'] td'+filter_query).replaceWith(row_html);
		});
	} else if(insert_type == 'prev') {
		//Is there a column that exists?
		var first_title = $('.calendar_view table:not(#time_html) th[data-date='+date+']').first();
		filter_query += '[data-date='+date+']';
		var first_region = $('.calendar_view table:not(#time_html) th[data-date='+date+'][data-region-group="'+region+'"]').first();
		if(first_region.length > 0) {
			first_title = first_region;
			filter_query += '[data-region-group="'+region+'"]';
		}

		if(first_title.length > 0) {
			//If column doesn't exist but there is a column, prepend it
			first_title.before(item_row['title']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=shifts] td'+filter_query).first().before(item_row['shifts']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=notes] td'+filter_query).first().before(item_row['notes']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=reminders] td'+filter_query).first().before(item_row['reminders']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=warnings] td'+filter_query).first().before(item_row['warnings']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=ticket_summary] td'+filter_query).first().before(item_row['ticket_summary']);
			item_row['rows'].forEach(function(item) {
				var row_time = item.time;
				var row_html = item.html;
				$('.calendar_view table:not(#time_html) tr[data-rowtype='+row_time+'] td'+filter_query).first().before(row_html);
			});
		} else {
			//If no columns exist, append to the beginning of the table
			$('.calendar_view table:not(#time_html) th').first().after(item_row['title']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=shifts] td').first().after(item_row['shifts']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=notes] td').first().after(item_row['notes']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=reminders] td').first().after(item_row['reminders']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=warnings] td').first().after(item_row['warnings']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=ticket_summary] td').first().after(item_row['ticket_summary']);
			item_row['rows'].forEach(function(item) {
				var row_time = item.time;
				var row_html = item.html;
				$('.calendar_view table:not(#time_html) tr[data-rowtype='+row_time+'] td').first().after(row_html);
			});
		}
	} else {
		//Is there a column that exists?
		var last_title = $('.calendar_view table:not(#time_html) th[data-date='+date+']').last();
		filter_query += '[data-date='+date+']';
		var last_region = $('.calendar_view table:not(#time_html) th[data-date='+date+'][data-region-group="'+region+'"]').last();
		if(last_region.length > 0) {
			last_title = last_region;
			filter_query += '[data-region-group="'+region+'"]';
		}
		if(last_title.length > 0) {
			last_title.after(item_row['title']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=shifts] td'+filter_query).last().after(item_row['shifts']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=notes] td'+filter_query).last().after(item_row['notes']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=reminders] td'+filter_query).last().after(item_row['reminders']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=warnings] td'+filter_query).last().after(item_row['warnings']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=ticket_summary] td'+filter_query).last().after(item_row['ticket_summary']);
			item_row['rows'].forEach(function(item) {
				var row_time = item.time;
				var row_html = item.html;
				$('.calendar_view table:not(#time_html) tr[data-rowtype='+row_time+'] td'+filter_query).last().after(row_html);
			});
		} else {
			//If no columns exist, append to the end of the table
			$('.calendar_view table:not(#time_html) th').last().after(item_row['title']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=shifts] td').last().after(item_row['shifts']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=notes] td').last().after(item_row['notes']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=reminders] td').last().after(item_row['reminders']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=warnings] td').last().after(item_row['warnings']);
			$('.calendar_view table:not(#time_html) tr[data-rowtype=ticket_summary] td').last().after(item_row['ticket_summary']);
			item_row['rows'].forEach(function(item) {
				var row_time = item.time;
				var row_html = item.html;
				$('.calendar_view table:not(#time_html) tr[data-rowtype='+row_time+'] td').last().after(row_html);
			});
		}
	}
    initTooltips();
    resize_rows();
	return deferred.promise();
}
function destroy_items(contact, block_type) {
	$('.calendar_view th[data-contact='+contact+'][data-blocktype='+block_type+'],.calendar_view td[data-contact='+contact+'][data-blocktype='+block_type+']').remove();
	var calendar_dates = JSON.parse($('#calendar_dates').val());
	calendar_dates.forEach(function(calendar_date) {
		if(item_list[calendar_date] != undefined) {
			item_list[calendar_date] = $.grep(item_list[calendar_date], function(row) {
				return (row.contactid != contact || row.block_type != block_type);
			});
		}
	});
	still_loading--;
}
function reload_resize_all() {
	if(reloadDragResize != undefined) {
		reloadDragResize();
	}
	clearClickableCells();
	resizeBlocks();
	expandBlock();
	reloadExpandDivs();

	<?php if($calendar_hide_left_time == 1) { ?>
		var num_columns = parseInt($('.table_bordered thead th:visible').length);
		if($('.calendar_view table:not(#time_html) th:visible').first().hasClass('today-active')) {
			$('.calendar_view table:not(#time_html) thead tr').css('margin-left', '-1px');
		} else {
			$('.calendar_view table:not(#time_html) thead tr').css('margin-left', '0px');
		}
	<?php } else { ?>
		var num_columns = parseInt($('.table_bordered thead th:visible').length) - 1;
	<?php } ?>

	clearTimeout(hide_overlay);
	var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).first();
	var ref_date = $(column).data('date');
	if(still_loading > 0 && $('.calendar_view table:not(#time_html) th[data-date='+ref_date+']').length < 10) {
		var hide_overlay = setInterval(function() {
			if(still_loading <= 0) {
				loadingOverlayHide();
				clearInterval(hide_overlay);
			}
		},50);
	} else {
		loadingOverlayHide();
	}

	// Specify the column width, if it's past min-width it will use that so this can just go down to 1%
	width = 100 / num_columns;
	$('.calendar_view table:not(#time_html) td, .calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).css('width',width+'%');
	$('.calendar-column-notes').each(function() { $(this).css('margin-bottom','calc(0.5em + '+this.offsetTop+'px)'); });
	$('.calendar_view tbody tr').first().find('td').css('padding-top',$('.calendar_view thead tr').outerHeight() + 8);
	$('#time_html tbody tr').first().find('td').css('padding-top',$('.calendar_view thead tr').outerHeight() + 8);
	$.when.apply(null, resize_calendar_view()).done(function() {
		if(scroll_to_today) {
			scrollToToday();
		}
	});
	scrollHeader();
	initTicketHoverStaff();
	setAutoRefresh();
	initIconColors();
	
    $('[name=multi_book]').click(function(e) {
    	e.stopImmediatePropagation();
    });
}
function scrollToToday() {
	clearInterval(clear_today);
	var clear_today = setInterval(function() {
		if(still_loading <= 0 && scroll_to_today) {
			scroll_to_today = false;
			clearInterval(clear_today);
			while($('.calendar_view table:not(#time_html) th').length <= 10 && $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).length > 0) {
				if(start_of_list) {
					break;
				}
				loadingOverlayShow('.calendar_view');
				var column = $('.calendar_view table:not(#time_html) th').filter(function() { return $(this).data('contact') > 0; }).first();
				var ref_date = column.data('date');
				var ref_contact = column.data('contact');
				var ref_blocktype = column.data('blocktype');
				var columns_to_load = retrieve_columns_to_load(item_list, ref_date, ref_contact, 'prev', 2, ref_blocktype);
				columns_to_load.forEach(function(col) {
					var col_arr = col.split('#*#');
					var item_row = $.grep(item_list[col_arr[0]], function(row) {
						return row.contactid == col_arr[1] && row.block_type == col_arr[2];
					});
					if(!item_row[0].loaded) {
						load_items(item_row[0], col_arr[0], col_arr[1], 'prev', col_arr[2], item_row[0].region);
						item_row[0].loaded = true;
					}
				});
				reload_resize_all();
				if(columns_to_load.length == 1 && columns_to_load[0] == ref_date+'#*#'+ref_contact+'#*#'+ref_blocktype) {
					start_of_list = true;
				}
			}
			if($('.calendar_view').scrollLeft() == 0) {
				$('.calendar_view').scrollLeft(10);
			}
		} else {
			scroll_to_today = false;
			clearInterval(clear_today);
		}
	}, 2000);
}

//RETRIEVE DATA AND LOAD ITEMS MONTH VIEW
function reload_all_data_month() {
	var retrieve_collapse2_filter = '';
	var retrieve_collapse = $('#retrieve_collapse').val();
	var retrieve_collapse2 = $('#retrieve_collapse2').val();
	if(retrieve_collapse2 != undefined && retrieve_collapse2 != '') {
		retrieve_collapse2_filter = ',[id^='+retrieve_collapse2+']';
	}
	var retrieve_summary = $('#retrieve_summary').val();
	if(retrieve_summary == 1) {
		retrieve_whole_month();
	} else {
		$('[id^='+retrieve_collapse+']'+retrieve_collapse2_filter).find('.block-item.active').each(function() {
			retrieve_items_month($(this).closest('a'));
		});
	}
	toggle_columns();
}
function clear_all_data_month() {
	$('.calendar_view .calendar_block').remove();
	still_loading_item_month = false;
	result_list_month = [];
	item_list = [];
}
function retrieve_whole_month() {
	var calendar_date = $('#calendar_start').val();
	var calendar_view = $('#calendar_view').val();
	var calendar_mode = $('#calendar_mode').val();
	var type = $('#calendar_type').val();

	loadingOverlayShow('.calendar_view');
	$.ajax({
		url: '../Calendar/monthly_display.php?<?= http_build_query($_GET) ?>&type='+type+'&view='+calendar_view+'&date='+calendar_date+'&retrieve_all=1'+'&mode='+calendar_mode,
		method: 'GET',
		success: function(response) {
			$('.calendar_view').html(response);
			reload_resize_all_month();
			toggle_columns();
		}
	});
}
var still_loading_item_month = false;
var result_list_month = [];
function retrieve_items_month(anchor, calendar_date = '', force_show = false, teamid = '') {
	if(still_loading_item_month) {
		var next_item = function() { retrieve_items_month(anchor, calendar_date, force_show, teamid) };
		result_list_month.push(next_item);
	} else {
		still_loading_item_month = true;
		loadingOverlayShow('.calendar_view');
		var block = $(anchor).find('.block-item');
		var type = $('#calendar_type').val();
		var config_type = $('#calendar_config_type').val();
		var block_type = $('#retrieve_block_type').val();
		if($(block).data('blocktype') != undefined && $(anchor).data('blocktype') != '') {
			block_type = $(block).data('blocktype');
		}
		var contact = $(block).data($('#retrieve_contact').val());
		if($(block).data('retrieve-contact') != undefined && $(block).data('retrieve-contact') != '') {
			contact = $(block).data($(block).data('retrieve-contact'));
		}
		var calendar_view = $('#calendar_view').val();
		var calendar_mode = $('#calendar_mode').val();
		if($(block).data('retrieve-mode') != undefined && $(block).data('retrieve-mode') != '') {
			calendar_mode = $(block).data('retrieve-mode');
		}
		if(teamid != '' && teamid > 0) {
			block_type = 'team';
			contact = teamid;
		}

		//If contact doesn't exist yet, initialize the contact
		if(item_list[block_type] == undefined) {
			item_list[block_type] = [];
		}
		if(item_list[block_type][contact] == undefined) {
			item_list[block_type][contact] = [];
		}

		var calendar_dates = JSON.parse($('#calendar_dates_month').val());
		if(calendar_date != '') {
			calendar_dates = [calendar_date];
		}

		var promises = [];
		if($(block).hasClass('active') || force_show) {
			calendar_dates.forEach(function(calendar_date) {
				//For each date of this contact, retrieve items
				var load_request = $.ajax({
					url: '../Calendar/monthly_display_load.php?<?= http_build_query($_GET) ?>&type='+type+'&block_type='+block_type+'&view='+calendar_view+'&mode='+calendar_mode,
					method: 'POST',
					data: {
						contact_id: contact,
						calendar_date: calendar_date,
						config_type: config_type
					},
					success: function(response) {
						console.log(response);
						loadingOverlayShow('.calendar_view');
						item_list[block_type][contact][calendar_date] = response;
					}
				});

				promises.push(load_request);
			});

			//When all ajax promises are done, display items and reload js and resize calendar
			$.when.apply(null, promises).done(function(){
				destroy_items_month(contact, block_type);
				calendar_dates.forEach(function(calendar_date) {
					load_items_month(item_list[block_type][contact][calendar_date], calendar_date, contact);
				});
				if(!force_show && $('#calendar_type').val() != 'schedule') {
					toggle_columns(1);
				}
				still_loading_item_month = false;
				if(result_list_month.length > 0) {
					result_list_month.shift()();
				} else {
					reload_resize_all_month();
				}
			});
		} else {
			if($(block).data('teamid') != undefined && $(block).data('teamid') > 0) {
				contact = $(block).data('teamid');
				block_type = 'team';
			}
			destroy_items_month(contact, block_type);
			still_loading_item_month = false;
			if(result_list_month.length > 0) {
				result_list_month.shift()();
			} else {
				reload_resize_all_month();
			}
		}
	}
}
function load_items_month(item_row, date, contact) {
	$('.calendar_view td[data-date='+date+']').append(item_row);
}
function destroy_items_month(contact, block_type = '') {
	$('.calendar_view .calendar_block[data-contact='+contact+'][data-blocktype='+block_type+']').remove();
}
function reload_resize_all_month() {
	loadingOverlayHide();
	resize_calendar_view_monthly();
	initTicketHoverStaff();
	setAutoRefresh();
	if($('#calendar_type').val() == 'shift') {
		toggle_columns(1);
	}
	initIconColors();
}
function reload_calendar_month(date) {
	return $.ajax({
		url: '../Calendar/monthly_display.php?<?= http_build_query($_GET) ?>&date='+date+'&view=monthly',
		success: function(response) {
			$('.calendar_table').replaceWith(response);
		}
	});
}
function getClientStaff(contactid, type) {
	var calendar_dates = $('#calendar_dates_month').val();
	var staff = [];
	return $.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_client_staff',
		data: { type: type, clientid: contactid, calendar_dates: calendar_dates },
		method: 'POST'
	});
}
function changeScheduledTime(btn) {
	var block = $(btn).closest('.used-block');
	var ticket_table = $(block).data('tickettable');
	var ticketid = $(block).data('ticket');
	var ticket_scheduleid = $(block).data('ticketscheduleid');
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_ticket_scheduled_time',
		method: 'POST',
		data: { ticket_table: ticket_table, ticketid: ticketid, ticket_scheduleid: ticket_scheduleid },
		success:function(response) {
			if(response != '') {
                destroyInputs();
				var arr = response.split('#*#');
				$('[name="change_to_do_date"]').val(arr[0]);
				$('[name="change_to_do_start_time"]').val(arr[1]);
				// $('[name="change_to_do_end_time"]').val(arr[2]);
				$('[name="change_ticket_table"]').val(ticket_table);
				if(ticket_table == 'ticket_schedule') {
					$('[name="change_ticket_id"]').val(ticket_scheduleid);
				} else {
					$('[name="change_ticket_id"]').val(ticketid);
				}
                initInputs();
				dialogScheduledTime();
			}
		}
	});
}
function dialogScheduledTime() {
    $( "#dialog-scheduled-time" ).dialog({
		resizable: false,
		height: "auto",
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
			"Submit": function() {
				var ticket_table = $('[name="change_ticket_table"]').val();
				var id = $('[name="change_ticket_id"]').val();
				var to_do_date = $('[name="change_to_do_date"]').val();
				var to_do_start_time = $('[name="change_to_do_start_time"]').val();
				// var to_do_end_time = $('[name="change_to_do_end_time"]').val();
				$.ajax({
					url: '../Calendar/calendar_ajax_all.php?fill=update_ticket_scheduled_time',
					method: 'POST',
					data: { ticket_table: ticket_table, id: id, to_do_date: to_do_date, to_do_start_time: to_do_start_time}, //, to_do_end_time: to_do_end_time },
					success:function(response) {
						reload_all_data();
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
function quickAddShift(a) {
	var retrieve_collapse = $('#retrieve_collapse').val();
	var date = $(a).data('date');
	var staff = $('[id^='+retrieve_collapse+']').find('.block-item.active').first().data('contact');
	if(!(staff > 0)) {
		staff = $('[id^='+retrieve_collapse+']').find('.block-item.active').first().data('staff');
	}
	var client = $('#collapse_booking').find('.block-item.active').first().data('contact');
	var block = $('#dialog-quick-add-shift');
	$(block).find('[name="quick_add_staff"]').val(staff).trigger('change.select2');
	$(block).find('[name="quick_add_client"]').val(client).trigger('change.select2');
	$(block).find('[name="quick_add_time"]').val('');
    $(block).dialog({
		resizable: false,
		height: "auto",
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		open: function(e, ui) {
			$(block).find('[name="quick_add_time"]').focus();
		    $("#dialog-quick-add-shift").unbind('keypress');
		    $("#dialog-quick-add-shift").keypress(function(e) {
				if (e.keyCode == $.ui.keyCode.ENTER) {
					$(this).parent().find("button:eq(0)").trigger("click");
				}
		    });
		},
		buttons: {
			"Submit": function() {
				var time = $(block).find('[name="quick_add_time"]').val();
				$.ajax({
					url: '../Calendar/calendar_ajax_all.php?fill=quick_add_shift',
					method: 'POST',
					data: { date: date, staff: staff, client: client, time: time },
					success: function(response) {
						var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-contact="'+staff+'"],.block-item[data-staff="'+staff+'"]').closest('a');
						retrieve_items_month(anchor);
					}
				});
			    $("#dialog-quick-add-shift").unbind('keypress');
	        	$(this).dialog('close');
			},
	        Cancel: function() {
			    $("#dialog-quick-add-shift").unbind('keypress');
	        	$(this).dialog('close');
	        }
		}
	});
}
function displayActiveBlocksAuto() {
	$('.active_blocks .block-item,.active_blocks').hide();
	$('.active_blocks').each(function() {
		var accordion = $(this).data('accordion');
		$(this).find('.block-item').each(function() {
			var active_value = $(this).data('activevalue');
			if($('#'+accordion+' .block-item[data-activevalue="'+active_value+'"]').hasClass('active')) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});

		if($('#'+accordion).hasClass('in')) {
			$(this).hide();
		} else {
			$(this).show();
		}
	});
}
function displayClientFrequency(clients) {
	if(clients.length > 0) {
		var staff_list = [];
		$('[id^=collapse_staff] .block-item').each(function() {
			if(staff_list.indexOf($(this).data('staff')) == -1) {
				staff_list.push($(this).data('staff'));
			}
		});
		$.ajax({
			url: '../Calendar/calendar_ajax_all.php?fill=get_ticket_client_frequency',
			method: 'POST',
			data: { clients: JSON.stringify(clients), staff: JSON.stringify(staff_list) },
			success: function(response) {
				var client_freqs = JSON.parse(response);
				client_freqs.forEach(function(client_freq) {
					$('[id^=collapse_staff] .block-item[data-staff='+client_freq.staffid+'] ul.client_freq').html(client_freq.html);
				});
			}
		});
		$('ul.client_freq').html('').show();
	} else {
		$('ul.client_freq').html('').hide();
	}
}
</script>