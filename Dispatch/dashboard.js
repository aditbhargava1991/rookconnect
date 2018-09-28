result_list = '';
$(document).ready(function() {
	retrieve_tickets();
	$(window).resize(function() {
		resize_blocks();
	}).resize();
	$('.search-fields').find('input,select').change(function() { search_filters(this) });
});
function resize_blocks() {
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
			block_height = $(this).height();
		}
	});
	if(block_height < ($('.main-screen.standard-body').height() - height_diff)) {
		block_height = 'calc('+$('.main-screen.standard-body').height()+'px'+' - '+height_diff+'px - 2.5em - 2px)';
	}
	$('.dispatch-equipment-group').css('width', block_width);
	$('.dispatch-equipment-group').css('min-height', block_height);
	$('.double-scroller div').width($('.dispatch-equipment-list').get(0).scrollWidth);
	$('.double-scroller').off('scroll',double_scroll).scroll(double_scroll);
	$('.dispatch-equipment-list').off('scroll',set_double_scroll).scroll(set_double_scroll);
}
function double_scroll() {
	$('.dispatch-equipment-list').scrollLeft(this.scrollLeft).scroll();
}
function set_double_scroll() {
	$('.double-scroller').scrollLeft(this.scrollLeft);
}
function retrieve_tickets() {
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
function search_filters(field) {
	if(field.name == 'search_date') {
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