$(document).ready(function () {
	resize_calendar_view_monthly();
	$('.sidebar.panel-group').css('padding-right','0');
    if($(window).width() >= 768) {
		$('panel-heading').on('click', function() {
			resize_calendar_view_monthly();
		});
		$(window).on('resize', function() {
			resize_calendar_view_monthly();
		});
	} else {
		$('.collapsible').css('max-width','100%');
		$('.collapsible:before').css('display','none');
	}
	setTimeout(function() {
		resize_calendar_view_monthly();	
	}, 500);
});

// $(function() {
//     $( ".connectedSortable" ).sortable({
// 		connectWith: ".connectedSortable",
// 		update: function( event, td ) {
// 			var ticket_task = td.item.attr("id"); //Done
// 			var table_class = td.item.parent().attr("class");
// 			var status = table_class.split(' ');
// 			var what_type = ticket_task.split('_');
// 			if(what_type[0] == 'ticket') {
// 				$.ajax({    //create an ajax request to load_page.php
// 					type: "GET",
// 					url: "calendar_ajax_all.php?fill=ticket_calendar&ticketid="+what_type[1]+"&to_do_date="+status[2],
// 					dataType: "html",   //expect html to be returned
// 					success: function(response){
// 						location.reload();
// 						//return false;
// 					}
// 				});
// 			}
// 			if(what_type[0] == 'task') {
// 				$.ajax({    //create an ajax request to load_page.php
// 					type: "GET",
// 					url: "calendar_ajax_all.php?fill=task_calendar&tasklistid="+what_type[1]+"&task_tododate="+status[2],
// 					dataType: "html",   //expect html to be returned
// 					success: function(response){
// 						location.reload();
// 						//return false;
// 					}
// 				});
// 			}
// 		}
//     }).disableSelection();

//     $( ".connectedSortable" ).sortable({
//       cancel: ".ui-state-disabled"
//     });

// });
if($('[name="edit_access"]').val() == 1) {
	$(function() {
	    $( ".calendarSortable" ).sortable({
			connectWith: ".calendarSortable",
			items: 'a.sortable-blocks',
			helper: 'clone',
			start: function(e, td) {
				old_contact = td.item.closest('.calendar_block').data('contact');
			},
			beforeStop: function(e, td) {
				if($('.highlightCell').length > 0) {
					calendar_type = $('#calendar_type').val();
					item_type = td.item.data('itemtype');
					ticket_table = td.item.data('tickettable');
					ticket_scheduleid = td.item.data('ticketscheduleid');
					timestamp = td.item.data('timestamp');
					blocktype = td.item.data('blocktype');
					appt = td.item.data('appt');
					ticket = td.item.data('ticket');
					task = td.item.data('task');
					swo = td.item.data('swo');
					shift = td.item.data('shift');
					estimateaction = td.item.data('estimateaction');
					old_date = td.item.data('currentdate');
					old_staff = td.item.data('currentstaff');
					old_equipassign = td.item.data('equipassign');
					target = $('.highlightCell').removeClass('highlightCell');
					new_date = target.data('date');
					contact = target.data('contact');
					equipassign = target.data('equipassign');
					old_td_blocktype = td.item.data('blocktype');
					new_td_blocktype = target.data('blocktype');
					mode = $('#calendar_mode').val();

					data = { ticket_table: ticket_table, ticket_scheduleid: ticket_scheduleid, new_date: new_date, old_date: old_date, item_type: item_type, old_contact: old_contact, contact: contact, shift: shift, appt: appt, ticket: ticket, task: task, swo: swo, equipassign: equipassign, estimateaction: estimateaction, blocktype: blocktype, old_staff: old_staff, old_td_blocktype: old_td_blocktype, new_td_blocktype: new_td_blocktype, mode: mode };
					if(new_date != '' && new_date != undefined) {
						if(item_type == 'ticket_equip' || item_type == 'ticket') {
							var recently_updated = checkTicketLastUpdated(ticket_table, ticket, ticket_scheduleid, timestamp);
							recently_updated.success(function(response) {
								if(response == 1) {
									alert('This item was recently updated by someone. Your Calendar will be updated with the latest data.');
									$(td.item).remove();
									reload_all_data_month();
								} else {
									console.log(old_contact+'\n'+contact);
									if(old_contact != contact && contact != '' && contact != undefined && item_type == 'ticket' && calendar_type != 'schedule' && calendar_type != 'event') {
										$( "#dialog-staff-add" ).dialog({
											resizable: false,
											height: "auto",
											width: ($(window).width() <= 500 ? $(window).width() : 500),
											modal: true,
											buttons: {
										        "Add Staff": function() {
										        	data.add_staff = 1;
													ajaxMoveApptMonth(data, td.item);
										        	$(this).dialog('close');
										        },
										        "Replace Staff": function() {
										        	data.add_staff = 0;
													ajaxMoveApptMonth(data, td.item);
										        	$(this).dialog('close');
										        },
										        Cancel: function() {
										        	reload_all_data_month();
										        	$(this).dialog('close');
										        }
									        }
									    });
									} else {
										ajaxMoveApptMonth(data, td.item);
									}
								}
							});
						} else {
							ajaxMoveApptMonth(data, td.item);
						}
					} else {
						// window.location.reload();
						$(td.item).remove();
						reload_all_data_month();
					}
				} else {
					// window.location.reload();
					$(td.item).remove();
					reload_all_data_month();
				}
			},
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.calendarSortable').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			}
	    });
	});
}

