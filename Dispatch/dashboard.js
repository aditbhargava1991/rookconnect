result_list = '';
$(document).ready(function() {
	retrieve_tickets();
	$(window).resize(function() {
		resize_blocks();
	}).resize();
});
function resize_blocks() {
	var height_diff = $('.dashboard-equipment-buttons-group').outerHeight() + $('.dashboard-equipment-summary').height() + $('.double-scroller').height();
	if($(window).width() < 768) {
		block_width = 'calc(100% - 1em)';
		block_height = 'calc('+($('.main-screen.standard-body').height())+'px'+' - '+height_diff+'px - 2em - 1px)';
		list_height = 'calc(100% - '+height_diff+'px)';
	} else {
		block_width = 'calc('+($(window).width() / 3)+'px'+' - 2em - 1px)';
		block_height = 'calc('+($('.main-screen.standard-body').height())+'px'+' - '+height_diff+'px - 2em - 1px)';
		list_height = 'calc('+($('.main-screen.standard-body').height())+'px'+' - '+height_diff+'px)';
	}
	$('.dispatch-equipment-group').css('width', block_width);
	$('.dispatch-equipment-group').css('min-height', block_height);
	$('.dispatch-equipment-list').css('height', list_height);
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
function retrieve_tickets(date = '') {
	$.ajax({
		url :'../Dispatch/dashboard_load_list.php',
		method: 'POST',
		data: { date: date },
		dataType: 'html',
		success: function(response) {
			result_list = JSON.parse(response);
			load_buttons();
			load_tickets();
			resize_blocks();
		}
	});
}
function load_buttons() {
	$('.dispatch-equipment-buttons').html('');
	result_list['buttons'].forEach(function(button) {
		$('.dispatch-equipment-buttons').append(button);
	});
	resize_blocks();
}
function load_tickets() {
	$('.dispatch-equipment-list').html('');
	result_list['equipment'].forEach(function(equipment) {
		$('.dispatch-equipment-list').append(equipment['html']);
	});
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