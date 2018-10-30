if($('[name="edit_access"]').val() == 1) {
	// Resizable appointment blocks
	$(document).ready(function() {
		itemsDraggable();
		itemsHoverInit();
		// shiftsResizable();
		// itemsResizable();
		unbookedDraggable();
		dispatchDraggable();
		teamsDraggable();
		clientsDraggable();
		reloadDragResize();
		$('div.used-block').each(function() {
			blockFontResize($(this));
		});
	});
	function blockFontResize(block) {
		block.css('width','100%');
		block.find('a').css('font-size','1em');
		var height = block.height();
		block.height('auto');
		var font = block.height();
		block.height(height);
		if(font > height) {
			var width = block.width() - block.find('img').width();
			block.find('a').css('font-size',(height/font)+'em');
			block.find('a').css('width',width+'px');
		}
	}

	function itemsHoverInit() {
		if(window.location.pathname == '/Calendar/calendars_mobile.php') {
			if($('.calendar_view table:not(#time_html)').find('div.used-block:not(.sorting-initialize):visible').not('.no_change').length > 0) {
				$('.calendar_view table:not(#time_html)').find('div.used-block:not(.sorting-initialize):visible').not('.no_change').addClass('sorting-initialize');
			    $( ".calendar_view table:not(#time_html)" ).sortable('refresh');
			}
			if($('td:not(.resize-initialize):not(.ui-resizable),th div.resizer:not(.resize-initialize):not(.ui-resizable)').not('.no_change').length > 0) {
				$('td:not(.resize-initialize):not(.ui-resizable),th div.resizer:not(.resize-initialize):not(.ui-resizable)').not('.no_change').addClass('resize-initialize');
				itemsResizable();
			}
		} else {
			$('.calendar_view table:not(#time_html)').find('div.used-block:not(.sorting-initialize):visible').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('sorting-initialize');
			    $( ".calendar_view table:not(#time_html)" ).sortable('refresh');
			});
			$('.equip_assign_div').find('.equip_assign_draggable:not(.sorting-initialize)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('sorting-initialize');
				$('.equip_assign_div').sortable('refresh');
			});
			$('.team_assign_div').find('.team_assign_draggable:not(.sorting-initialize)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('sorting-initialize');
				$('.team_assign_div').sortable('refresh');
			});
			$('.client_assign_div').find('.client_assign_draggable:not(.sorting-initialize)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('sorting-initialize');
				$('.client_assign_div').sortable('refresh');
			});
			$('.unbooked, .bookable').find('.block-item:not(.sorting-initialize)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('sorting-initialize');
			    $( ".unbooked, .bookable" ).sortable('refresh');
			});
			$('td:not(.resize-initialize):not(.ui-resizable),th div.resizer:not(.resize-initialize):not(.ui-resizable)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('resize-initialize');
				itemsResizable();
			});
			$('.calendar_view table:not(#time_html) div.resizable-shift:not(.resize-initialize):not(.ui-resizable)').not('.no_change').off('mouseenter').on('mouseenter', function() {
				$(this).addClass('resize-initialize');
				shiftsResizable();
			});
		}
	}

	function reloadDragResize() {
		itemsHoverInit();
		// shiftsResizable();
		// itemsDraggable();
		// itemsResizable();
		// unbookedDraggable();
		// dispatchDraggable();

	 //    $( ".calendar_view table:not(#time_html)" ).sortable('refresh');
	 //    $( ".unbooked, .bookable" ).sortable('refresh');
		// $('.equip_assign_div').sortable('refresh');
	}

	function initDraggable() {
		// shiftsResizable();
		itemsDraggable();
		// itemsResizable();
		unbookedDraggable();
		dispatchDraggable();
		teamsDraggable();
		clientsDraggable();
	}

	// Resizable shift to allow time ranges
	function shiftsResizable() {
		$( ".calendar_view table:not(#time_html) div.resizable-shift.resize-initialize:not(.ui-resizable)" ).resizable({
			handles: 's',
			start: function(e, block) {
				$(this).css('min-height', '0px');
				$(this).find('span').unbind('mouseenter').unbind('mouseleave');
			},
			resize: function(e, block) {
				$(window).off('resize');
				$('.highlightCell').removeClass('highlightCell');
				var firstBlock = block.element.closest('td').data('time');
				var contact = block.element.closest('td').data('contact');
				var date = block.element.closest('td').data('date');
				var posX = block.element.offset().left;
				var posEnd = block.element.offset().top + block.element.height() - $(window).scrollTop();
				// for(var posY = block.element.offset().top - $(window).scrollTop(); posY <= posEnd; posY++) {
					var posY = posEnd;
					// $(document.elementsFromPoint(posX, posY)).filter('table:not(#time_html) td').not('.ui-sortable-helper').first().addClass('highlightCell');
					var lastBlock = $(document.elementsFromPoint(posX, posY)).filter('table:not(#time_html) td').not('.ui-sortable-helper').first().data('time');
				// }
				$('table:not(#time_html) td[data-contact='+contact+'][data-date='+date+']').filter(function() {
					return ($(this).data('time') >= firstBlock && $(this).data('time') <= lastBlock);
				}).addClass('highlightCell');
				$('.calendar_view table:not(#time_html)').off('mouseup');
				$('.calendar_view table:not(#time_html)').mouseup(function() {
					$('.calendar_view table:not(#time_html)').off('mouseup');
					blockFontResize(block.element);
					var target = $('.highlightCell').last();
					var start_datatime = block.element.data('row');
					var end_datatime = target.data('time');
					var start_time = $('.calendar_view table:not(#time_html) td[data-time='+start_datatime+']').first().text();
					var end_time = $('.calendar_view table:not(#time_html) td[data-time='+end_datatime+']').first().text();
					var shifttype = block.element.data('shifttype');
					if(shifttype == 'universal') {
						var appt_url = block.element.closest('a.shift').data('appturl');
						var ticket_url = block.element.closest('a.shift').data('ticketurl');
						appt_url += '&end_appoint_date='+block.element.closest('td').data('date')+' '+end_time;
						ticket_url += '&end_time='+end_time;
						block.element.closest('a.shift').data('appturl', appt_url);
						block.element.closest('a.shift').data('ticketurl', ticket_url);
						block.element.closest('a.shift').trigger('click');
					} else if(shifttype == 'ticket_equip') {
						block.element.closest('a.shift').attr('data-endtime', end_time);
						block.element.closest('a.shift').trigger('click');
					} else if(shifttype == 'ticket') {
						var ticket_url = block.element.closest('a.shift').data('ticketurl');
						ticket_url += '&end_time='+end_time;
						block.element.closest('a.shift').data('ticketurl', ticket_url);
						block.element.closest('a.shift').trigger('click');
					} else if(shifttype == 'appt') {
						var appt_url = block.element.closest('a.shift').data('appturl');
						appt_url += '&end_appoint_date='+block.element.closest('td').data('date')+' '+end_time;
						block.element.closest('a.shift').data('appturl', appt_url);
						block.element.closest('a.shift').trigger('click');
					} else if(shifttype == 'shift') {
						var shift_url = block.element.closest('a.shift').attr('href');
						shift_url += '&shift_endtime='+end_time;
						overlayIFrameSlider(shift_url);
					} else {
						reload_all_data();
					}
					$('.highlightCell').removeClass('highlightCell');
				});
			}
		});
		$('div.resizable-shift .ui-resizable-s').css('height', $('div.resizable-shift').outerHeight());
		$('div.resizable-shift .ui-resizable-s').css('cursor', 'pointer');
	}

	// Draggable table cells
	function itemsDraggable() {
		var initial_time = 0;
		var initial_date = '';
		var initial_contact = '';
	    $( ".calendar_view table:not(#time_html)" ).sortable({
			appendTo: ".calendar_view table:not(#time_html)",
			beforeStop: function(e, td) {
				$('.temp_td').remove();
				prev_td = null;
				td.helper.removeClass('popped-field');
				if($('.highlightCell').length > 0) {
					td_items = [];
					if($('[name=multi_book][data-date='+initial_date+'][data-contact='+initial_contact+']:checked').length > 0) {
						$('[name=multi_book][data-date='+initial_date+'][data-contact='+initial_contact+']:checked').each(function() {
							var used_block = $(this).closest('.used-block');
							if(used_block.hasClass('combined_blocks')) {
								used_block.find('.combined_block').each(function() {
									td_items.push(this);
								});
							} else {
								td_items.push(used_block);
							}
						});
					} else if(td.item.hasClass('combined_blocks')) {
						td.item.find('.combined_block').each(function() {
							td_items.push(this);
						});
					} else {
						td_items.push(td.item);
					}
					target = $('.highlightCell').removeClass('highlightCell');
					row_time = target.data('time');

					td_items.forEach(function(td_item) {
						td.item = $(td_item);
						new_row_time = td.item.data('row') - initial_time + row_time;
						new_time = target.data('date') + ' ' + $('.calendar_view tr[data-rowtype='+new_row_time+'] td').first().text();
						new_date = target.data('date');
						contact = target.data('contact');
						calendar_type = target.data('calendartype');
						equipassign = target.data('equipassign');
						td_blocktype = target.data('blocktype');
						old_date = td.item.closest('td').data('date');
						old_contact = td.item.closest('td').data('contact');
						timestamp = td.item.data('timestamp');
						// $('.calendar_view table').css('background-color','rgba(0,0,0,0.1)');
						id = 0;
						blocktype = td.item.data('blocktype');
						appt = td.item.data('appt');
						workorder = td.item.data('workorder');
						shift = td.item.data('shift');
						ticket = td.item.data('ticket');
						ticket_status = td.item.data('status');
						old_equipassign = td.item.data('equipassign');
						teamid = td.item.data('teamid');
						if (workorder != null && workorder != '') {
							item_type = 'workorder';
						} else if(ticket != null && ticket != '') {
							item_type = 'ticket';
							ticket_table = td.item.data('tickettable');
							ticket_scheduleid = td.item.data('ticketscheduleid');
							if(ticket_table == 'ticket_schedule' && ticket_scheduleid > 0) {
								item_type = 'ticket_schedule';
								id = ticket_scheduleid;
							}
						} else if(shift != null && shift != '') {
							old_date = td.item.data('currentdate');
							recurring = td.item.data('recurring');
							item_type = 'shift';
						} else {
							item_type = 'appt';
						}
						duration = td.item.data('duration');
						if(target.closest('tr').find('td').first().text() != 'Notes' && target.closest('tr').find('td').first().text() != 'Reminders' && target.closest('tr').find('td').first().text() != 'Warnings' && $('.highlightCell').last().data('draggable') != '0' && parseInt(contact) > 0) {
							data = { id: id, new_date: new_date, time_slot: new_time, duration: duration, appointment: appt, old_contact: old_contact, contact: contact, mode: page_mode, item: item_type, workorder: workorder, ticket: ticket, ticket_status: ticket_status, move_type: 'move', calendar_type: calendar_type, shift: shift, equipassign: equipassign, teamid: teamid, blocktype: blocktype, td_blocktype: td_blocktype };
							if(item_type == 'shift') {
								data.old_date = old_date;
								data.recurring = recurring;
							}
							if(item_type == 'ticket' || item_type == 'ticket_schedule') {
								var recently_updated = checkTicketLastUpdated(ticket_table, ticket, id, timestamp);
								recently_updated.success(function(response) {
									if(response == 1) {
										alert('This item was recently updated by someone. Your Calendar will be updated with the latest data.');
										reload_all_data();
									} else {
										if(old_contact != contact && item_type == 'ticket' && calendar_type != 'schedule' && calendar_type != 'event' && page_mode != 'client') {
											if(td_blocktype != 'team') {
												var check_staff_shifts = checkStaffShifts(contact, new_date);
												check_staff_shifts.success(function(response) {
													// console.log(response);
													var response = JSON.parse(response);
													if(response.success == true) {
													    $( "#dialog-staff-add" ).dialog({
															resizable: false,
															height: "auto",
															width: ($(window).width() <= 500 ? $(window).width() : 500),
															modal: true,
															buttons: {
														        "Add Staff": function() {
														        	data.add_staff = 1;
																	ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
														        	$(this).dialog('close');
														        },
														        "Replace Staff": function() {
														        	data.add_staff = 0;
																	ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
														        	$(this).dialog('close');
														        },
														        Cancel: function() {
														        	reload_all_data();
														        	$(this).dialog('close');
														        }
													        }
													    });
													} else {
														alert(response.message);
														reload_all_data();
													}
												});
											} else {
											    $( "#dialog-staff-add" ).dialog({
													resizable: false,
													height: "auto",
													width: ($(window).width() <= 500 ? $(window).width() : 500),
													modal: true,
													buttons: {
												        "Add Staff": function() {
												        	data.add_staff = 1;
												        	data.check_team_shifts = 1;
															ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
												        	$(this).dialog('close');
												        },
												        "Replace Staff": function() {
												        	data.add_staff = 0;
												        	data.check_team_shifts = 1;
															ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
												        	$(this).dialog('close');
												        },
												        Cancel: function() {
												        	reload_all_data();
												        	$(this).dialog('close');
												        }
											        }
											    });
											}
										} else {
											ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
										}
									}
								});
							} else {
								ajaxMoveAppt(data, old_contact, contact, old_date, new_date);
							}
							$('[name=multi_book]').prop('checked', false);
						} else {
							// window.location.reload();
							// reload_all_data();
						}
					});
				}
			},
			delay: 0,
			handle: ".drag-handle",
			helper: "clone",
			items: "div.used-block.sorting-initialize:visible",
			revert: 1,
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('td').not('.ui-sortable-helper').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			},
			start: function(e, td) {
				var data_time = td.item.closest('td').data('time');
				initial_date = td.item.closest('td').data('date');
				initial_contact = td.item.closest('td').data('contact');
				initial_time = data_time;
				$('table#time_html').find('td[data-time='+data_time+']').css('height',td.item.closest('td').css('height'));
				if(td.item.prop('rowspan') > 1) {
					prev_td = td.item.prev('td');
					prev_td = $(prev_td.closest('tr').nextAll('tr').first().find('td').get(prev_td.index()));
					prev_td.after('<td rowspan="'+(td.item.prop('rowspan') - 1)+'" class="temp_td"></td>');
				}
				td.helper.addClass('popped-field');
				if($('[name=multi_book][data-date='+initial_date+'][data-contact='+initial_contact+']:checked').length > 0) {
					block_html = '';
					td.helper.css('z-index', '999999');
					var top_px = 10;
					var zindex = 999998;
					$('[name=multi_book][data-date='+initial_date+'][data-contact='+initial_contact+']:checked').each(function() {
						used_block = $(this).closest('.used-block').clone();
						if($(used_block).data('ticketid') != td.item.data('ticketid') || $(used_block).data('ticketscheduleid') != td.item.data('ticketscheduleid')) {
							used_block.css('position', 'absolute');
							used_block.css('top', top_px+'px');
							used_block.css('left', top_px+'px');
							used_block.css('z-index', zindex);
							used_block.css('height', '200px');
							used_block.addClass('popped-field');
							block_html += used_block[0].outerHTML;
							top_px += 10;
							zindex--;
						}
					});
					td.helper.html(td.helper[0].outerHTML + block_html);
					td.helper.css('background-color', 'transparent');
					td.helper.css('overflow', 'hidden');
					td.helper.css('border', 'none');
					td.helper.css('height', '100%');
					td.helper.css('width', '100%');
				}
			},
			deactivate: function(e, td) {
				resize_calendar_view();
			}
	    });
	}

	var resizeTimeout;
	var resizeSave;
	function itemsResizable() {
		$('div.used-block:not(.ui-resizable,.no_change)').resizable({
			handles: 's',
			start: function(e, block) {
				$(this).css('min-height', '0px');
				$(this).find('span').unbind('mouseenter').unbind('mouseleave');
			},
			resize: function(e, block) {
				$(window).off('resize');
				$('.highlightCell').removeClass('highlightCell');
				var firstBlock = block.element.closest('td').data('time');
				var contact = block.element.closest('td').data('contact');
				var date = block.element.closest('td').data('date');
				var posX = block.element.offset().left;
				var posEnd = block.element.offset().top + block.element.height() - $(window).scrollTop();
				// for(var posY = block.element.offset().top - $(window).scrollTop(); posY <= posEnd; posY++) {
					var posY = posEnd;
					// $(document.elementsFromPoint(posX, posY)).filter('table:not(#time_html) td').not('.ui-sortable-helper').first().addClass('highlightCell');
					var lastBlock = $(document.elementsFromPoint(posX, posY)).filter('table:not(#time_html) td').not('.ui-sortable-helper').first().data('time');
				// }
				$('table:not(#time_html) td[data-contact='+contact+'][data-date='+date+']').filter(function() {
					return ($(this).data('time') >= firstBlock && $(this).data('time') <= lastBlock);
				}).addClass('highlightCell');
				$('.calendar_view table:not(#time_html)').off('mouseup');
				$('.calendar_view table:not(#time_html)').mouseup(function() {
					$('.calendar_view table:not(#time_html)').off('mouseup');
					blockFontResize(block.element);
					duration = $('.highlightCell').length * block.element.data('duration') / block.element.data('blocks');
					target = $('.highlightCell').first();
					new_time = target.data('date') + ' ' + target.closest('tr').find('td').first().text();
					new_date = target.data('date');
					contact = target.data('contact');
					calendar_type = target.data('calendartype');
					td_blocktype = target.data('blocktype');
					block_items = [];
					if(block.element.hasClass('combined_blocks')) {
						block.element.find('.combined_block').each(function() {
							block_items.push(this);
						});
					} else {
						block_items.push(block.element);
					}

					block_items.forEach(function(block_item) {
						block.element = $(block_item);
						id = 0;
						timestamp = block.element.data('timestamp');
						blocktype = block.element.data('blocktype');
						workorder = block.element.data('workorder');
						appt = block.element.data('appt');
						shift = block.element.data('shift');
						ticket = block.element.data('ticket');
						ticket_status = block.element.data('status');
						equipassign = block.element.data('equipassign');
						teamid = block.element.data('teamid');
						if (workorder != null && workorder != '') {
							item_type = 'workorder';
						} else if (ticket != null && ticket != '') {
							item_type = 'ticket';
							ticket_table = block.element.data('tickettable');
							ticket_scheduleid = block.element.data('ticketscheduleid');
							if(ticket_table == 'ticket_schedule' && ticket_scheduleid > 0) {
								item_type = 'ticket_schedule';
								id = ticket_scheduleid;
							}
						} else if (shift != null && shift != '') {
							recurring = block.element.data('recurring');
							item_type = 'shift';
						} else {
							item_type = 'appt';
						}

						data = { id: id, new_date: new_date, time_slot: new_time, duration: duration, appointment: appt, contact: contact, old_contact: contact, mode: page_mode, item: item_type, workorder: workorder, ticket: ticket, ticket_status: ticket_status, move_type: 'resize', calendar_type: calendar_type, shift: shift, equipassign: equipassign, teamid: teamid, blocktype: blocktype, td_blocktype: td_blocktype };
						if(item_type == 'shift' && recurring == 'yes') {
						    $( "#dialog-confirm" ).dialog({
								resizable: false,
								height: "auto",
								width: ($(window).width() <= 500 ? $(window).width() : 500),
								modal: true,
								buttons: {
							        "Only this shift": function() {
							        	data.edit_type = 'once';
							        	ajaxMoveAppt(data, contact, contact, new_date, new_date);
							        	$(this).dialog('close');
							        },
							        "Following shifts": function() {
							        	data.edit_type = 'following';
							        	ajaxMoveAppt(data, contact, contact, new_date, new_date);
							        	$(this).dialog('close');
							        },
							        "All shifts": function() {
							        	data.edit_type = 'all';
							        	ajaxMoveAppt(data, contact, contact, new_date, new_date);
							        	$(this).dialog('close');
							        },
							        Cancel: function() {
							        	reload_all_data();
							        	$(this).dialog('close');
							        }
						      }
						    });
						} else if(target.closest('tr').find('td').first().text() != 'Notes' && target.closest('tr').find('td').first().text() != 'Reminders' && target.closest('tr').find('td').first().text() != 'Warnings' && $('.highlightCell').last().data('draggable') != '0' && parseInt(contact) > 0) {
							if(item_type == 'ticket' || item_type == 'ticket_schedule') {
								var recently_updated = checkTicketLastUpdated(ticket_table, ticket, id, timestamp);
								recently_updated.success(function(response) {
									if(response == 1) {
										alert('This item was recently updated by someone. Your Calendar will be updated with the latest data.');
										reload_all_data();
									} else {
							        	ajaxMoveAppt(data, contact, contact, new_date, new_date);
									}
								});
							} else {
					        	ajaxMoveAppt(data, contact, contact, new_date, new_date);
							}
							$('.highlightCell').removeClass('highlightCell');
						} else {
							// window.location.reload();
							reload_all_data();
						}
					});
				});
			}
		});
		$('td.resize-initialize:not(.ui-resizable),th div.resizer.resize-initialize:not(.ui-resizable)').resizable({
			handles: 'e',
			resize: function(e, block) {
				var posX = block.element.offset().left;
				var mouseX = e.clientX;
				var width = mouseX - posX;
				var col = $(this).closest('tr').find('td,th div.resizer').index($(this));
				if(block.element.is('div')) {
					block.element.width('100%').height('auto');
				}
				if(width > 0 && col > 0) {
					var table = $(this).closest('table');
					table.find('tr').each(function() {
						$($(this).find('td,th').get(col)).css('min-width',width+'px').css('max-width',width+'px');
					});
				}
			},
			stop: function(e, block) {
				if(block.element.is('div')) {
					block.element.width('100%').height('auto');
				}
			}
		});
	}

	// Draggable onto table
	function unbookedDraggable() {
	    $( ".unbooked, .bookable" ).sortable({
			appendTo: ".main-screen",
			start: function(e, ui) {
				if($('[name=book_this]:checked').length > 0) {
					block_html = '';
					$('[name=book_this]:checked').each(function() {
						if(!$(this).closest('.block-item').hasClass('ui-sortable-helper') && $(this).closest('.block-item').data('table')+$(this).closest('.block-item').data('id') != ui.item.data('table')+ui.item.data('id')) {
							block_html += $(this).closest('.block-item')[0].outerHTML;
						}
					});
					ui.helper.append(block_html);
				}
			},
			beforeStop: function(e, block) {
				var blocks = [];
				if($('[name=book_this]:checked').length > 0) {
					$('[name=book_this]:checked').each(function() {
						// blocks.push($(this).next('a').find('.block-item'));
						if(!$(this).closest('.block-item').hasClass('ui-sortable-helper')) {
							blocks.push($(this).closest('.block-item'));
						}
					});
				} else {
					blocks.push(block.item);
				}
				if($('.highlightCell').length > 0) {
					var td = $('.highlightCell');
					var calendar_type = td.data('calendartype');
					var td_blocktype = td.data('blocktype');
					var item_type = blocks[0].data('type');
					var new_contact = td.data('contact');
					var new_date = td.data('date');

					if(item_type == 'ticket' && calendar_type != 'schedule' && calendar_type != 'event') {
						if(td_blocktype != 'team') {
							var check_staff_shifts = checkStaffShifts(new_contact, new_date);
							check_staff_shifts.success(function(response) {
								var response = JSON.parse(response);
								if(response.success == true) {
									$( "#dialog-staff-add" ).dialog({
										resizable: false,
										height: "auto",
										width: ($(window).width() <= 500 ? $(window).width() : 500),
										modal: true,
										buttons: {
									        "Add Staff": function() {
									        	ajaxUnbooked(blocks, block, 1, td_blocktype);
									        	$(this).dialog('close');
									        },
									        "Replace Staff": function() {
									        	ajaxUnbooked(blocks, block, 0, td_blocktype);
									        	$(this).dialog('close');
									        },
									        Cancel: function() {
									        	reload_all_data();
									        	$(this).dialog('close');
									        }
								        }
								    });
								} else {
									alert(response.message);
									reload_all_data();
								}
							});
						} else {
							$( "#dialog-staff-add" ).dialog({
								resizable: false,
								height: "auto",
								width: ($(window).width() <= 500 ? $(window).width() : 500),
								modal: true,
								buttons: {
							        "Add Staff": function() {
							        	ajaxUnbooked(blocks, block, 1, td_blocktype, 1);
							        	$(this).dialog('close');
							        },
							        "Replace Staff": function() {
							        	ajaxUnbooked(blocks, block, 0, td_blocktype, 1);
							        	$(this).dialog('close');
							        },
							        Cancel: function() {
							        	reload_all_data();
							        	$(this).dialog('close');
							        }
						        }
						    });
						}
					} else {
						ajaxUnbooked(blocks, block);
					}
				}
			},
			delay: 0,
			handle: ".drag-handle",
			helper: "clone",
			items: ".block-item.sorting-initialize",
			revert: 1,
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('td').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			}
	    });
	}

	function ajaxUnbooked(blocks, block, add_staff = 0, td_blocktype = '', check_team_shifts = 0) {
		var i = 0;
		var td = $('.highlightCell').removeClass('highlightCell');
		var new_contact = td.data('contact');
		var new_date = td.data('date');
		var unbooked_promises = [];
		var recently_updated_items = [];
		blocks.forEach(function(item) {
			calendar_type = td.data('calendartype');
			var time = td.closest('tr');
			for(var j = 0; j < i; j++) {
				time = time.next('tr');
			}
			i++;
			new_time = td.data('date') + ' ' + time.find('td').first().text();
			new_date = td.data('date');
			var header = td.closest('table').find('tr').first().find('th').eq(1);
			var item_label = item.text().trim().split('\n')[0];
			if(item.data('preferred-staff') != undefined && item.data('preferred-staff').length > 0) {
				var non_pref = true;
				header.find('.equip_assign_staff').each(function() {
					if(item.data('preferred-staff').indexOf($(this).data('contact')) != -1) {
						non_pref = false;
					}
				});
				if(non_pref) {
					alert('You have booked '+item_label+' to a non-preferred staff!');
				}
			}
			var booked_time = new Date(new_time).getTime();
			if(item.data('min-time') != '') {
				if(booked_time < new Date(new_time.substring(0,11) + item.data('min-time')).getTime()) {
					alert('You have booked '+item_label+' before of the listed availability!');
				}
			}
			if(item.data('max-time') != '') {
				if(booked_time > new Date(new_time.substring(0,11) + item.data('max-time')).getTime()) {
					alert('You have booked '+item_label+' after of the listed availability!');
				}
			}
			if(td.data('region') != '' && td.data('region') != undefined && td.data('region') != block.item.data('region') && block.item.data('region') != '' && block.item.data('region') != undefined && calendar_type == 'schedule') {
				if(!confirm(item_label + ' has a different region from this ' + $('[name="equipment_category_label"]').val() + '.  The region will get updated to the new region. Are you sure you want to schedule ' + item_label+ ' here?')) {
					return;
				}
			}

			if(td.closest('tr').find('td').first().text() != 'Notes' && td.closest('tr').find('td').first().text() != 'Reminders' && td.closest('tr').find('td').first().text() != 'Warnings' && td.data('draggable') != '0' && parseInt(td.data('contact')) > 0) {
				if(item.data('type') == 'ticket') {
					var recently_updated = checkTicketLastUpdated(item.data('table'), item.data('id'), item.data('id'), item.data('timestamp'));
					recently_updated.success(function(response) {
						if(response == 1) {
							recently_updated_items.push(item_label);
						} else {
							var unbooked_request = $.ajax({
								url: '../Calendar/calendar_ajax_all.php?fill=schedule_unbooked&offline='+offline_mode,
								method: 'POST',
								data: { time_slot: new_time, item: item.data('type'), id: item.data('id'), contact: td.data('contact'), duration: td.data('duration'), mode: page_mode, calendar_type: calendar_type, equipassign: td.data('equipassign'), blocktype: item.data('blocktype'), blocktable: item.data('table'), add_staff: add_staff, td_blocktype: td_blocktype },
								success: function(response) {
									// var block_a = $(item).closest('a')
									// $(block_a).prev('label').remove();
									// $(block_a).remove();
									if(response != '') {
										$(item).closest('.block-item').data('timestamp', response);
									} else {
										$(item).closest('.block-item').remove();
									}
									if($('#collapse_teams .block-item.active').length > 0) {
										reload_teams();
									}
								}
							});
							unbooked_promises.push(unbooked_request);
						}
					});
				} else {
					var unbooked_request = $.ajax({
						url: '../Calendar/calendar_ajax_all.php?fill=schedule_unbooked&offline='+offline_mode,
						method: 'POST',
						data: { time_slot: new_time, item: item.data('type'), id: item.data('id'), contact: td.data('contact'), duration: td.data('duration'), mode: page_mode, calendar_type: calendar_type, equipassign: td.data('equipassign'), blocktype: item.data('blocktype'), blocktable: item.data('table'), add_staff: add_staff, td_blocktype: td_blocktype },
						success: function(response) {
							// var block_a = $(item).closest('a')
							// $(block_a).prev('label').remove();
							// $(block_a).remove();
							$(item).closest('.block-item').remove();
						}
					});
					unbooked_promises.push(unbooked_request);
				}
			}
		});

		$.when.apply(null, unbooked_promises).done(function(){
			if(recently_updated_items.length > 0) {
				block.helper.remove()
				var alert_html = 'The following items were not updated because they were recently updated by someone else:\n';
				recently_updated_items.forEach(function(item_label) {
					alert_html += item_label+'\n';
				});
				alert_html += 'Your Calendar and Unbooked List will now be reloaded to the latest data.';
				alert(alert_html);
				retrieveUnbookedData();
			}
			var retrieve_collapse = $('#retrieve_collapse').val();
			var retrieve_contact = $('#retrieve_contact').val();
			// if(new_contact > 0) {
			// 	var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-'+retrieve_contact+'='+new_contact+']').closest('a');
			// 	retrieve_items(anchor, new_date);
			// } else {
			    reload_all_data();
			// }

			if(check_team_shifts == 1) {
				checkStaffShifts(new_contact, new_date, 'team').success(function(response) {
					var response = JSON.parse(response);
					if(response.success == false) {
						alert(response.message);
					}
				});
			}
		});
		// setTimeout(function() { reload_all_data(); }, 250);
	}

	function ajaxMoveAppt(data, old_contact, new_contact, old_date, new_date) {
		var retrieve_collapse = $('#retrieve_collapse').val();
		var retrieve_contact = $('#retrieve_contact').val();
		$.ajax({
			url: '../Calendar/calendar_ajax_all.php?fill=move_appt&offline='+offline_mode,
			method: 'POST',
			data: data,
			success: function(response) {
				if(window.location.pathname == '/Calendar/calendars_mobile.php') {
					reload_all_data();
				} else {
					if($('#collapse_teams .block-item.active').length > 0) {
						reload_teams();
					} else {
						if(response != '') {
							all_contacts = JSON.parse(response);
							$.each(all_contacts, function() {
								var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-'+retrieve_contact+'='+this+']').closest('a');
								retrieve_items(anchor);
							});
						} else {
							// window.location.reload();
							if(!(old_contact > 0) && !(new_contact > 0)) {
								reload_all_data();
							} else if(data.move_type == 'resize') {
								$('[id^='+retrieve_collapse+']').find('.block-item.active').each(function() {
									retrieve_items($(this).closest('a'), old_date);
								});
							} else {
								if(old_contact > 0) {
									var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-'+retrieve_contact+'='+old_contact+']').closest('a');
									if(old_date != new_date) {
										retrieve_items(anchor);
									} else {
										retrieve_items(anchor, old_date);
									}
								}
								if(new_contact > 0) {
									var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-'+retrieve_contact+'='+new_contact+']').closest('a');
									if(old_date != new_date) {
										retrieve_items(anchor);
									} else {
										retrieve_items(anchor, new_date);
									}
								}
							}
						}
						var retrieve_collapse2 = $('#retrieve_collapse2').val();
						var retrieve_contact2 = $('#retrieve_contact2').val();
						if(retrieve_collapse2 != undefined && retrieve_collapse2 != '') {
							$('[id^='+retrieve_collapse2+']').find('.block-item.active').each(function() {
								var anchor = $(this).closest('a');
								if(old_date != new_date) {
									retrieve_items(anchor);
								} else {
									retrieve_items(anchor, new_date);
								}
							});
						}
						if(data.check_team_shifts != undefined && data.check_team_shifts == 1) {
							checkStaffShifts(new_contact, data.new_date, 'team').success(function(response) {
								var response = JSON.parse(response);
								if(response.success == false) {
									alert(response.message);
								}
							});
						}
					}
				}
			}
		});
	}

	function checkStaffShifts(staffid, to_do_date, blocktype) {
		var check_shifts = $('#calendar_check_shifts').val();
		var check_days_off = $('#calendar_check_days_off').val();
		return $.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=check_staff_shifts',
			method: 'POST',
			data: { staffid: staffid, to_do_date: to_do_date, from_calendar: '1', check_shifts: check_shifts, check_days_off: check_days_off, blocktype: blocktype }
		});
	}

	// Dispatch Calendar draggables
	function dispatchDraggable() {
		var clone, before, parent, current_block;
		$('.equip_assign_div').sortable({
			connectWith: ".equip_assign_block",
			items: '.equip_assign_draggable.sorting-initialize',
			handle: '.drag-handle',
			helper: 'clone',
			start: function (e, block) {
				$(block.item).show();
				clone = $(block.item).clone();
				before = $(block.item).prev();
		        parent = $(block.item).parent();
		        current_block = $(block.item);
			},
			stop: function(e, td) {
				if($('.highlightCell').length > 0) {
					if(before.length) {
						before.after(clone);
					} else {
						parent.prepend(clone);
					}
					$(current_block).remove();
					var blocktype = td.item.data('blocktype');
					var clientid = td.item.data('client');
					var staffid = td.item.data('staff');
					var teamid = td.item.data('teamid');
					var new_equipmentid = td.item.data('equipment');
					var contractor = td.item.data('contractor');
					var restrict_assign = td.item.data('restrict-assign') > 0;
					var target = $('.highlightCell').removeClass('highlightCell');
					var equipmentid = target.data('equip');
					var equipment_assignid = target.data('equip-assign');
					var date = target.data('date');

					var new_contact = target.data('equip');
					var new_date = target.data('date');

					data = { blocktype: blocktype, clientid: clientid, staffid: staffid, teamid: teamid, new_equipmentid: new_equipmentid, equipmentid: equipmentid, equipment_assignid: equipment_assignid, date: date, contractor: contractor }
					if(!restrict_assign || !(new_equipmentid > 0) || new_equipmentid == equipmentid || confirm('Setting this assignment will replace a previous assignment. Are you sure you want to proceed?')) {
					// if(confirm('Changing the details for this Assignment will update all Work Orders for this day to the new details. Press OK to continue.')) {
						$.ajax({
							url: '../Calendar/calendar_ajax_all.php?fill=equip_assign_draggable&offline='+offline_mode,
							method: 'POST',
							data: data,
							success: function(response) {
								// window.location.reload();
								var retrieve_collapse = $('#retrieve_collapse').val();
								var retrieve_contact = $('#retrieve_contact').val();
								// if(new_contact > 0) {
								// 	var anchor = $('[id^='+retrieve_collapse+']').find('.block-item[data-'+retrieve_contact+'='+new_contact+']').closest('a');
								// 	retrieve_items(anchor, new_date);
								// } else {
									reload_equipment_assignment(equipmentid);
									if(new_equipmentid != equipmentid) {
										reload_equipment_assignment(new_equipmentid);
										if(restrict_assign) {
											reload_equipment_assignment(equipmentid);
										}
									}
								    reload_all_data();
								// }
							}
						});
						td.item.data('equipment',equipmentid);
					} else {
						reload_equipment_assignment(new_equipmentid);
					}
					// } else {
					// 	window.location.reload();
					// }
				} else {
					$(this).sortable('cancel');
				}
			},
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.equip_assign_block').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			}
		});
	}

	// Teams draggables
	function teamsDraggable() {
		var clone, before, parent, current_block;
		$('.team_assign_div').sortable({
			connectWith: ".team_assign_block,.equip_assign_block",
			items: '.team_assign_draggable.sorting-initialize',
			handle: '.drag-handle',
			helper: 'clone',
			start: function (e, block) {
				$(block.item).show();
				clone = $(block.item).clone();
				before = $(block.item).prev();
		        parent = $(block.item).parent();
		        current_block = $(block.item);
			},
			stop: function(e, td) {
				if($('.highlightCell').length > 0) {
					if(before.length) {
						before.after(clone);	
					} else {
						parent.prepend(clone);
					}
					$(current_block).remove();
					var staffid = td.item.data('staff');
					var target = $('.highlightCell').removeClass('highlightCell');
					if(target.hasClass('equip_assign_block')) {
						var blocktype = 'staff';
						var staffid = td.item.data('staff');
						var equipmentid = target.data('equip');
						var equipment_assignid = target.data('equip-assign');
						var date = target.data('date');

						data = { blocktype: blocktype, staffid: staffid, equipmentid: equipmentid, equipment_assignid: equipment_assignid, date: date };
											
						$.ajax({
							url: '../Calendar/calendar_ajax_all.php?fill=equip_assign_draggable&offline='+offline_mode,
							method: 'POST',
							data: data,
							success: function(response) {
								 
							    reload_all_data();
							}
						});
					} else {
						var teamid = target.data('team');
						var date = target.data('date');

						data = { staffid: staffid, teamid: teamid, date: date };
						if(teamid != undefined && teamid > 0) {
							$.ajax({
								url: '../Calendar/calendar_ajax_all.php?fill=team_assign_draggable&offline='+offline_mode,
								method: 'POST',
								data: data,
								success: function(response) {
									// console.log(response);
									reload_teams(response);
								}
							});
						}
					}
				} else {
					$(this).sortable('cancel');
				}
			},
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.team_assign_block,.equip_assign_block').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			}
		});
	}

	// Clients draggables
	function clientsDraggable() {
		var clone, before, parent, current_block;
		$('.client_assign_div').sortable({
			connectWith: ".calendar_view table:not(#time_html) td[data-duration]:not([data-contact=0])",
			items: '.client_assign_draggable.sorting-initialize',
			handle: '.drag-handle',
			helper: 'clone',
			start: function (e, block) {
				$(block.item).show();
				clone = $(block.item).clone();
				before = $(block.item).prev();
		        parent = $(block.item).parent();
		        current_block = $(block.item);
			},
			stop: function(e, td) {
				if($('.highlightCell').length > 0) {
					if(before.length) {
						before.after(clone);	
					} else {
						parent.prepend(clone);
					}
					$(current_block).remove();
					var clientid = td.item.data('client');
					var target = $('.highlightCell').removeClass('highlightCell');
					var contact = target.data('contact');
					var blocktype = target.data('blocktype');
					var start_time = target.closest('tr').find('td').first().text();
					var start_date = target.data('date');

					var tickets_have_recurrence = $('#tickets_have_recurrence').val();
					var data = { clientid: clientid, contact: contact, blocktype: blocktype, start_time: start_time, start_date: start_date };
					if(tickets_have_recurrence == 1) {
						$('#dialog_create_recurrence_cal').dialog({
							resizable: true,
							height: "auto",
							width: ($(window).width() <= 800 ? $(window).width() : 800),
							modal: true,
							open: function() {
								destroyInputs('#dialog_create_recurrence_cal');
								$('[name="recurrence_start_date"]').val(start_date);
								$('[name="recurrence_end_date"]').val('');
								$('[name="recurrence_repeat_interval"]').val(1);
								$('[name="recurrence_repeat_type"]').val('week').change();
								$('[name="recurrence_repeat_monthly_type"]').val('day').change();
								$('[name="recurrence_repeat_days[]"]').prop('checked', false);
								initInputs('#dialog_create_recurrence_cal');
							},
							buttons: {
								"One Time": {
									class: "left-dialog-button",
									text: "One Time",
									click: function() {
										var create_ticket = bookClientTicket(data);
										create_ticket.success(function (response) {
											reload_all_data();
											$('#dialog_create_recurrence_cal').dialog('close');
										});
									}
								},
								"Create Recurrence": function() {
									var recurrence_start_date = $('[name="recurrence_start_date"]').val();
									var recurrence_end_date = $('[name="recurrence_end_date"]').val();
									var recurrence_repeat_type = $('[name="recurrence_repeat_type"]').val();
									var recurrence_repeat_monthly = $('[name="recurrence_repeat_monthly_type"]').val();
									var recurrence_repeat_interval = $('[name="recurrence_repeat_interval"]').val();
									var recurrence_repeat_days = [];
									$('[name="recurrence_repeat_days[]"]:checked').each(function() {
										recurrence_repeat_days.push(this.value);
									});
									var recurrence_data = { start_date: recurrence_start_date, end_date: recurrence_end_date, repeat_type: recurrence_repeat_type, repeat_monthly: recurrence_repeat_monthly, repeat_interval: recurrence_repeat_interval, repeat_days: recurrence_repeat_days };
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
													data.start_date = response.first_date;
													data.is_recurring = 1;
													var create_ticket = bookClientTicket(data);
													create_ticket.success(function (response) {
														recurrence_data.ticketid = response;
														recurrence_data.skip_first = 1;
														$.ajax({
															url: '../Ticket/ticket_ajax_all.php?action=create_recurrence_tickets',
															method: 'POST',
															data: recurrence_data,
															success: function(response) {
																reload_all_data();
																$('#dialog_create_recurrence_cal').dialog('close');
															}
														});
													});
												}
											}
										}
									});
								},
								Cancel: function() {
									reload_all_data();
									$(this).dialog('close');
								}
							}
						});
					} else {
						var create_ticket = bookClientTicket(data);
						create_ticket.success(function (response) {
							reload_all_data();
						});
					}
				} else {
					$('.highlightCell').removeClass('highlightCell');
					$(this).sortable('cancel');
				}
			},
			sort: function(e, block) {
				td = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('td[data-duration]:not([data-contact=0])').first();
				$('.highlightCell').removeClass('highlightCell');
				td.addClass('highlightCell');
			}
		});
	}
	function bookClientTicket(data) {
		return $.ajax({
			url: '../Calendar/calendar_ajax_all.php?fill=book_client_ticket',
			data: data,
			method: 'POST'
		});
	}
}

