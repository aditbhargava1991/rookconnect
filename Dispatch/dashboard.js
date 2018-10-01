result_list = '';
$(document).ready(function() {
	// Load the Visualization API and the corechart package.
	google.charts.load('current', {'packages':['corechart']});

	set_url_with_current_date();
	retrieve_tickets();
	$(window).resize(function() {
		resize_blocks();
	}).resize();
	$('.search-fields').find('input,select').change(function() { search_filters(this) });
});
function resize_blocks() {
	$('.dispatch-equipment-group').css('min-height', 0);
	height_diff = $('.dashboard-equipment-buttons-group').outerHeight() + $('.dashboard-equipment-summary').height() + $('.double-scroller').height();
	if($(window).width() < 768) {
		block_width = 'calc(100% - 1em)';
	} else if($(window).width() < 1024) {
		block_width = 'calc('+($(window).width() / 2)+'px'+' - 2em - 2px)';
	} else {
		block_width = 'calc('+($(window).width() / 3)+'px'+' - 2em - 2px)';
	}
	block_height = $('.dispatch-equipment-group:visible').first().height();
	$('.dispatch-equipment-group:visible').each(function() {
		if($(this).height() > block_height) {
			block_height = $(this).height() + 2;
		}
	});
	if(block_height < ($('.main-screen.standard-body').height() - height_diff)) {
		block_height = 'calc('+$('.main-screen.standard-body').height()+'px'+' - '+height_diff+'px - 2.5em - 2px)';
	}
	$('.dispatch-equipment-group,.dispatch-summary-block').css('width', block_width);
	$('.dispatch-equipment-group').css('min-height', block_height);
	$('.double-scroller div').width($('.dispatch-equipment-list').get(0).scrollWidth);
	$('.double-scroller').off('scroll',double_scroll).scroll(double_scroll);
	$('.dispatch-equipment-list').off('scroll',set_double_scroll).scroll(set_double_scroll);
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
function retrieve_tickets() {
	$('.dispatch-equipment-list').html('Loading...');
	var date = $('[name="search_date"]').val()
	var active_equipment = [];
	$('.dispatch-equipment-button').each(function() {
		if($(this).hasClass('active')) {
			active_equipment.push($(this).data('equipment'));
		}
	});
	active_equipment = JSON.stringify(active_equipment);
	$.ajax({
		url :'../Dispatch/dashboard_load_list.php',
		method: 'POST',
		data: { date: date, active_equipment: active_equipment },
		dataType: 'html',
		success: function(response) {
			result_list = JSON.parse(response);
			load_buttons();
			load_tickets();
			filter_equipment();
		}
	});
}
function load_buttons() {
	$('.dispatch-equipment-buttons').html('');
	result_list['buttons'].forEach(function(button) {
		$('.dispatch-equipment-buttons').append(button);
	});

	setTimeout(function() { resize_blocks() }, 500);
}
function load_tickets() {
	$('.dispatch-equipment-list').html('');
	result_list['equipment'].forEach(function(equipment) {
		$('.dispatch-equipment-list').append(equipment['html']);
	});
	
	setTimeout(function() { resize_blocks() }, 500);
}
function view_summary(equipmentid) {
	if($('.dispatch-summary-group').is(':visible') && $('.dispatch-summary-group').data('equipment') == equipmentid) {
		$('.dispatch-equipment-summary').html('').hide();
		$('.dispatch-equipment-summary-title').html('').hide();
	} else {
		var item_row = '';

		var ontime_data = new google.visualization.DataTable();
		ontime_arr = [];
		ontime_arr.push (['On Time', 'Count', { role: 'style' }]);

		var status_data = new google.visualization.DataTable();
		status_data.addColumn('string', 'Status');
		status_data.addColumn('number', 'Count');
		status_colors = [];

		equip_label = '';
		if(equipmentid == 'ALL') {
			item_row = result_list['summary'];
			equip_label = result_list['label'];
			result_list['status_summary'].forEach(function(status) {
				status_data.addRow([status['status'], status['count']]);
				status_colors.push(status['color']);
			});
			result_list['ontime_summary'].forEach(function(ontime) {
				ontime_arr.push([ontime['label'], ontime['count'], ontime['color']]);
			});
		} else {
			item_row = $.grep(result_list['equipment'], function(row) {
				return row['equipmentid'] == equipmentid;
			});
			equip_label = item_row[0]['label'];
			item_row[0]['status_summary'].forEach(function(status) {
				status_data.addRow([status['status'], status['count']]);
				status_colors.push(status['color']);
			});
			item_row[0]['ontime_summary'].forEach(function(ontime) {
				ontime_arr.push([ontime['label'], ontime['count'], ontime['color']]);
			});
			item_row = item_row[0]['summary'];
		}
		$('.dispatch-equipment-summary').html(item_row).show();
		$('.dispatch-equipment-summary-title').html('<h4>Summary - '+equip_label+'</h4>').show();

		var ontime_data = google.visualization.arrayToDataTable(ontime_arr);
		var ontime_options = {
			title: 'On Time Summary',
			legend: { position: 'none' }
		};
		var ontime_chart = new google.visualization.ColumnChart($('.dispatch-summary-ontime')[0]);
        ontime_chart.draw(ontime_data, ontime_options);

		var status_options = {
			title: 'Status Summary',
			is3D: true,
			colors: status_colors
		};
		var status_chart = new google.visualization.PieChart($('.dispatch-summary-status')[0]);
        status_chart.draw(status_data, status_options);
	}
	resize_blocks();
}
function load_summary() {

}
function search_filters(field) {
	if(field.name == 'search_date') {
		set_url_with_current_date();
		retrieve_tickets();
	} else {
		filter_equipment();
	}
	resize_blocks();
}
function filter_equipment(a = '') {
	if(a != '') {
		var block = $(a).closest('.dispatch-equipment-button').toggleClass('active');
	}

	$('.dispatch-equipment-group').hide();
	$('.dispatch-equipment-button').each(function() {
		if($(this).hasClass('active')) {
			$('.dispatch-equipment-group[data-equipment="'+$(this).data('equipment')+'"]').show();
		}
	});

	var region = $('[name="search_region"]').val();
	var location = $('[name="search_location"]').val();
	var classification = $('[name="search_classification"]').val();
	var business = $('[name="search_business"]').val();

	var filter_query = '';
	if(region != undefined && region != '') {
		filter_query += '[data-region="'+region+'"]';
	}
	if(location != undefined && location != '') {
		filter_query += '[data-location="'+location+'"]';
	}
	if(classification != undefined && classification != '') {
		filter_query += '[data-region="'+classification+'"]';
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

	resize_blocks();
}
function select_all_buttons(a) {
	if($(a).is(':checked')) {
		$('.dispatch-equipment-button').addClass('active');
	} else {
		$('.dispatch-equipment-button').removeClass('active');
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
			$('#camera_hover').html('<img src="'+$(img).data('file')+'" style="max-width: 300px; max-height: 300px; width: auto; height: auto;">');
		}
	} else {
		hide_camera();
	}
}
function hide_camera(img) {
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
function hide_signature(img) {
	$('#signature_hover').hide().html('Loading...');
}
function view_customer_notes(url) {
	overlayIFrameSlider(url, 'auto', true, true);
	return false;
}