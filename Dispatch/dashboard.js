result_list = [];
$(document).ready(function() {
    $( ".connectedChecklist" ).sortable({
		beforeStop: function(e, ui) { ui.helper.removeClass('popped-field'); },
		connectWith: ".connectedChecklist",
		delay: 500,
		handle: ".drag_handle",
		//items: "li:not(.no-sort)",
		start: function(e, ui) {ui.helper.addClass('popped-field'); }
    });

	// Load the Visualization API and the corechart package.
	google.charts.load('current', {'packages':['corechart']});

	$('.dispatch-legend-block').on('mouseover', function() { toggle_ticket_legend('show') });
	$('.dispatch-legend-block').on('mouseout', function() { toggle_ticket_legend('hide') });
	set_url_with_current_date();
	reload_all_data();
	$(window).resize(function() {
		resize_blocks();
	}).resize();
	$('.search-fields').find('input,select').change(function() { search_filters(this) });

	$('.sidebar-higher-level:not(.active_li,.highest_level)').click(function(event) {
		if($(event.target).hasClass('cursor-hand')) {
			$('.sidebar-higher-level:not(.active_li,.highest_level)').not(this).find('a.cursor-hand:not(.collapsed)').click();
		}
	});
});
$(document).on('click', 'a', resize_blocks);
$(document).on('click', '.active_li .active_li_item', function() {
	var parent = $(this).closest('.active_li');
	var accordion = $(parent).data('accordion');
	var activevalue = $(this).data('activevalue');
	$('#'+accordion).find('a[data-activevalue="'+activevalue+'"]').find('li.active').closest('a').click();
});
var auto_refresh_dispatch = '';
function setAutoRefresh() {
	clearTimeout(auto_refresh_dispatch);
	var refresh_time = parseInt($('#dispatch_auto_refresh').val()) * 1000;if(refresh_time != '' && refresh_time > 0) {
		auto_refresh_dispatch = setTimeout(function() { reload_summary_tab(); retrieve_active_equipment(); }, refresh_time);
	}
}
function resize_blocks() {
	if($('#summary_tab li').hasClass('active')) {
		$('.tile-sidebar li,.tile-sidebar a.cursor-hand').not('#summary_tab li').removeClass('active blue');
		$('.dispatch-equipment-list').hide();
		$('.dispatch-summary-tab-list').show();
	} else {
		$('.dispatch-equipment-list').show();
		$('.dispatch-summary-tab-list').hide();
		$('.standard-collapsible').find('li.sidebar-higher-level').each(function() {
			$(this).find('a.cursor-hand').first().removeClass('active blue');
			if($(this).find('li.active').length > 0) {
				$(this).find('a.cursor-hand').first().addClass('active blue');
			}
		});	
	}
	if($('.dispatch-equipment-group:visible').length > 0 || $('#summary_tab li').hasClass('active')) {
		$('.dispatch-equipment-list-none').hide();
		if($('.dispatch-summary-tab').length == 0 && $('#summary_tab li').hasClass('active')) {
			$('.dispatch-summary-list-none').show();
		} else {
			$('.dispatch-summary-list-none').hide();
		}
	} else {
		$('.dispatch-summary-list-none').hide();
		$('.dispatch-equipment-list-none').show();
	}

	$('.dispatch-equipment-group').css('min-height', 0);
	height_diff = $(window).height() - $('.standard-body-content').offset().top - $('footer:visible').height() - ($('.double-scroller').outerHeight() * 2);

	if($('.standard-body-content').width() < 768) {
		block_width = 'calc(100% - 1em)';
	} else if($('.standard-body-content').width() < 1280) {
		block_width = 'calc('+($('.standard-body-content').width() / 2)+'px'+' - 2em - 2px)';
		summary_width = 'calc('+($('.standard-body-content').width() / 3)+'px'+' - 2em - 2px)';
	} else {
		block_width = 'calc('+($('.standard-body-content').width() / 3)+'px'+' - 2em - 2px)';
		summary_width = 'calc('+($('.standard-body-content').width() / 3)+'px'+' - 2em - 2px)';
	}
	block_height = $('.dispatch-equipment-group:visible').first().height();
	$('.dispatch-equipment-group:visible').each(function() {
		if($(this).height() > block_height) {
			block_height = $(this).height() + 2;
		}
	});
	if(block_height < height_diff) {
	}
	block_height = 'calc('+height_diff+'px - 1em - 2px)';

	$('.dispatch-equipment-group').css('height', block_height);
	$('.dispatch-equipment-content:visible').each(function() {
		var group = $(this).closest('.dispatch-equipment-group');
		var content_height = $(group).outerHeight() - $(group).find('.dispatch-equipment-title').outerHeight() - 2;
		$(this).css('height', content_height);
	});
	$('.dispatch-equipment-group').css('width', block_width);
	$('.dispatch-summary-block').css('width', summary_width);
	$('.double-scroller div').width($('.dispatch-equipment-list').get(0).scrollWidth);
	$('.double-scroller').off('scroll',double_scroll).scroll(double_scroll);
	$('.dispatch-equipment-list').off('scroll',set_double_scroll).scroll(set_double_scroll);
	display_active_blocks();
	redraw_charts();
}
function set_url_with_current_date() {
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
	query_string_arr["date"] = $('[name="search_date"]').val();
	var new_url = "?"+$.param(query_string_arr);
	window.history.replaceState(null, '', new_url);
}
function double_scroll() {
	$('.dispatch-equipment-list').scrollLeft(this.scrollLeft).scroll();
}
function set_double_scroll() {
	$('.double-scroller').scrollLeft(this.scrollLeft);
}
function reload_summary_tab() {
	summary_list = [];
	still_loading_summary = false;
	if(loading_buttons) {
		setTimeout(function() {
			reload_summary_tab();
		},250);
	} else {
		var date = $('[name="search_date"]').val();
		var equipment = [];
		$('.dispatch-equipment-button').each(function() {
			equipment.push($(this).data('equipment'));
		});
		equipment = JSON.stringify(equipment);
		$.ajax({
			url: '../Dispatch/ajax.php?action=sort_equipment_latest_updated',
			method: 'POST',
			data: { date: date, equipment: equipment },
			success:function(response) {
				equipment = JSON.parse(response);
				equipment.forEach(function(equipmentid) {
					retrieve_summary_tab(equipmentid);
				});
			}
		});
	}
}
still_loading_summary = false;
summary_list = [];
function retrieve_summary_tab(equipmentid = '') {
	if(still_loading_summary) {
		var next_item = function() { retrieve_summary_tab(equipmentid) };
		summary_list.push(next_item);
	} else {
		still_loading_summary = true;
		loadingOverlayShow('.standard-body');
		var date = $('[name="search_date"]').val();
		if(equipmentid != undefined && equipmentid != '') {
			$.ajax({
				url: '../Dispatch/dashboard_load_summary_tab.php',
				method: 'POST',
				data: { date: date, equipmentid: equipmentid },
				dataType: 'html',
				success: function(response) {
					if(result_list['summary_tab'] == undefined) {
						result_list['summary_tab'] = [];
					}
					var response = JSON.parse(response);
					var item_row = $.grep(result_list['summary_tab'], function(row) {
						return row['equipmentid'] == equipmentid;
					});
					if(item_row.length > 0) {
						for(key in item_row[0]) {
							if(key != undefined && response[key] != undefined) {
								item_row[0][key] = response[key];
							}
						}
					} else {
						result_list['summary_tab'].push(response);
					}
					load_summary_tab(equipmentid);
					filter_equipment();
					still_loading_summary = false;
					if(summary_list.length > 0) {
						summary_list.shift()();
					} else {
						loadingOverlayHide();
					}
				}
			});
		} else {
			still_loading_summary = false;
			if(loading_list.length > 0) {
				loading_list.shift()();
			} else {
				loadingOverlayHide();
			}
		}
		setAutoRefresh();
	}
}
function redraw_charts() {
	if(all_chart_data['summary_tab'] != undefined) {
		all_chart_data['summary_tab'].forEach(function(chart) {
			chart();
		});
	}
	if(all_chart_data['ontime_chart'] != undefined) {
		all_chart_data['ontime_chart']();
	}
	if(all_chart_data['status_chart'] != undefined) {
		all_chart_data['status_chart']();
	}
}
all_chart_data = [];
function load_summary_tab(equipmentid) {
	var item_row = $.grep(result_list['summary_tab'], function(row) {
		return row['equipmentid'] == equipmentid;
	});
	var stop_count = 0;
	item_row[0]['ontime_summary'].forEach(function(summary) {
		stop_count += summary['count'];
	});

	if(stop_count > 0 && $('#summary_tab li').hasClass('active')) {
		var group_exists = $('.dispatch-summary-tab[data-equipment="'+equipmentid+'"]');
		var prev_equip = '';
		var next_equip = '';
		var prev_equip_search = '';
		var next_equip_search = '';
		if(group_exists.length > 0) {
			var prev_equip = $(group_exists).prev('.dispatch-summary-tab').data('equipment');
			var next_equip = $(group_exists).next('.dispatch-summary-tab').data('equipment');
			if(prev_equip > 0) {
				prev_equip_search = $(group_exists).prev('.dispatch-summary-tab');
			}
			if(next_equip > 0) {
				next_equip_search = $(group_exists).next('.dispatch-summary-tab');
			}
		}
		if($('.dispatch-summary-tab').length > 0) {
			$('.dispatch-summary-tab').not(group_exists).each(function() {
				if($(this).data('latest-updated-time') > item_row[0]['latest_updated_time'] && ($(this).data('latest-updated-time') < $(prev_equip_search).data('latest-updated-time') || prev_equip_search == '')) {
					prev_equip_search = this;
					prev_equip = $(prev_equip_search).data('equipment');
				} else if($(this).data('latest-updated-time') < item_row[0]['latest_updated_time'] && ($(this).data('latest-updated-time') > $(next_equip_search).data('latest-updated-time') || next_equip_search == '')) {
					next_equip_search = this;
					next_equip = $(prev_equip_search).data('equipment');
				}
			});
		}

		if(group_exists.length > 0 && prev_equip == $(group_exists).prev('.dispatch-summary-tab').data('equipment') && next_equip == $(group_exists).next('.dispatch-summary-tab').data('equipment')) {
			$(group_exists).replaceWith(item_row[0]['html']);
		} else {
			$(group_exists).remove();
			if(prev_equip > 0 && item_row[0]['latest_updated_time'] > 0) {
				$('.dispatch-summary-tab[data-equipment="'+prev_equip+'"]').after(item_row[0]['html']);
			} else if(next_equip > 0 && item_row[0]['latest_updated_time'] > 0) {
				$('.dispatch-summary-tab[data-equipment="'+next_equip+'"]').before(item_row[0]['html']);
			} else {
				$('.dispatch-summary-tab-list').append(item_row[0]['html']);
			}
		}

		var block = $('.dispatch-summary-tab[data-equipment="'+equipmentid+'"] .dispatch-summary-tab-block');
		var ontime_data = new google.visualization.DataTable();
		ontime_arr = [];
		ontime_arr.push (['On Time', 'Count', { role: 'style' }]);
		item_row[0]['ontime_summary'].forEach(function(ontime) {
			ontime_arr.push([ontime['label'], ontime['count'], ontime['color']]);
		});
		var ontime_data = google.visualization.arrayToDataTable(ontime_arr);
		var ontime_options = {
	        chartArea:{ top: 0 },
			height: 110,
			legend: { position: 'none' }
		};
		var ontime_chart = new google.visualization.BarChart($(block)[0]);
	    ontime_chart.draw(ontime_data, ontime_options);
	    if(all_chart_data['summary_tab'] == undefined) {
	    	all_chart_data['summary_tab'] = [];
	    }
	    all_chart_data['summary_tab'][equipmentid] = function() { ontime_chart.draw(ontime_data, ontime_options); };	
	}

	setTimeout(function() { $(window).resize(); }, 500);
}
loading_buttons = false;
function reload_all_data() {
	hide_summary();
	all_chart_data = [];
	loading_list = [];
	still_loading_item = false;
	$('.dispatch-summary-tab-list').html('');
	$('.dispatch-equipment-list').html('');
	filter_equipment();
	retrieve_buttons();
	reload_summary_tab();
	retrieve_active_equipment();
	setAutoRefresh();
}
function retrieve_active_equipment() {
	if(loading_buttons) {
		setTimeout(function() {
			retrieve_active_equipment();
		},250);
	} else {
		var equipment = [];
		$('.dispatch-equipment-button.active').each(function() {
			equipment.push($(this).data('equipment'));
		});
		equipment.forEach(function(equipmentid) {
			retrieve_tickets(equipmentid);
		});
	}
}
still_loading_item = false;
loading_list = [];
function retrieve_tickets(equipmentid = '') {
	if(still_loading_item) {
		var next_item = function() { retrieve_tickets(equipmentid) };
		loading_list.push(next_item);
	} else {
		still_loading_item = true;
		loadingOverlayShow('.standard-body');
		var date = $('[name="search_date"]').val();
		if(equipmentid != undefined && equipmentid != '') {
			$.ajax({
				url: '../Dispatch/dashboard_load_equipment.php',
				method: 'POST',
				data: { date: date, equipmentid: equipmentid },
				dataType: 'html',
				success: function(response) {
					if(result_list['equipment'] == undefined) {
						result_list['equipment'] = [];
					}
					var response = JSON.parse(response);
					var item_row = $.grep(result_list['equipment'], function(row) {
						return row['equipmentid'] == equipmentid;
					});
					if(item_row.length > 0) {
						for(key in item_row[0]) {
							if(key != undefined && response[key] != undefined) {
								item_row[0][key] = response[key];
							}
						}
					} else {
						result_list['equipment'].push(response);
					}
					load_tickets(equipmentid);
					filter_equipment();
					still_loading_item = false;
					if(loading_list.length > 0) {
						loading_list.shift()();
					} else {
						loadingOverlayHide();
					}
				}
			});
		} else {
			still_loading_item = false;
			if(loading_list.length > 0) {
				loading_list.shift()();
			} else {
				loadingOverlayHide();
			}
		}
		setAutoRefresh();
	}
}
function retrieve_buttons() {
	loading_buttons = true;
	var date = $('[name="search_date"]').val();
	var active_equipment = [];
	$('.dispatch-equipment-button:active').each(function() {
		active_equipment.push($(this).data('equipment'));
	});
	active_equipment = JSON.stringify(active_equipment);
	$('.dispatch-equipment-buttons').html('Loading...');
	$.ajax({
		url: '../Dispatch/dashboard_load_buttons.php',
		method: 'POST',
		data: { date: date, active_equipment: active_equipment },
		success: function(response) {
			var response = JSON.parse(response);
			result_list['buttons'] = response['buttons'];
			result_list['active_li'] = response['active_li'];
			load_buttons();
		}
	});
}
function load_buttons() {
	$('.dispatch-equipment-buttons').html('');
	var current_region = '';
	result_list['buttons'].forEach(function(button) {
		if(current_region != button['region']['label'] && $('[name="group_regions"]').val() == 1) {
			current_region = button['region']['label'];
			$('.dispatch-equipment-buttons').append(button['region']['html']);	
		}
		$('.dispatch-equipment-buttons').append(button['html']);
	});
	$('.equip_active_li ul').html('');
	$('.equip_active_li ul').html(result_list['active_li']);

	setTimeout(function() { $(window).resize(); loading_buttons = false; }, 500);
}
function load_tickets(equipmentid) {
	var item_row = $.grep(result_list['equipment'], function(row) {
		return row['equipmentid'] == equipmentid;
	});

	var button_a = $('.dispatch-equipment-buttons .dispatch-equipment-button-a[data-equipment="'+equipmentid+'"]');
	var prev_equip = $(button_a).prev('.dispatch-equipment-button-a').data('equipment');
	var next_equip = $(button_a).next('.dispatch-equipment-button-a').data('equipment');

	var group_exists = $('.dispatch-equipment-group[data-equipment="'+equipmentid+'"]');
	if(group_exists.length > 0) {
		$('.dispatch-equipment-group[data-equipment="'+equipmentid+'"]').replaceWith(item_row[0]['html']);
	} else if(prev_equip != undefined && $('.dispatch-equipment-group[data-equipment="'+prev_equip+'"]').length > 0) {
		$('.dispatch-equipment-group[data-equipment="'+prev_equip+'"]').after(item_row[0]['html']);
	} else if(next_equip != undefined && $('.dispatch-equipment-group[data-equipment="'+next_equip+'"]').length > 0) {
		$('.dispatch-equipment-group[data-equipment="'+next_equip+'"]').before(item_row[0]['html']);
	} else {
		$('.dispatch-equipment-list').append(item_row[0]['html']);
	}
	
	setTimeout(function() { $(window).resize(); }, 500);
}
function hide_summary() {
	delete all_chart_data['ontime_chart'];
	delete all_chart_data['status_chart'];
	$('.dispatch-equipment-summary').html('').hide();
	$('.dispatch-equipment-summary-title').html('').hide();
	$('.dispatch-summary').hide();
	resize_blocks();
	$('.dispatch-summary-icon').removeClass('dispatch-summary-active');
}
function retrieve_summary(img, equipmentid, keep_visible = 0) {
	$('.dispatch-summary-icon').removeClass('dispatch-summary-active');
	var date = $('[name="search_date"]').val();
	if($('.dispatch-summary-group').is(':visible') && $('.dispatch-summary-group').data('equipment') == equipmentid && keep_visible != 1) {
		hide_summary();
	} else {
		$(img).find('.dispatch-summary-icon').addClass('dispatch-summary-active');
		loadingOverlayShow('.standard-body');
		var equipment = [];
		if(equipmentid == 'ALL') {
			$('.dispatch-equipment-button').each(function() {
				equipment.push($(this).data('equipment'));
			});
		} else if(equipmentid == 'VISIBLE') {
			if($('#summary_tab li').hasClass('active')) {
				$('.dispatch-summary-tab:visible').each(function() {
					equipment.push($(this).data('equipment'));
				});
			} else {
				$('.dispatch-equipment-button.active').each(function() {
					equipment.push($(this).data('equipment'));
				});
			}
		} else {
			equipment.push(equipmentid);
		}
		equipment = JSON.stringify(equipment);
		$.ajax({
			url: '../Dispatch/dashboard_load_summary.php',
			method: 'POST',
			data: { date: date, equipmentid: equipmentid, equipment: equipment },
			success: function(response) {
				if(result_list['summary'] == undefined) {
					result_list['summary'] = [];
				}
				var response = JSON.parse(response);
				var item_row = $.grep(result_list['summary'], function(row) {
					return row['equipmentid'] == equipmentid;
				});
				if(item_row.length > 0) {
					for(key in item_row[0]) {
						if(key != undefined && response[key] != undefined) {
							item_row[0][key] = response[key];
						}
					}
				} else {
					result_list['summary'].push(response);

				}
				load_summary(equipmentid);
				drag_blocks();

				loadingOverlayHide();
			}
		});
	}
}