// Javascript Common Display Functions
function resize_calendar_view() {
	if(window.location.pathname == '/Calendar/calendars_mobile.php') {
		$('.calendar_view table td').not('.calendar_notes').css('pointer-events', '');
	}
	if(window.location.pathname != '/Calendar/calendars_mobile.php') {
		var deferred = $.Deferred();
		//setTimeout(function() {
			// $('body>.container').css('margin-bottom','-5em');
			// $('body>.container .main-screen').css('padding-bottom','0');
			// var diff = Math.round($(window).height() - $('#footer').offset().top - $('#footer').height()) - 10;
			// if($('.sidebar.panel-group').height() + diff > 200) {
			// 	$('.sidebar.panel-group').height($('.sidebar.panel-group').height() + diff);
			// }
			// $('.calendar_view').height($('.sidebar.panel-group').height());
			// $('.unbooked_view').height($('.calendar_view').closest('.col-xs-12').height());
			// var sidebar_headings = 0;
			// $('.sidebar.panel-group .panel:visible').each(function() {
			// 	sidebar_headings += $(this).outerHeight() - $(this).find('.panel-body').outerHeight();
			// })
			// $('.sidebar.panel-group .panel-body').outerHeight($('.sidebar.panel-group').outerHeight() - sidebar_headings);
			// $('.used-block').filter(function() { return !$(this).data('resized'); }).each(function() {
			// 	$(this).outerHeight($(this).closest('td').outerHeight() * $(this).data('blocks'));
			// 	$(this).data('resized',true);
			// });
		//}, 250);
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

		var height = $('.calendar-screen .scale-to-fill').outerHeight();
		$('.calendar_view').height('calc('+$('.calendar-screen .scale-to-fill').outerHeight()+'px - 4em)');
		$('.collapsible .sidebar').height('calc('+($('.calendar_view').height() - $('.search-text').outerHeight())+'px - 1.5em - 1px)');
		// $('.calendar_view').outerHeight($('.calendar_view').outerHeight() - $('.ticket-status-legend').outerHeight(true));

		var time_blocks = [];
		$('.calendar_view tbody tr').each(function() {
			time_blocks.push($(this).find('td').first()[0].outerHTML);
		});

		if($('#time_html').length < 1) {
			var time_width = $('.calendar_view tr').first().find('th').first().width() + 2;
			var time_html = '<table id="time_html" class="table table-bordered" style="position: absolute; left: 0px; top: -1px; z-index: 3; width: '+time_width+'px; background-color: #fff;">';
			time_html += '<thead><tr style="position: absolute; z-index: 4;">'+$('.calendar_view tr th').first()[0].outerHTML+'</tr></thead>';
			time_html += '<tbody>';
			time_blocks.forEach(function(html) {
				time_html += '<tr>'+html+'</tr>';
			});
			time_html += '</tbody></table>';
			$('.calendar_view').append(time_html);
		}
		$('#time_html th').first().height($('.calendar_view table:not(#time_html) tr th').first().height() + 1);
		$('#time_html tr').each(function() {
			if($(this).find('th').length > 0) {
				$(this).find('th').height($('.calendar_view table:not(#time_html) th first').height());
			} else {
				var datatime = $(this).find('td').data('time');
				$(this).find('td').height($('.calendar_view table:not(#time_html) td[data-time='+datatime+']').first().height() - 1);
			}
		});
		return deferred;
	}
}
function scrollHeader() {
	$('.calendar_view table:not(#time_html) tr').first().css('top',$('.calendar_view').first().prop('scrollTop'));
	$('.calendar_view table:not(#time_html) tr').first().css('z-index',2);

	$('#time_html th').first().height($('.calendar_view table:not(#time_html) tr th').first().height() + 1);
	$('#time_html tr').first().css('top',$('.calendar_view').first().prop('scrollTop'));
	$('#time_html').css('left', $('.calendar_view').first().prop('scrollLeft') - 1);


}
function expandDiv(link) {
    if ($(link).parent().find('.calendar_notes').css('max-height') == 'none') {
        $(link).closest('tr').find('.calendar_notes').css('max-height', '5em');
    } else {
        $(link).closest('tr').find('.calendar_notes').css('max-height', '');
    }
	resize_calendar_view();
}
force_resize_blocks = '';
function expandBlock() {
	$('.used-block span').unbind('mouseenter mouseleave').hover(function() {
		if($(this).closest('.used-block').height() < $(this).height()) {
			$(this).closest('.used-block').clearQueue().stop().animate({
				'min-height': $(this).height() + 5
			});
		}
        $(this).closest('td').css({
            zIndex: 2
        });
		clearTimeout(force_resize_blocks);
	}, function() {
		$(this).closest('.used-block').clearQueue().stop().animate({
			'min-height': '0'
		}, 'normal');
        $(this).closest('td').css({
            zIndex: 1
        });
		clearTimeout(force_resize_blocks);
        $('.used-block').each(function() {
            $(this).closest('td').css('z-index', 1);
        });
		force_resize_blocks = setTimeout(function() {
            reload_resize_all();
		}, 1000);
	});
}
function resizeBlocks() {
	$('.used-block').each(function() {
		$(this).closest('td').css('z-index', 1);
		var rows = $(this).data('blocks');
		var parent = $(this).closest('td');
		var header = 0;
		if (parent.prev().is('thead:visible')) {
			header = $(this).closest('table').find('thead tr').first()[0].clientHeight;
		}
		$(this).css('top', header);
		$(this).css('left', '0');
		$(this).css('margin', '0');
		$(this).css('padding', '0.2em');
		if($(this).hasClass('combined_blocks')) {
			$(this).css('padding', '0');
		}
		$(this).height((parent.innerHeight() * parseInt(rows)) - header);
		$(this).height('calc(' + $(this).height() + 'px - 2px + ' + rows + 'px)');
		$(this).closest('td').find('.ui-resizable-e').height((parent.innerHeight() * parseInt(rows)) - header);
		// $(this).width(parent.innerWidth());
		// $(this).width('calc(' + $(this).width() + 'px)');
	});
}
function reloadExpandDivs() {
    $('.expand-div-link').each(function() {
        var notes_row = $(this).parent().find('.calendar_notes');
        var max_height = parseFloat($(this).closest('td').css('font-size')) * 5;
        if (notes_row.height() >= max_height) {
            notes_row.css('max-height', '5em')
        } else {
            $(this).hide();
        }
    });
    $('.edit_calendar_notes').on('click', function() {
    	var td = $(this).closest('td');
    	var textarea = td.find('.calendar_notes_edit textarea');
    	var prev_html = td.find('.calendar_notes').html();
    	td.find('.calendar_notes').html(td.find('.calendar_notes').html().replace(/<br[^>]*>/gi,'\n'));
    	td.find('.calendar_notes').hide();
    	td.find('.calendar_notes_btn').hide();
    	td.find('.calendar_notes_edit').show();
    	textarea.text(td.find('.calendar_notes').text()).focus();
    	resize_calendar_view();
    	if(window.location.pathname == '/Calendar/calendars_mobile.php') {
    		$('.calendar_view table td').not('.calendar_notes').css('pointer-events', 'none');
    	}
    	return false;
    });
    $('.calendar_notes_edit').focusout(function() {
    	var td = $(this).closest('td');
    	var contact_id = td.data('contact');
    	var calendar_type = td.data('calendartype');
    	var calendar_mode = td.data('calendarmode');
    	var date = td.data('date');
    	var notes = $(this).find('textarea').val().replace(/\r\n|\r|\n/g,"<br />");
    	$.ajax({
    		url: '../Calendar/calendar_ajax_all.php?fill=calendar_notes&offline='+offline_mode,
    		method: 'POST',
    		data: { contact_id: contact_id, calendar_type: calendar_type, calendar_mode: calendar_mode, date: date, notes: notes },
    		success: function(response) {
    			td.find('.calendar_notes').html(response);
		    	td.find('.calendar_notes').show();
		    	td.find('.calendar_notes_btn').show();
		    	td.find('.calendar_notes_edit').hide();
		    	resize_calendar_view();
    		}
    	});
    })
}
function clearClickableCells() {
	if($('[name="edit_access"]').val() != 1) {
		$('.calendar_view table td').closest('td').css('background-color', '');
		$('.calendar_view table td').filter(function() { return $(this).data('contact') > 0 }).css('background-color', '');
		$('.calendar_view table td a.shift').remove();
	}
}
$(document).ready(function() {
	clearClickableCells();
	expandBlock();
	reloadExpandDivs();
	$('.calendar-screen .scale-to-fill,.calendar-screen .collapsible,.calendar-screen .scalable').css('min-height', '600px');
	$('#dialog-confirm').on('dialogclose',function() {
		// window.location.reload();
		reload_all_data();
	});
	$('.sidebar.panel-group').css('padding-right','0');
    if($(window).width() >= 768) {

		$('panel-heading').on('click', function() {
			resize_calendar_view();
		});
		$(window).on('resize', function() {
			resize_calendar_view();
		});
	} else {
		$('.collapsible').css('max-width','100%');
		$('.collapsible:before').css('display','none');
	}
});
function removeStaffEquipAssign(link) {
	var contactid = $(link).closest('.equip_assign_staff').data('contact');
	var contact_name = $(link).closest('.equip_assign_staff').data('contact-name');
	var equipment_assignid = $(link).closest('.equip_assign_block').data('equip-assign');
	var equipment_label = $(link).closest('.equip_assign_block').data('equip-label');
	var date = $(link).closest('.equip_assign_block').data('date');

	var new_contact = $(link).closest('.equip_assign_block').data('equip');
	var new_date = $(link).closest('.equip_assign_block').data('date');

	var confirm_string = "Are you sure you want to remove "+contact_name+" from "+equipment_label+" on "+date+"?";

	if(confirm(confirm_string)) {
		data = { contactid: contactid, equipment_assignid: equipment_assignid, date: date };
		$.ajax({
			url: '../Calendar/calendar_ajax_all.php?fill=equip_assign_remove_staff&offline='+offline_mode,
			method: 'POST',
			data: data,
			success: function(response) {
				// window.location.reload();
				var retrieve_collapse2_filter = '';
				var retrieve_collapse = $('#retrieve_collapse').val();
				var retrieve_collapse2 = $('#retrieve_collapse2').val();
				if(retrieve_collapse2 != undefined && retrieve_collapse2 != '') {
					retrieve_collapse2_filter = ',[id^='+retrieve_collapse2+']';
				}
				if(new_contact > 0) {
					var anchor = $('[id^='+retrieve_collapse+']'+retrieve_collapse2_filter).find('.block-item[data-equipment='+new_contact+']').closest('a');
					retrieve_items(anchor, new_date);
				} else {
				    reload_all_data();
				}
			}
		});
	}
}
function removeStaffTeam(link) {
	var contactid = $(link).closest('.team_staff').data('contact');
	var contact_name = $(link).closest('.team_staff').data('contact-name');
	var teamid = $(link).closest('.team_assign_block').data('team');
	var date = $(link).closest('.team_assign_block').data('date');

	var confirm_string = "Are you sure you want to remove "+contact_name+" from this team on "+date+"?";

	if(confirm(confirm_string)) {
		data = { contactid: contactid, teamid: teamid, date: date };
		$.ajax({
			url: '../Calendar/calendar_ajax_all.php?fill=team_assign_remove_staff',
			method: 'POST',
			data: data,
			success: function(response) {
				reload_teams(response);
			}
		});
	}
}

