<script type="text/javascript">
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
$(function() {
    $( ".connectedChecklist" ).sortable({
		beforeStop: function(e, ui) { ui.helper.removeClass('popped-field'); },
		connectWith: ".connectedChecklist",
		delay: 500,
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		start: function(e, ui) {ui.helper.addClass('popped-field'); },
		update: function( event, ui ) {
			var checklistnameid = ui.item.attr("id"); //Done
			var after_checklistnameid = ui.item.prev().attr('id');

			var table_class = ui.item.parent().attr("class");
			var status = table_class.split(' ');

			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "../Checklist/checklist_ajax.php?fill=checklist_priority&checklistnameid="+checklistnameid+"&after_checklistnameid="+after_checklistnameid,
				dataType: "html",   //expect html to be returned
				success: function(response){
				}
			});
		}
    });

});

function changeEndAme(sel) {
	$(this).focus();

	$(this).prop("disabled",false);
	var stage = sel.value;
	var typeId = sel.id;

	var checklist = typeId.split(' ');
	var checklistid = checklist[1];

	var stage = stage.replace(" ", "FFMSPACE");
	var stage = stage.replace("&", "FFMEND");
	var stage = stage.replace("#", "FFMHASH");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Checklist/checklist_ajax.php?fill=add_checklist&checklistid="+checklistid+"&checklist="+stage,
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
		url: "../Tasks_Updated/task_ajax_all.php?fill=trellotable&contactid="+contactide+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function checklistChange(sel) {
	var stage = sel.value;
    if($(sel).is(':checked')){
        var checked = 1;
    } else {
        var checked = 0;
    }
    $.ajax({
        type: "GET",
        url: "../Checklist/checklist_ajax.php?fill=checklist&checklistid="+stage+"&checked="+checked,
        dataType: "html",
        success: function(response){
            location.reload();
        }
    });
}
function choose_user(target, type, id, date) {
	var title	= 'Choose a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({type: type, id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to '+(target == 'alert' ? 'enable alerts for' : 'send the '+target+' to')+' the selected user(s)?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?fill=checklistalert',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?fill=checklistemail',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?fill=checklistreminder',
						data: { id: id, type: type, schedule: date, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				$(this).closest('body').find('select').val('');
				$('.close_iframer').click();
			}
			else if($(this).closest('body').find('select').val() == '') {
				$('.close_iframer').click();
			}
		});
	});
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target+'&id='+id+'&type='+type+'&multiple=true');
	$('.iframe_title').text(title);
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
}
function send_alert(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	choose_user('alert', type, checklist_id);
}
function send_email(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	choose_user('email', type, checklist_id);
}
function send_reminder(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	var name_id = (type == 'checklist board' ? 'board_' : '');
	$('[name=reminder_'+name_id+checklist_id+']').show().focus();
	$('[name=reminder_'+name_id+checklist_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reminder_'+name_id+checklist_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', type, checklist_id, date);
		}
	});
}
function send_reply(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	$('[name=reply_'+checklist_id+']').show().focus();
	$('[name=reply_'+checklist_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+checklist_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: '../Checklist/checklist_ajax.php?fill=checklistreply',
				data: { id: checklist_id, reply: save_reply },
				complete: function(result) { window.location.reload(); }
			})
		}
	});
}
function edit_item(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	$('[name=edit_'+checklist_id+']').show().focus();
	$('[name=edit_'+checklist_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=edit_'+checklist_id+']').blur(function() {
		$(this).hide();
		var line = $(this).val().trim();
		$.ajax({
			method: 'POST',
			url: '../Checklist/checklist_ajax.php?fill=checklistedit',
			data: { id: checklist_id, checklist: line },
			complete: function(result) { window.location.reload(); }
		})
	});
}
function add_time(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	$('[name=checklist_time_'+checklist_id+']').show();
	$('[name=checklist_time_'+checklist_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$('[name=checklist_time_'+checklist_id+']').hide();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: '../Checklist/checklist_ajax.php?fill=checklist_quick_time',
				data: { id: checklist_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=checklist_time_'+checklist_id+']').timepicker('show');
}
function attach_file(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist_board';
		checklist_id = checklist_id.substring(5);
	}
	var file_id = 'attach_'+(type == 'checklist' ? '' : 'board_')+checklist_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "../Checklist/checklist_ajax.php?fill=checklist_upload&type="+type+"&id="+checklist_id,
			data: fileData,
			complete: function(result) {
				//console.log(result.responseText);
				window.location.reload();
			}
		});
	});
	$('[name='+file_id+']').click();
}
function flag_item(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist_board';
		checklist_id = checklist_id.substring(5);
	}
	$.ajax({
		method: "POST",
		url: "../Checklist/checklist_ajax.php?fill=checklistflag",
		data: { type: type, id: checklist_id },
		complete: function(result) {
			console.log(result.responseText);
			if(type == 'checklist') {
				$(checklist).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			} else {
				$(checklist).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			}
		}
	});
}
function archive(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	if(type == 'checklist' && confirm("Are you sure you want to archive this item?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "../Checklist/checklist_ajax.php?fill=delete_checklist&checklistid="+checklist_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				window.location.reload();
				//console.log(response.responseText);
			}
		});
	}
}
function export_pdf(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist').substring(5);
	var type = 'checklist board';
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Checklist/checklist_ajax.php?fill=export_pdf&checklistid="+checklist_id,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(response != 'NO ID') {
				window.location = response;
			}
		}
	});
}
function mark_favourite(button) {
	if(button.src.includes('filled')) {
		button.src = '<?= WEBSITE_URL ?>/img/blank_star.png';
	} else {
		button.src = '<?= WEBSITE_URL ?>/img/filled_star.png';
	}
	$.ajax({
		type: "GET",
		url: "../Checklist/checklist_ajax.php?fill=mark_favourite&checklistid="+$(button).data('id')+"&status="+button.src.includes('filled'),
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
</script>