function ajaxMoveApptMonth(data, block) {
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=move_appt_month&offline='+offline_mode,
		method: 'POST',
		data: data,
		success: function(response) {
			// window.location.reload();
				$(block).remove();
				reload_all_data_month();
		}
	});

}

function resize_calendar_view_monthly () {
	$('.calendar-screen.set-height').css('padding-bottom','0');
    $('body>.container .main-screen').css('padding-bottom','0');
    $('.main-screen .scale-to-fill').outerHeight('100vh');
    $('.main-screen .scale-to-fill').outerHeight($('.main-screen .scale-to-fill').outerHeight() - $('#footer')[0].clientHeight - $('.main-screen .scale-to-fill').offset().top);
    $('.collapsible').outerHeight('100vh');
    $('.collapsible').outerHeight($('.collapsible').outerHeight() - $('#footer')[0].clientHeight - $('.collapsible').offset().top);
    $('.collapsible').css('overflow','auto');
    if($('.scalable').length > 0) {
	    $('.scalable').outerHeight('100vh');
	    $('.scalable').outerHeight($('.scalable').outerHeight() - $('#footer')[0].clientHeight - $('.scalable').offset().top - 10);
	    $('.scalable').css('overflow','auto');
	    $('.scalable .block-group').not('.no-resize').outerHeight($('.calendar_view').outerHeight() - $('.scalable .block-group').prev('div').outerHeight());
    }
    
    if($('#calendar_type').val() != 'schedule' && $('#calendar_type').val() != 'ticket' && $('#calendar_type').val() != 'uni') {
		var sidebar_headings = 0;
		$('.sidebar.panel-group .panel:visible').each(function() {
			sidebar_headings += $(this).outerHeight() - $(this).find('.panel-body')[0].clientHeight;
		});
		$('.sidebar.panel-group .panel-body').outerHeight($('.sidebar.panel-group').outerHeight() - sidebar_headings);
	}

	var num_cols = $('.calendar_view table th').length;
	var width = 100 / num_cols;
	$('.calendar_view table td').css('width', width+'%');
	var width = $('.calendar_view table td').first().outerWidth();
	$('.calendar_view table td, .calendar_view table th').css('width',width);
	$('.calendar_view table tbody tr').first().find('td').css('padding-top',$('.calendar_view table thead tr th').outerHeight() + 8);
	
	var height = $('.calendar-screen .scale-to-fill').outerHeight();
	$('.calendar_view').height('calc('+$('.calendar-screen .scale-to-fill').outerHeight()+'px - 4em)');
	$('.collapsible .sidebar').height('calc('+($('.calendar_view').height() - $('.search-text').outerHeight())+'px - 1.5em - 1px)');
	// $('.calendar_view').outerHeight($('.calendar_view').outerHeight() - $('.ticket-status-legend').outerHeight(true));
}