function finish_offline() {
	$.ajax({
		url: 'calendar_ajax_all.php?action=finish_edits'
	});
}

function loadUnbookedList(anchor) {
	ticket_list = [];
	result_list = [];
	continue_loading = '';
	clearTimeout(continue_loading);
	if($(anchor).data('type') == 'appt') {
		$('a.unbooked_anchor[data-type=ticket]').removeClass('active');
		$('.calendar-screen .unbooked_view').html('');
		$('.calendar-screen .unbooked_view').remove();
	} else if($(anchor).data('type') == 'ticket') {
		$('a.unbooked_anchor[data-type=appt]').removeClass('active');
		$('.calendar-screen .unbooked_view').html('');
		$('.calendar-screen .unbooked_view').remove();
	}
	if($(anchor).hasClass('unbooked_anchor') && $('a.all_tickets_anchor').hasClass('active')) {
		$('.calendar-screen .unbooked_view').html('');
		$('.calendar-screen .unbooked_view').remove();
		$('a.all_tickets_anchor').removeClass('active');
	} else if($(anchor).hasClass('all_tickets_anchor') && $('a.unbooked_anchor').hasClass('active')) {
		$('.calendar-screen .unbooked_view').html('');
		$('.calendar-screen .unbooked_view').remove();
		$('a.unbooked_anchor').removeClass('active');
	}
	if($(anchor).hasClass('active')) {
		$('.calendar-screen .unbooked_view').html('');
		$('.calendar-screen .unbooked_view').remove();
		$(anchor).removeClass('active');
	} else {
		var href = $(anchor).data('href');
		var calendar_dates = JSON.parse($('#calendar_dates').val());
		var start_date = calendar_dates.shift();
		var end_date = calendar_dates.pop();
		if(end_date == undefined || end_date == '') {
			end_date = start_date;
		}
		$.ajax({
			url: '../Calendar/unbooked.php'+href+'&search_start_date='+start_date+'&search_end_date='+end_date,
			success: function(response) {
				var unbooked_html = '<div class="pull-right scalable unbooked_view" style="height: 30em; overflow: auto;">'+response+'</div>';
				$('.calendar-screen .collapsible').after(unbooked_html);
				resize_calendar_view();	
				reloadDragResize();
				setSelectOnChange();
				unbookedDraggable();
			}
		});
		$(anchor).addClass('active');
	}
}