function drag_blocks() {
	$( ".connectedChecklist" ).sortable({
		beforeStop: function(e, ui) { ui.helper.removeClass('popped-field'); },
		connectWith: ".connectedChecklist",
		delay: 500,
		handle: ".drag_handle",
		//items: "li:not(.no-sort)",
		start: function(e, ui) {ui.helper.addClass('popped-field'); },
		update: function( event, ui ) {
			var block_name = ui.item.attr("class"); //Done
			var block_change = block_name.split(' ');

			var after_block_name = ui.item.prev().attr('class');
			if (typeof after_block_name === "undefined") {
				var after_block_change = 0;
			} else {
				var block_change = after_block_name.split(' ');	
				var after_block_change = block_change[1];
			}

			//var table_class = ui.item.parent().attr("class");
			//var status = table_class.split(' ');
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "../Dispatch/ajax.php?action=summary_block_reorder&block_change="+block_change[1]+"&after_block_change="+after_block_change,
				dataType: "html",   //expect html to be returned
				success: function(response){
				}
			});
		}
	});
}

function load_summary(equipmentid) {
	resize_blocks();
	var ontime_data = new google.visualization.DataTable();
	ontime_arr = [];
	ontime_arr.push (['On Time', 'Count', { role: 'style' }]);

	var status_data = new google.visualization.DataTable();
	status_data.addColumn('string', 'Status');
	status_data.addColumn('number', 'Count');
	status_colors = [];

	var item_row = $.grep(result_list['summary'], function(row) {
		return row['equipmentid'] == equipmentid;
	});
	item_row[0]['status_summary'].forEach(function(status) {
		status_data.addRow([status['status'], status['count']]);
		status_colors.push(status['color']);
	});
	item_row[0]['ontime_summary'].forEach(function(ontime) {
		ontime_arr.push([ontime['label'], ontime['count'], ontime['color']]);
	});

	$('.dispatch-summary').show();
	$('.dispatch-equipment-summary').html(item_row[0]['summary']).show();
	$('.dispatch-equipment-summary-title').html('<h4>Summary - '+item_row[0]['label']+'</h4>').show();
	resize_blocks();

	var ontime_data = google.visualization.arrayToDataTable(ontime_arr);
	var ontime_options = {
		title: 'On Time Summary',
		legend: { position: 'none' }
	};

	if($(".dispatch-summary-ontime")[0]){
		var ontime_chart = new google.visualization.ColumnChart($('.dispatch-summary-ontime')[0]);
		ontime_chart.draw(ontime_data, ontime_options);
		all_chart_data['ontime_chart'] = function() { ontime_chart.draw(ontime_data, ontime_options); };
	}

	var status_options = {
		title: 'Status Summary',
		is3D: true,
		colors: status_colors
	};

	if($(".dispatch-summary-status")[0]){
		var status_chart = new google.visualization.PieChart($('.dispatch-summary-status')[0]);
		status_chart.draw(status_data, status_options);
		all_chart_data['status_chart'] = function() { status_chart.draw(status_data, status_options); };
	}

	$( "div.dispatch-summary-status-count" ).html('<b>Status List Summary</b><br><br>');
	item_row[0]['status_summary'].forEach(function(status) {
		//status_data.addRow([status['status'], status['count']]);
		$( "div.dispatch-summary-status-count" ).append('<br>'+status['status']+' : '+status['count']);
	});

}
function search_filters(field) {
	if(field.name == 'search_date') {
		result_list = [];
		$('.standard-body-title h3').text('Dispatch Schedule - '+field.value);
		set_url_with_current_date();
		reload_all_data();
		reload_summary_tab();
	} else {
		filter_equipment();
	}
	resize_blocks();
}
still_toggling = '';
function display_active_blocks() {
	clearInterval(still_toggling);
	still_toggling = setInterval(function() {
		if($('#summary_tab li').hasClass('active')) {
			$('.active_li').hide();
		} else if(!($('.collapsing').length > 0)) {
			clearInterval(still_toggling);
			$('.active_li .active_li_item,.active_li').hide();
			$('.active_li').each(function() {
				var accordion = $(this).data('accordion');
				$(this).find('.active_li_item').each(function() {
					var active_value = $(this).data('activevalue');
					if($('#'+accordion+' a[data-activevalue="'+active_value+'"]').find('li').hasClass('active')) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});

				if($('#'+accordion).hasClass('in') || $(this).find('.active_li_item').filter(function() { return $(this).css('display') != 'none'; }).length == 0) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
		}
	}, 250);
}
function filter_sidebar(a = '') {
	if($(a).is('#summary_tab')) {
		//hide_summary();
		$(a).find('li').addClass('active blue');
		retrieve_summary_tab();
	} else if(a != '') {
		$(a).find('li').toggleClass('active blue');
		$('#summary_tab').find('li').removeClass('active blue');
	}

	var regions = [];
	var locations = [];
	var classifications = [];

	$('#collapse_region').find('li.active').each(function() {
		var anchor = $(this).closest('a');
		var region = $(anchor).data('region');
		regions.push(region);
	});

	$('#collapse_location').find('li.active').each(function() {
		var anchor = $(this).closest('a');
		var location = $(anchor).data('location');
		locations.push(location);
	});

	$('#collapse_classification').find('li').each(function() {
		var anchor = $(this).closest('a');
		$(anchor).show();

		var class_regions = $(anchor).data('region');
		if(regions.length > 0) {
			if(class_regions.length > 0) {
				class_regions.forEach(function(class_region) {
					if(regions.indexOf(class_region) < 0) {
						$(anchor).hide();
						$(anchor).find('li').removeClass('active blue');
					}
				});
			} else {
				$(anchor).hide();
				$(anchor).find('li').removeClass('active blue');
			}
		}
		if($(anchor).find('li').hasClass('active blue')) {
			var classification = $(anchor).data('classification');
			classifications.push(classification);

		}
	});

	$('#collapse_equipment').find('li').each(function() {
		var anchor = $(this).closest('a');
		$(anchor).show();

		var equip_regions = $(anchor).data('region');
		var equip_locations = $(anchor).data('location');
		var equip_classifications = $(anchor).data('classification');

		if(regions.length > 0) {
			if(equip_regions.length > 0) {
				equip_regions.forEach(function(equip_region) {
					if(regions.indexOf(equip_region) < 0) {
						$(anchor).hide();
						$(anchor).find('li').removeClass('active blue');
					}
				});
			} else {
				$(anchor).hide();
				$(anchor).find('li').removeClass('active blue');
			}
		}
		if(locations.length > 0) {
			if(equip_locations.length > 0) {
				equip_locations.forEach(function(equip_location) {
					if(locations.indexOf(equip_location) < 0) {
						$(anchor).hide();
						$(anchor).find('li').removeClass('active blue');
					}
				});
			} else {
				$(anchor).hide();
				$(anchor).find('li').removeClass('active blue');
			}
		}
		if(classifications.length > 0) {
			if(equip_classifications.length > 0) {
				equip_classifications.forEach(function(equip_classification) {
					if(classifications.indexOf(equip_classification) < 0) {
						$(anchor).hide();
						$(anchor).find('li').removeClass('active blue');
					}
				});
			} else {
				$(anchor).hide();
				$(anchor).find('li').removeClass('active blue');
			}
		}
	});

	filter_equipment();
}
function summary_select_equipment(a) {
	var equipmentid = $(a).closest('.dispatch-summary-tab').data('equipment');
	if($('a[data-target="#collapse_equipment_view"]').is('.collapsed')) {
		$('a[data-target="#collapse_equipment_view"]').click();
	}
	if($('a[data-target="#collapse_equipment"]').is('.collapsed')) {
		$('a[data-target="#collapse_equipment"]').click();
	}
	$('.dispatch-equipment-button-a[data-equipment="'+equipmentid+'"]').click();
}
function select_equipment(a) {
	$('#summary_tab li').removeClass('active blue');
	var block = $(a).find('li').toggleClass('active blue');
	if($(block).hasClass('active')) {
		retrieve_tickets($(block).data('equipment'));
	} else {
		$('.dispatch-equipment-group[data-equipment="'+$(block).data('equipment')+'"]').hide();
	}
	filter_equipment();
}
function filter_equipment() {
	$('.dispatch-equipment-group').hide();
	$('.dispatch-equipment-button.active').each(function() {
		if($(this).closest('a').css('display') != 'none') {
			$('.dispatch-equipment-group[data-equipment="'+$(this).data('equipment')+'"]').show();
		}
	});
	$('.dispatch-summary-tab').show();

	var region = $('[name="search_region"]').val();
	var location = $('[name="search_location"]').val();
	var classification = $('[name="search_classification"]').val();
	var business = $('[name="search_business"]').val();

	var filter_query = '';
	if(region != undefined && region != '') {
		filter_query += '[data-region="'+region+'"]';
		$('.dispatch-summary-tab').each(function() {
			if($(this).data('region').indexOf(region) == -1) {
				$(this).hide();
			}
		});
	}
	if(location != undefined && location != '') {
		filter_query += '[data-location="'+location+'"]';
		$('.dispatch-summary-tab').each(function() {
			if($(this).data('location').indexOf(location) == -1) {
				$(this).hide();
			}
		});
	}
	if(classification != undefined && classification != '') {
		filter_query += '[data-classification="'+classification+'"]';
		$('.dispatch-summary-tab').each(function() {
			if($(this).data('classification').indexOf(classification) == -1) {
				$(this).hide();
			}
		});
	}
	if(business != undefined && business > 0) {
		filter_query += '[data-businessid="'+business+'"]';
	}
	if(filter_query != '') {
		$('.dispatch-equipment-block').hide();
		$('.dispatch-equipment-block'+filter_query).show();
	} else {
		$('.dispatch-equipment-block').show();
	}

	$('.dispatch-region-block').hide();
	$('.dispatch-region-block').each(function() {
		var block = this;
		$(this).nextAll('.dispatch-equipment-button-a').each(function() {
			if($(this).css('display') != 'none') {
				$(block).show();
			}
		});
	});

	initTooltips();
	resize_blocks();

	if($('.visible_summary').hasClass('dispatch-summary-active')) {
		retrieve_summary($('.visible_summary').closest('a'), 'VISIBLE', 1);
	}
}
function select_all_buttons(a) {
	if($(a).is(':checked')) {
		$('.dispatch-equipment-button').addClass('active blue');
	} else {
		$('.dispatch-equipment-button').removeClass('active blue');
	}
	filter_equipment();
}
function show_search_fields() {
	if($('.search-fields').is(':visible')) {
		$('.search-fields').hide();
	} else {
		$('.search-fields').show();
	}
	resize_blocks();
}
function display_camera(img) {
	if($(img).hasClass('active')) {
	    var left  = (event.clientX + 25) + "px";
	    var top  = event.clientY + "px";

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
	    var left  = (event.clientX + 25) + "px";
	    var top  = event.clientY + "px";

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
	    var left  = (event.clientX + 25) + "px";
	    var top  = event.clientY + "px";
	    var rating_html = $(img).closest('.dispatch-equipment-block').find('.star_rating_hover_html').clone();
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
	    var left  = (event.clientX + 25) + "px";
	    var top  = event.clientY + "px";
	    var notes_html = $(img).closest('.dispatch-equipment-block').find('.customer_notes_hover_html').clone();
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
function toggle_ticket_legend(display) {
	if(display == 'show') {
		$('.dispatch-status-legend').show();
	} else {
		$('.dispatch-status-legend').hide();
	}
}