function removeTicketSchedule(ticketid, stopid) {
    $.post('calendar_ajax_all.php?action=removeSchedule', {
        ticketid: ticketid,
        stopid: stopid
    }, function(response) {
        $('[data-ticket='+ticketid+']'+(stopid > 0 ? '[data-ticketscheduleid='+stopid+']' : '')).each(function() {
            var td = $(this).closest('td');
            retrieve_items($('[id^='+$('#retrieve_collapse').val()+']').find('.block-item[data-'+$('#retrieve_contact').val()+'='+$(td).data('contact')+']').closest('a'),$(td).data('date'))
        });
    });
}
function setDaysheetReminder(input) {
    var daysheet_reminder = $(input);
    var daysheetreminderid = input.value;
    var done = 0;
    if ($(input).is(':checked')) {
        done = 1;
    }
    $.ajax({
        url: '../Profile/profile_ajax.php?fill=daysheet_reminders',
        method: 'POST',
        data: {
            daysheetreminderid: daysheetreminderid,
            done: done
        },
        success: function(response) {
            if (done == 1) {
                daysheet_reminder.closest('.daysheet_row').find('span').css('text-decoration', 'line-through');
            } else {
                daysheet_reminder.closest('.daysheet_row').find('span').css('text-decoration', 'none');
            }
        }
    });
}
									 
									 
										 
				 
								  
				 
	 
			
																   
					   
			   
												   
					  
		  
									 
							
																											   
					
																									   
			 
		 
	   
 

// Functions for the hover icons for Customer Sign Off on Deliveries
function display_camera(img) {
	if($(img).hasClass('active')) {
	    var left  = ($(img).offset().left + 25) + "px";
	    var top  = $(img).offset().top + "px";

		if($('#camera_hover').not(':visible')) {
		    $('#camera_hover').css('left', left);
		    $('#camera_hover').css('top', top);
			$('#camera_hover').show().html('Loading...');
			$('#camera_hover').html('<p><b>'+$(img).data('label')+'</b></p><img src="'+$(img).data('file')+'" style="max-width: 300px; max-height: 300px; width: auto; height: auto;">');
		}
	} else {
		hide_camera();
	}
}
function hide_camera() {
	$('#camera_hover').hide().html('Loading...');
}
function display_signature(img) {
	if($(img).hasClass('active')) {
	    var left  = ($(img).offset().left + 25) + "px";
	    var top  = $(img).offset().top + "px";

		if($('#signature_hover').not(':visible')) {
		    $('#signature_hover').css('left', left);
		    $('#signature_hover').css('top', top);
			$('#signature_hover').show().html('Loading...');
			$('#signature_hover').html('<img src="'+$(img).data('file')+'" style="max-width: 300px; max-height: 300px; width: auto; height: auto;">');
		}
	} else {
		hide_camera();
	}
}
function hide_signature() {
	$('#signature_hover').hide().html('Loading...');
}
function display_star_rating(img) {
	if($(img).hasClass('active')) {
	    var left  = ($(img).offset().left + 25) + "px";
	    var top  = $(img).offset().top + "px";
	    var rating_html = $(img).closest('.used-block').find('.star_rating_hover_html').clone();
	    $(rating_html).css('display', '');

		if($('#star_rating_hover').not(':visible')) {
		    $('#star_rating_hover').css('left', left);
		    $('#star_rating_hover').css('top', top);
			$('#star_rating_hover').show().html('Loading...');
			$('#star_rating_hover').html(rating_html);
		}
	} else {
		hide_star_rating();
	}
}
function hide_star_rating() {
	$('#star_rating_hover').hide().html('Loading...');
}
function display_customer_notes(img) {
	if($(img).hasClass('active')) {
	    var left  = ($(img).offset().left + 25) + "px";
	    var top  = $(img).offset().top + "px";
	    var notes_html = $(img).closest('.used-block').find('.customer_notes_hover_html').clone();
	    $(notes_html).css('display', '');

		if($('#customer_notes_hover').not(':visible')) {
		    $('#customer_notes_hover').css('left', left);
		    $('#customer_notes_hover').css('top', top);
			$('#customer_notes_hover').show().html('Loading...');
			$('#customer_notes_hover').html(notes_html);
		}
	} else {
		hide_customer_notes();
	}
}
function hide_customer_notes() {
	$('#customer_notes_hover').hide().html('Loading...');
}
function view_customer_notes(url) {
	overlayIFrameSlider(url, 'auto', true, true);
	return false;
}