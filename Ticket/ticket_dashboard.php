<?php include_once('config.php');
if(get_config($dbc, 'ticket_unassigned_status') == 1) {
	$archive_query = "UPDATE tickets SET status='Unassigned' WHERE (contactid IS NULL OR contactid = '') AND `status` != 'Archive' AND `deleted` = 0";
	$delete_result = mysqli_query($dbc, $archive_query);
}

$summary_block_list = explode(',',get_config($dbc, 'summary_block_sort'));
$block_list_arr = explode('||*||',$summary_block_list[0]);

$ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
$project_types = [];
foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
	$project_types[config_safe_str($type_name)] = $type_name;
}
$_GET['tile_name'] = @filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
if($strict_view > 0) {
	$tile_security['edit'] = 0;
	$tile_security['config'] = 0;
}
$ticket_type = isset($_GET['type']) ? filter_var($_GET['type'],FILTER_SANITIZE_STRING) : filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
$db_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tickets_dashboard` FROM `field_config`"))['tickets_dashboard'];
if($db_config == '') {
	$db_config = 'Business,Contact,Heading,Services,Status,Deliverable Date';
}
$db_config = explode(',',$db_config);
$db_summary = get_config($dbc, 'ticket_summary_userid_'.$_SESSION['contactid']);
if(empty($db_summary)) {
	$db_summary = get_config($dbc, 'ticket_summary_seclevel_'.config_safe_str($_SESSION['role']));
}
if(empty($db_summary)) {
	$db_summary = get_config($dbc, 'tickets_summary');
}
$db_summary = explode(',',$db_summary);
$hide_archived = get_config($dbc, 'ticket_exclude_archive');
$db_sort = get_config($dbc, 'tickets_sort');
if($db_sort != '') {
	$db_sort = explode(',',$db_sort);
} else {
	$db_sort = $db_config;
}
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
}
$recent_manifests = get_config($dbc, 'recent_manifests'); ?>
<script>
var ajax_loads = [];
$(document).ready(function() {
	loadTickets();
    loadNote();
    <?php if($_GET['tab'] != 'manifest' && strpos($_GET['tab'], 'administration_') === FALSE && $_GET['tab'] != 'invoice') { ?>
		highlightHigherLevels();
	<?php } ?>
	$('.search_list').keyup(function() {
		if(current_ticket_search_key != this.value.toLowerCase()) {
			loadTickets();
		}
	});
	$('[data-type] a').not('.cursor-hand').click(function() {
		var tab = $(this).closest('[data-type]').data('type');
        loadNote(tab);
		$('[data-type]').not('[data-type="'+tab+'"]').find('.active.blue').removeClass('active').removeClass('blue');
		highlightHigherLevels();
	});
	$('#mobile_accordions .panel-body .panel-title,#mobile_accordions .higher_level_clickable').click(function() {
		var panel = $(this).closest('.panel').find('.panel-body');
		var data = panel.data('accordion');
		var arr = [];
		if(data == 'summary') {
			panel.html('Loading...');
		} else if(data == 'view_all') {
			var type = panel.data('type');
			ticket_list[type].forEach(function(element) {
				arr.push(element);
			});
		} else {
			ticket_list['ticket'].forEach(function(element) {
				if(data > 0 && element.status != 'Archive' && element.status != 'Archived' && element.status != 'Done' && (element.staff != undefined && element.staff.indexOf(data+'') > -1 || element.internal_qa != undefined && element.internal_qa.indexOf(data+'') > -1 || element.deliverable_id != undefined && element.deliverable_id.indexOf(data+'') > -1 || element.created_by != undefined && element.created_by.indexOf(data+'') > -1 || element.business == data || element.contact != undefined && element.contact.indexOf(data+'') > -1)) {
					arr.push(element);
				} else if(element.project != null && element.project != undefined && data == 'project_'+element.project && element.status != 'Archive' && element.status != 'Archived' && element.status != 'Done') {
					arr.push(element);
				} else if(element.status != null && element.status != undefined && data == 'status_'+element.status) {
					arr.push(element);
				} else if(element.projectid != null && element.projectid != undefined && data == 'projectid_'+element.projectid) {
					arr.push(element);
				}
			});
		}
		panel.empty();
		if(data == 'summary') {
			panel.html($('.summary_div').html());
		} else if(arr.length > 0) {
			showResults(arr, panel, ++search_option_id);
		} else {
			panel.html('<h4>No <?= $ticket_tile ?> Found</h4>');
		}
	});

    /* $('.tile-sidebar .highest-level .top-a').click(function() {
        $(this).each(function() {
            if ( $('.tile-sidebar .highest-level .top-a').data('parent') == '#accordion' ) {
                $('.tile-sidebar .highest-level .top-ul').removeClass('in');
            }
        });
    }); */

    $('.tile-sidebar .highest-level .top-a').click(function() {
        $(this).each(function() {
            if ( $(this).data('parent') == '#accordion' ) {
                $('.tile-sidebar .highest-level .top-ul').removeClass('in');
            }
        }).off('click').click(function() {
            // alert('second');
            $(this).addClass('collapsed');
            // return false;
        });
    });
});
var ticket_list = [];
var current_ticket_search_key = '';
var search_option_id = 0;
function highlightHigherLevels() {
	$('.cursor-hand').removeClass('active blue');
	$('.sidebar-higher-level.highest-level').each(function() {
		var active_li = false;
		$(this).find('.sidebar-higher-level').each(function() {
			if($(this).find('li.active').length > 0) {
				$(this).find('.cursor-hand').addClass('active blue');
				active_li = true;
			}
		});
		if(active_li) {
			$(this).find('.cursor-hand').first().addClass('active blue');
		}
	});
}
function loadNote(tab) {
    $('.ticket_note_div').hide();
    if(tab == '' || tab == undefined) {
        $('.ticket_note_div[data-type=ticket_summary]').show();
    } else {
        $('.ticket_note_div[data-type='+tab+']').show();
    }
}
function loadTickets() {
	<?php if($_GET['form_list'] > 0) {
		echo 'return;';
	} ?>
	if(ticket_list == undefined) {
		return;
	}
	loadingOverlayHide();
	clearTimeout(continue_loading);
	ajax_loads.forEach(function(call) { call.abort(); });
	$('.show-on-mob .standard-dashboard-body-content:visible').empty().html('<h4 class="col-sm-12">Enter a search term to display <?= $ticket_tile ?> here<h4>');
	var target = $('.main-content-screen .main-screen .standard-dashboard-body-content:visible');
	var result_list = [];
	var filter_list = [];
	if($('[data-type] .active.blue').length + $('[data-type].active.blue').length	> 0) {
		var staff = [];
		$('.active.blue [data-staff]').each(function() {
			if($(this).data('staff') > 0) {
				staff.push($(this).data('staff'));
			}
		});
		var creator = [];
		$('.active.blue [data-creator]').each(function() {
			if($(this).data('creator') != '') {
				creator.push($(this).data('creator'));
			}
		});
		var business = [];
		$('.active.blue [data-business]').each(function() {
			if($(this).data('business') > 0) {
				business.push($(this).data('business'));
			}
		});
		var contact = [];
		$('.active.blue [data-contact]').each(function() {
			if($(this).data('contact') > 0) {
				contact.push($(this).data('contact'));
			}
		});
		var project = [];
		$('.active.blue [data-project]').each(function() {
			if($(this).data('project') != '') {
				project.push($(this).data('project'));
			}
		});
		var projectid = [];
		$('.active.blue [data-projectid]').each(function() {
			if($(this).data('projectid') > 0) {
				projectid.push($(this).data('projectid'));
			}
		});
		var po = [];
		$('.active.blue [data-po]').each(function() {
			if($(this).data('po') != '') {
				po.push($(this).data('po')+'');
			}
		});
		var status = [];
		$('.active.blue [data-status]').each(function() {
			if($(this).data('status') != '') {
				status.push($(this).data('status'));
			}
		});
		if(ticket_list[$('.active.blue').closest('[data-type]').first().data('type')] != undefined) {
			var i = 0;
			ticket_list[$('.active.blue').closest('[data-type]').first().data('type')].forEach(function(ticket) {
				if((business.indexOf(ticket.business * 1) >= 0 || business.length == 0) &&
					(ticket.contact.some(function(element) { return this.indexOf(element * 1) >= 0; }, contact) || contact.length == 0) &&
					((ticket.staff.some(function(element) { return this.indexOf(element * 1) >= 0; }, staff) && ticket.status != 'Internal QA' && ticket.status != 'Customer QA') || (ticket.internal_qa.some(function(element) { return this.indexOf(element * 1) >= 0; }, staff) && ticket.status == 'Internal QA') || (ticket.deliverable_id.some(function(element) { return this.indexOf(element * 1) >= 0; }, staff) && ticket.status == 'Customer QA') || staff.length == 0) &&
					(creator.indexOf(ticket.created_by * 1) >= 0 || creator.length == 0) &&
					(po.indexOf(ticket.po) >= 0 || po.length == 0) &&
					(project.indexOf(ticket.project) >= 0 || project.length == 0) &&
					(projectid.indexOf(ticket.projectid * 1) >= 0 || projectid.length == 0) &&
					(status.indexOf(ticket.status) >= 0 || (status.length == 0 && ticket.status != 'Done' && ticket.status != 'Archive') || status.indexOf('ALL_STATUS') >= 0) &&
					($('.active.blue').closest('[data-type]').first().data('limit') == undefined || $('.active.blue').closest('[data-type]').first().data('limit') > i)) {
					filter_list.push(ticket);
					i++;
				}
			});
		} else {
			loadingOverlayShow('.main-content-screen');
		}
		var key = $('.search_list:visible').val();
		if(key != undefined) {
			key = key.toLowerCase();
		}
		current_ticket_search_key = key;
		filter_list.forEach(function(ticket) {
			if(key == '' || key == undefined || ticket.key.toLowerCase().search(key) >= 0) {
				result_list.push(ticket);
			}
		});
	} else if($('.search_list:visible').val() != '') {
		var key = $('.search_list:visible').val();
		if(key != undefined) {
			key = key.toLowerCase();
		}
		current_ticket_search_key = key;
		if(ticket_list['ticket'] == undefined) {
			return;
		}
		ticket_list['ticket'].forEach(function(ticket) {
			if(key == '' || key == undefined || ticket.key.toLowerCase().search(key) >= 0) {
				result_list.push(ticket);
			}
		});
	} else {
		current_ticket_search_key = '';
		result_list = [];
	}
	if($('[data-type] .active.blue').length + $('[data-type].active.blue').length > 0 || current_ticket_search_key != '') {
		$('.summary_tab').removeClass('active').removeClass('blue');
		var type = $('[data-type] .active.blue,[data-type].active.blue').first().closest('[data-type]');
		var title = type.find('a').first().text();
		var title_subtext = '';
		if(current_ticket_search_key != '') {
			title_subtext += (type.length > 0 ? ': ' : '')+'Search Results for '+current_ticket_search_key+' ('+result_list.length+' results)';
		}
		$('li.active.blue:not(.highest-level) a').each(function() {
			var cat = $(this).closest('ul').prevAll('a').first().text();
			var item = $(this).clone();
			item.find('span').remove();
			title_subtext += (cat != '' ? ': <small><b>'+cat+' - ' : '<small><b>')+item.text()+'</b></small>';
		});
		var link = '?<?= $current_tile ?>type='+(type.length > 0 ? type.data('type').substr(7) : '')+'&edit=0';
		if(type.is('[data-form]')) {
			link = '?<?= $current_tile ?>custom_form='+type.data('type').substr(5);
		}

		if(current_ticket_search_key != '') {
			$('.main-content-screen .main-screen .standard-dashboard-body-title h3').html(title+title_subtext);
		} else {
			$('.main-content-screen .main-screen .standard-dashboard-body-title h3').html(title+title_subtext);
			<?php if($tile_security['edit'] > 0) { ?>
				$('.main-content-screen .main-screen .standard-dashboard-body-title h3').append('<a class="btn brand-btn pull-right" href="'+link+'">New '+title+'</a>');
			<?php } ?>
		}

		target.html('');
		var arr = [];
		result_list.forEach(function(element) {
			if(element.id == current_ticket_search_key && arr.indexOf(element) === -1) {
				arr.unshift(element);
			} else if(arr.indexOf(element) === -1) {
				arr.push(element);
			}
		});
		showResults(arr, target, ++search_option_id);
	} else if($('.hide-titles-mob .standard-dashboard-body-content').is(':visible')) {
		<?php if(!in_array('Disable',$db_summary) && empty($_GET['tab'])) { ?>
			$('.main-content-screen .main-screen .standard-dashboard-body-title h3').text('Summary');
			$('.summary_tab').addClass('active blue');
			target.html($('.summary_div').html());

			$('.sort-block-group').sortable({
				//connectWith: '.block-group',
				handle: '.drag_handle',
				items: '.overview-block',
				update: function(event, ui) {
					var id_list = [];
					$('.block-group [name=id]').each(function() { if(id_list.indexOf(this.value)>-1){}else{id_list.push(this.value);} });
					$.ajax({
						url: '../ajax_all.php?action=summary_block_sort',
						method: 'POST',
						data: {
							template_id: id_list
						}
					});
				}
			});

			/*$('.sort_div1').sortable({
				connectWith: '.sort_div',
				handle: '.drag_handle',
				items: '.overview-block',
				update: function(event, ui) {
					var id_list = [];
					$('.block-group [name=id]').each(function() { id_list.push(this.value); });
					alert(id_list);
					/*$.ajax({
						url: '../ajax_all.php?action=summary_block_sort',
						method: 'POST',
						data: {
							template_id: id_list
						}
					});
				}
			});*/
			initTooltips();
		<?php } else if(empty($_GET['tab'])) { ?>
			$('.main-content-screen .main-screen .standard-dashboard-body-title h3').text('All <?= $ticket_tile ?>');
			var arr = [];
			if(ticket_list['ticket'] != undefined) {
				ticket_list['ticket'].forEach(function(element) { arr.push(element); });
			} else {
				loadingOverlayShow('.main-content-screen');
			}
			showResults(arr, target, ++search_option_id);
		<?php } ?>
	} else {
		$('#mobile_accordions').show();
	}
}
var continue_loading = '';
function showResults(result_list, target, search_id) {
	clearTimeout(continue_loading);
	if(search_id != search_option_id) {
		return;
	} else if($('.dashboard-item').length == 0 || $('.dashboard-item').last().offset().top - $(window).scrollTop() < $(window).innerHeight() + 500) {
		var ticket = result_list.shift();
		var type = $('.active.blue').closest('[data-type]');
		if(ticket != undefined && ticket.id > 0 && (!(type.length > 0) || type.data('type').substr(0,4) != 'form')) {
			if(target.attr('class') == 'panel-body') {
				$('#mobile_accordions').show();
			} else {
				$('#mobile_accordions').hide();
			}
			ajax_loads.push($.ajax({
				url: 'ticket_load.php?<?= $current_tile ?>ticketid='+ticket.id+'&tile=<?= $_GET['tile_name'] ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>',
				success: function(response) {
					if(search_id == search_option_id) {
						target.append(response);
						continue_loading = showResults(result_list, target, search_id);
						setActions();
						initTooltips();
					}
				}
			}));
		} else if(ticket != undefined && ticket.id > 0) {
			target.append('<div class="dashboard-item form-horizontal">'+
					'<h3><a target="_blank" href="'+(ticket.file != '' ? ticket.file : '../Ticket/download/'+$('.active.blue').closest('[data-type]').data('form')+'_'+ticket.revision+'_'+ticket.id+'.pdf')+'">'+ticket.label+'</a>'+
					<?php if($tile_security['edit'] > 0) { ?>'<div class="pull-right"><a href="?<?= $current_tile ?>custom_form='+$('.active.blue').closest('[data-type]').data('type').substr(5)+'&revision='+ticket.revision+'&ticketid='+ticket.id+'&pdf_mode=edit&revision_mode=edit" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a><a href="?<?= $current_tile ?>custom_form='+$('.active.blue').closest('[data-type]').data('type').substr(5)+'&revision='+ticket.revision+'&ticketid='+ticket.id+'&pdf_mode=edit&revision_mode=new" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a><a href="" onclick="overlayIFrameSlider(\'<?= WEBSITE_URL ?>/Ticket/ticket_pdf_revisions.php?<?= $current_tile ?>form='+$('.active.blue').closest('[data-type]').data('type').substr(5)+'&ticketid='+ticket.id+'\', \'auto\', false, true); return false;" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/eyeball.png" class="inline-img theme-color-icon no-toggle" title="View Revisions"></a>'+
						'<?= ($tile_security['config'] > 0 ? '<a href="" onclick="remForm(\'+$(\'.active.blue\').closest(\'[data-type]\').data(\'type\').substr(5)+\',\'+ticket.id+\',\'+ticket.revision+\',$(this).closest(\\\'.dashboard-item\\\')); return false;" class="small pad-10"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>' : '') ?></div><div class="clearfix"></div>'+
					<?php } else { ?>
						''+
					<?php } ?>'</h3></div>');
			continue_loading = showResults(result_list, target, search_id);
			initTooltips();
		}
	} else if(result_list.length > 0) {
		continue_loading = setTimeout(function() { showResults(result_list, target, search_id); }, 1000);
	}
}
function remForm(form, ticket, rev, div) {
	if(confirm('Are you sure you want to remove this form?')) {
		$(div).remove();
		$.post('ticket_ajax_all.php?action=removePdfForm',{formid:form,ticket:ticket,revision:rev});
	}
}
function setActions() {
	$('.archive-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: 'ticket_ajax_all.php?action=archive',
			method: 'POST',
			data: { ticketid: item.data('id') }
		});
		item.hide();
	});
	$('.manual-flag-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
	});
	$('.flag-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: 'ticket_ajax_all.php?action=quick_actions',
			method: 'POST',
			data: {
				field: 'flag_colour',
				value: item.data('colour'),
				table: item.data('table'),
				id: item.data('id'),
				id_field: item.data('id-field')
			},
			success: function(response) {
				item.data('colour',response.substr(0,6));
				item.css('background-color','#'+response.substr(0,6));
				item.find('.flag-label').html(response.substr(6));
			}
		});
	});
	$('.attach-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table','ticket_document');
			fileData.append('folder','download');
			fileData.append('id',item.data('id'));
			fileData.append('id_field','ticketid');
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "ticket_ajax_all.php?action=quick_actions",
				data: fileData
			});
		}).click();
	});
	$('.reply-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
	});
	$('.reminder-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
	});
	$('.email-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
	});
	$('.alert-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		var select = item.find('.select_users');
		$(this).data('users').split(',').forEach(function(user) {
			if(user > 0) {
				select.find('option[value='+user+']').attr('selected',true);
			}
		});
		select.find('.cancel_button').off('click').click(function() {
			select.find('option:selected').removeAttr('selected');
			select.find('select').trigger('change.select2');
			select.hide();
			return false;
		});
		select.find('.submit_button').off('click').click(function() {
			if(select.find('select').val() != '' && confirm('Are you sure you want to activate alerts for the selected user(s)?')) {
				var users = [];
				select.find('select option:selected').each(function() {
					users.push(this.value);
					$(this).removeAttr('selected');
				});
				$.ajax({
					method: 'POST',
					url: 'ticket_ajax_all.php?action=quick_actions',
					data: {
						id: item.data('id'),
						id_field: item.data('id-field'),
						table: item.data('table'),
						field: 'alert',
						value: users
					},
					success: function(result) {
						select.hide();
						item.find('h4').append(result);
					}
				});
			}
			return false;
		});
		select.find('select').trigger('change.select2');
		select.show();
	});
	$('.emailpdf-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[name=emailpdf]').off('change').off('blur').show().focus().blur(function() {
			$(this).off('blur');
			$.ajax({
				url: 'ticket_ajax_all.php?action=quick_actions',
				method: 'POST',
                data: {
                        id: item.data('id'),
                        id_field: item.data('id-field'),
                        table: item.data('table'),
                        field: 'emailpdf',
                        value: this.value,
                    },
                success: function(response) {
                            alert('PDF Sent');
                }
        		});
			$(this).hide().val('');
		}).keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			} else if(e.which == 27) {
				$(this).off('blur').hide();
			}
		});
	});
	$('.history-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/Ticket/ticket_history.php?tile=tickets&ticketid='+item.data('id'), 'auto', false, true);
	});
}
function setManualFlag(ticketid, colour, label) {
	var item = $('.dashboard-item[data-id="'+ticketid+'"]').find('.flag-label-block');
	if(colour == 'FFFFFF') {
		colour = '';
	}
	if(colour != '') {
		$(item).show();
		$(item).css('background-color', '#'+colour);
		if(label != '') {
			$(item).text('Flagged: '+label);
		} else {
			$(item).text('Flagged');
		}
	} else {
		$(item).hide();
	}
}
function setStatus(select) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Ticket/ticket_ajax_all.php?fill=update_ticket_status&ticketid="+$(select).data('id')+'&status='+select.value,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(status == 'Archive') {
				$(sel).closest('tr').hide();
			}
		}
	});
}

function setMilestoneTimeline(select) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Ticket/ticket_ajax_all.php?fill=update_ticket_mt&ticketid="+$(select).data('id')+'&mt='+select.value,
		dataType: "html",   //expect html to be returned
		success: function(response){

		}
	});
}


function setTotalBudgetTime(input) {
	$.ajax({
		type: "POST",
		url: "../Ticket/ticket_ajax_all.php?action=update_ticket_total_budget_time",
		data: { ticketid: $(input).data('id'), time: $(input).val() },
		dataType: "html",
		success: function(response){
			if(response != '') {
				$(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).show();
			} else {
				$(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).hide();
			}
		}
	});
}
</script><?php
IF(!IFRAME_PAGE) { ?>
<style>
	.li-collapsed-active-show ul.collapse{
		display: block !important;
		height:auto !important;
	}
	.li-collapsed-active-show a.collapsed + ul.collapse li{
		display: none !important;
	}
	.li-collapsed-active-show a.collapsed + ul.collapse li.active.blue{
		display: block !important;
	}
</style>
  <div id="accordion" class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible panel-group">
		<ul class="panel">
			<li class="standard-sidebar-searchbox"><input type="text" class="form-control search_list" placeholder="Search <?= $ticket_tile ?>"></li>
			<?php $active_tab = true;
			if(!in_array('Disable',$db_summary)) { ?>
				<li class="<? $_GET['tab'] != 'manifest' && strpos($_GET['tab'], 'administration_') === FALSE && $_GET['tab'] != 'invoice' ? 'active blue' : '' ?> cursor-hand summary_tab" onclick="$('.active.blue').removeClass('active').removeClass('blue'); $(this).addClass('active blue'); loadTickets(); loadNote('');">Summary</li>
				<?php $active_tab = false;
			}
			foreach($db_sort as $sort_tab) {
				if(substr($sort_tab, 0, 6) == 'Top 25') {
					$tab_type = substr($sort_tab,7);
					if(substr($tab_type, 0, 4) == 'Form') {
						$tab_type = substr($tab_type, 5);
						$label = $dbc->query("SELECT `pdf_name` FROM `ticket_pdf` WHERE `id`='$tab_type' AND IFNULL(`dashboard`,'')!='hidden' AND IFNULL(`ticket_types`,'') IN ('ALL','','".implode("','",$ticket_tabs)."')")->fetch_assoc()['pdf_name'];
						$tab_type = 'form_'.$tab_type;
					} else if($tab_type == 'All') {
						$label = $ticket_tile;
						$tab_type = 'ticket';
					} else if(in_array($tab_type,$ticket_conf_list)) {
						$label = $ticket_tabs[$tab_type];
						$tab_type = 'ticket_'.$tab_type;
					} ?>
					<li data-limit="25" data-type="<?= $tab_type ?>" class="<?= $active_tab ? 'active blue' : '' ?>"><a href="" data-top="<?= $tab_type ?>" onclick="$('.active.blue').removeClass('active blue'); $('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;">Top <?= $label ?></a></li>
					<?php $active_tab = false;
				}
			}
			$ticket_filters = ['ticket'=>(count($ticket_tabs) > 0 ? 'All ' : '').$ticket_tile];
			if($_GET['tile_name'] == '') {
				foreach($ticket_tabs as $type => $type_name) {
                    if(check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_type_'.$type)) {
                        $ticket_filters['ticket_'.$type] = $type_name;
                    }
				}
			}
			$forms = $dbc->query("SELECT `id`, `pdf_name` FROM `ticket_pdf` WHERE `deleted`=0 AND IFNULL(`dashboard`,'')!='hidden' AND IFNULL(`ticket_types`,'') IN ('ALL','','".implode("','",$ticket_tabs)."')");
			while($form = $forms->fetch_assoc()) {
				$ticket_filters['form_'.$form['id']] = $form['pdf_name'];
			}
			foreach($ticket_filters as $type => $type_name) {
                $filter = '';
                $filter_join = '';
                $file_name = '';
                $row_type = '';
                if(strpos($type,'form_') !== FALSE) {
                    $formid = substr($type, 5);
                    $pdf_info = $dbc->query("SELECT `pdf_name`, `revisions` FROM `ticket_pdf` WHERE `id`='$formid'")->fetch_assoc();
                    $file_name = config_safe_str($pdf_info['pdf_name']);
                    $filter = " AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_pdf_field_values` WHERE `pdf_type`='$formid' AND `deleted`=0)";
                    $filter_join = " LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`".($pdf_info['revisions'] > 0 ? ", `revision`" : "").") `values` ON `tickets`.`ticketid`=`values`.`ticketid` AND `values`.`pdf_type`='$formid'";
                } else if($type == 'ticket' && $_GET['tile_name'] != '') {
                    $row_type = $_GET['tile_name'];
                    $filter = " AND `tickets`.`ticket_type`='{$_GET['tile_name']}'";
                } else if($type == 'ticket') {
                    $filter = " AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."')";
                } else if($type == 'ticket_other' && $_GET['tile_name'] == '') {
                    $filter = " AND `tickets`.`ticket_type`=''";
                } else if(strpos($type,'ticket_') !== FALSE) {
                    $row_type = substr($type, 7);
                    $filter = " AND `tickets`.`ticket_type`='$row_type'";
                }
                $filter .= $match_business; ?>
                <script>
                $(document).ready(function() {
                    $.ajax({
                        url: '../Ticket/ticket_load_list.php?<?= $current_tile ?>',
                        method: 'POST',
                        data: {
                            ticket_type: '<?= $type ?>',
                            ticket_tile: '<?= $_GET['tile_name'] ?>',
                            ticket_group: '<?= $_GET['tile_group'] ?>'
                        },
                        success: function(response) {
                            response = response.split('###*###');
                            if(response[1] != '' && response[1] != undefined) {
                                console.log(response[1]);
                            }
                            ticket_list['<?= $type ?>'] = JSON.parse(response[0]);
                            loadTickets();
                        }
                    });
                });
                </script>
                <li class="sidebar-higher-level highest-level" data-type="<?= $type ?>" <?= $file_name != '' ? 'data-form="'.$file_name.'"' : '' ?>><a class="top-a cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#<?= $type ?>"><?= $type_name ?><span class="arrow" /></a>
                    <ul class="top-ul collapse" id="<?= $type ?>" style="overflow: hidden;">
                        <?php if(in_array('Staff',$db_sort) || in_array('Deliverable Date',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_staff_<?= $type ?>">Staff<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_staff_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN (SELECT `tickets`.`ticketid`, `tickets`.`contactid`, `tickets`.`internal_qa_contactid`, `tickets`.`deliverable_contactid`, GROUP_CONCAT(`item_id`) `staff_list`, `tickets`.`status`, `tickets`.`ticket_type` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` WHERE IFNULL(`ticket_attached`.`src_table`,'Staff')='Staff' AND IFNULL(`ticket_attached`.`deleted`,0)=0 AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`ticket_type`,'') GROUP BY `tickets`.`ticketid`) `tickets` ON CONCAT(',',IFNULL(`tickets`.`contactid`,''),',',IFNULL(`tickets`.`internal_qa_contactid`,''),',',IFNULL(`tickets`.`deliverable_contactid`,''),',',IFNULL(`tickets`.`staff_list`,''),',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contacts`.`deleted`=0 AND `contacts`.`status`>0 AND `tickets`.`ticketid` > 0 AND `contacts`.`first_name` != '' AND `contacts`.`last_name` != '' AND `contacts`.`contactid` > 0 $filter GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`")) as $row) { ?>
                                        <li><a href="" data-staff="<?= $row['contactid'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $row['first_name'].' '.$row['last_name'] ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Staff Create',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_creator_<?= $type ?>">Created By<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_creator_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`created_by` WHERE `tickets`.`deleted` = 0 AND `contacts`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`ticket_type`,'') AND `tickets`.`ticketid` > 0 AND (`contacts`.`first_name` != '' OR `contacts`.`last_name` != '') AND `contacts`.`contactid` > 0 $filter GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`")) as $row) { ?>
                                        <li><a href="" data-creator="<?= $row['contactid'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $row['first_name'].' '.$row['last_name'] ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Business',$db_sort) && in_array('Business',$db_config)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_business_<?= $type ?>"><?= BUSINESS_CAT ?><span class="arrow"></span></a>
                                <ul class="collapse" id="filter_business_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`businessid` AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `contacts`.`contactid`, `contacts`.`name`")) as $row) { ?>
                                        <li class="sidebar-higher-level"><!-- class="<?= $_SESSION['category'] == BUSINESS_CAT && $row['contactid'] == $_SESSION['contactid'] ? 'active blue' : '' ?>"--><a href="" data-business="<?= $row['contactid'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $row['name'] ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } else if(in_array('Business',$db_sort) && in_array('Contact',$db_config)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_contact_<?= $type ?>">Contact<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_contact_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON CONCAT(',',`tickets`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`")) as $row) { ?>
                                        <li class="sidebar-higher-level"><!--class="<?= !in_array($_SESSION['category'],['Staff',BUSINESS_CAT]) && $row['contactid'] == $_SESSION['contactid'] ? 'active blue' : '' ?>"--><a href="" data-contact="<?= $row['contactid'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $row['first_name'].' '.$row['last_name'] ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Project',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_project_type_<?= $type ?>"><?= PROJECT_NOUN ?> Tabs<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_project_type_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach($project_types as $cat_tab_value => $cat_tab) {
                                        $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tickets` WHERE `projectid` IN (SELECT `projectid` FROM `project` WHERE `deleted`=0 AND `projecttype`='$cat_tab_value') AND `deleted`=0 AND `status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")); ?>
                                        <li class="sidebar-higher-level"><a href="" data-project="<?= $cat_tab_value ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $cat_tab ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Project ID',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_project_<?= $type ?>"><?= PROJECT_TILE ?><span class="arrow"></span></a>
                                <ul class="collapse" id="filter_project_<?= $type ?>" style="overflow: hidden;">
                                    <?php $project_list = $dbc->query("SELECT `tickets`.`projectid`, COUNT(*) `count` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`projectid` > 0 AND `tickets`.`deleted` = 0 AND `project`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `tickets`.`projectid`");
                                    while($project_item = $project_list->fetch_assoc()) { ?>
                                        <li class="sidebar-higher-level"><a href="" data-projectid="<?= $project_item['projectid'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= get_project_label($dbc, $dbc->query("SELECT * FROM `project` WHERE `projectid` = '".$project_item['projectid']."'")->fetch_assoc()) ?><span class="pull-right"><?= $project_item['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Purchase Order',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_po_<?= $type ?>">Purchase Orders<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_po_<?= $type ?>" style="overflow: hidden;">
                                    <?php $po_list = $dbc->query("SELECT `purchase_order`, COUNT(*) `count` FROM `tickets` WHERE `deleted`=0 AND `status` != 'Archive' AND IFNULL(`purchase_order`,'') != '' AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter GROUP BY `purchase_order`");
                                    while($po_item = $po_list->fetch_assoc()) { ?>
                                        <li class="sidebar-higher-level"><a href="" data-po="<?= $po_item['purchase_order'] ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $po_item['purchase_order'] ?: 'No Purchase Order' ?><span class="pull-right"><?= $po_item['count'] ?></span></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('Status',$db_sort)) { ?>
                            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_status_<?= $type ?>">Status<span class="arrow"></span></a>
                                <ul class="collapse" id="filter_status_<?= $type ?>" style="overflow: hidden;">
                                    <?php foreach ($ticket_status_list as $cat_tab) {
                                        if($hide_archived != 'true' || $cat_tab != 'Archive') {
                                            $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tickets` WHERE `deleted`=0 AND `status`='$cat_tab' AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")); ?>
                                            <li class="sidebar-higher-level"><a href="" data-status="<?= $cat_tab ?>" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;"><?= $cat_tab ?><span class="pull-right"><?= $row['count'] ?></span></a></li>
                                        <?php }
                                    } ?>
                                    <?php
                                    /*
                                    $row1 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count1` FROM `tickets` WHERE `deleted`=0 AND (contactid IS NULL OR contactid = '' OR contactid <= 0)"));
                                    */
                                    ?>
                                    <!--
                                    <li class="sidebar-higher-level"><a href="" data-status="Unassigned" onclick="$('.search_list').val(''); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;">Unassigned<span class="pull-right"><?= $row1['count1'] ?></span></a></li>
                                    -->
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(in_array('ALL',$db_sort)) {
                            if(strpos($type,'form_') !== FALSE) {
                                $count = $dbc->query("SELECT COUNT(*) `count` FROM `tickets` LEFT JOIN `ticket_pdf_field_values` ON `tickets`.`ticketid`=`ticket_pdf_field_values`.`ticketid` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `tickets`.`ticketid`=`revisions`.`ticketid` AND `ticket_pdf_field_values`.`pdf_type`=`revisions`.`pdf_type` WHERE `tickets`.`deleted`=0  AND `ticket_pdf_field_values`.`pdf_type`='".substr($type,5)."' AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `ticket_pdf_field_values`.`deleted`=0 GROUP BY `tickets`.`ticketid`")->fetch_assoc();
                            } else {
                                $count = $dbc->query("SELECT COUNT(*) `count` FROM `tickets` $filter_join WHERE `deleted`=0 AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")->fetch_assoc();
                            } ?>
                            <li class="sidebar-higher-level"><a href="" data-status="ALL_STATUS" onclick="$('.search_list').val(''); $(this).closest('ul').find('.active.blue').removeClass('active').removeClass('blue'); $(this).closest('li').toggleClass('active blue'); loadTickets(); return false;">View All<span class="pull-right"><?= $count['count'] ?></span></a></li>
                        <?php } ?>
                    </ul>
                </li>
			<?php } ?>

			<?php if(in_array('Administration',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'administration')) {
				include_once('../Project/project_administration_functions.php');
				$admin_groups = $dbc->query("SELECT `id`, `name`,`region`,`classification`,`location`,`customer` FROM `field_config_project_admin` WHERE CONCAT(',',`contactid`,',') LIKE '%".$_SESSION['contactid']."%' AND `deleted`=0");
				if($admin_groups->num_rows > 0) { ?>
					<li class="sidebar-higher-level highest-level"><a class="top-a cursor-hand <?= substr($_GET['tab'],0,14) == 'administration' ? 'active blue' : 'collapsed' ?>" data-parent="#accordion" data-toggle="collapse" data-target="#tab_admin">Administration<span class="arrow"></span></a>
						<ul id="tab_admin" class="top-ul collapse <?= substr($_GET['tab'],0,14) == 'administration' ? 'in' : '' ?>">
							<?php while($admin_group = $admin_groups->fetch_assoc()) {
								$other_groups = $dbc->query("SELECT GROUP_CONCAT(`region` SEPARATOR ''',''') `regions`, GROUP_CONCAT(`classification` SEPARATOR ''',''') `classifications` FROM `field_config_project_admin` WHERE `id`!='{$admin_group['id']}' AND `deleted`=0")->fetch_assoc(); ?>
								<a href="?tab=administration_<?= $admin_group['id'] ?>_summary"><h4><?= $admin_group['name'].
									($admin_group['region'] != '' ? '<br /><em><small>'.$admin_group['region'].'</small></em>' : '').
									($admin_group['classification'] != '' ? '<br /><em><small>'.$admin_group['classification'].'</small></em>' : '').
									($admin_group['location'] != '' ? '<br /><em><small>'.$admin_group['location'].'</small></em>' : '').
									($admin_group['customer'] > 0 ? '<br /><em><small>'.get_contact($dbc,$admin_group['customer'],'full_name').'</small></em>' : '') ?></h4></a>
								<?php $admin_regions = $admin_classes = [''];
								if($admin_group['region'] == '') {
									$admin_regions = mysqli_fetch_all($dbc->query("SELECT IFNULL(`region`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['regions'] != "','" && $other_groups['regions'] != "" ? " AND ((`region` IN ('{$admin_group['region']}','') AND `region` NOT IN ('{$other_groups['regions']}')) OR ('{$admin_group['region']}'='' AND `region` NOT IN ('{$other_groups['regions']}')))" : "")." GROUP BY IFNULL(`region`,'')"));
								}
								if(empty($admin_regions)) {
									$admin_regions = [''];
								}
								if($admin_group['classification'] == '') {
									$admin_classes = mysqli_fetch_all($dbc->query("SELECT IFNULL(`classification`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['classifications'] != "','" && $other_groups['classifications'] != "" ? " AND (`classification` IN ('{$admin_group['classification']}','') OR ('{$admin_group['classification']}'='' AND `classification` NOT IN ('{$other_groups['classifications']}')))" : "")." GROUP BY IFNULL(`classification`,'')"));
								}
								if(empty($admin_classes)) {
									$admin_classes = [''];
								}
								foreach($admin_regions as $region_i => $admin_region) {
									foreach($admin_classes as $class_i => $admin_class) { ?>
										<?php if($admin_region[0].$admin_class[0] != '') { ?>
											<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && strpos($_GET['tab'], '_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0]))) !== FALSE ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_<?= $region_i ?>_<?= $class_i ?>"><?= $admin_region[0] != '' ? 'Region: '.$admin_region[0] : '' ?><?= $admin_region[0] != '' && $admin_class[0] != '' ? '<br />' : '' ?><?= $admin_class[0] != '' ? 'Classification: '.$admin_class[0] : '' ?><span class="arrow"></span></a>
												<ul id="tab_admin_<?= $admin_group['id'] ?>_<?= $region_i ?>_<?= $class_i ?>" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && strpos($_GET['tab'], '_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0]))) !== FALSE ? 'in' : '' ?>">
										<?php } ?>
										<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_pending_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])), 0, $ticket_conf_list)->num_rows; ?>
										<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_pending_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_pending_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">Pending<span class="pull-right"><?= $ticket_count ?></span></a></li>
										<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_approved_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])), 0, $ticket_conf_list)->num_rows; ?>
										<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_approved_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_approved_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">Approved<span class="pull-right"><?= $ticket_count ?></span></a></li>
										<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_revision_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])), 0, $ticket_conf_list)->num_rows; ?>
										<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_revision_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_revision_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">In Revision<span class="pull-right"><?= $ticket_count ?></span></a></li>
										<?php if($admin_region[0].$admin_class[0] != '') { ?>
											</ul></li>
										<?php } ?>
									<?php }
								} ?>
								<?php if(in_array('Administration Sort Sites',$db_config)) { ?>
									<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && !empty($_GET['filter_site']) ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_sites">Sites<span class="arrow"></span></a>
										<ul id="tab_admin_<?= $admin_group['id'] ?>_sites" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && !empty($_GET['filter_site']) ? 'in' : '' ?>">
											<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT CONCAT(IFNULL(`contacts`.`site_name`,''),IF(IFNULL(`contacts`.`site_name`,'') != '' AND IFNULL(`contacts`.`display_name`,'') != '',': ',''),IFNULL(`contacts`.`display_name`,'')) display_name, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`siteid` AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') GROUP BY `contacts`.`contactid`, `contacts`.`name`")) as $row) { ?>
												<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && $_GET['filter_site'] == $row['contactid'] ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_sites_<?= $row['contactid'] ?>"><?= $row['display_name'] ?><span class="arrow"></span></a>
													<ul id="tab_admin_<?= $admin_group['id'] ?>_sites_<?= $row['contactid'] ?>" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && $_GET['filter_site'] == $row['contactid'] ? 'in' : '' ?>">
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_pending___'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_pending___'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_pending___<?= $row['contactid'] ?>&filter_site=<?= $row['contactid'] ?>">Pending<span class="pull-right"><?= $ticket_count ?></span></a></li>
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_approved___'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_approved___'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_approved___<?= $row['contactid'] ?>&filter_site=<?= $row['contactid'] ?>">Approved<span class="pull-right"><?= $ticket_count ?></span></a></li>
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_revision___'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_revision___'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_revision___<?= $row['contactid'] ?>&filter_site=<?= $row['contactid'] ?>">In Revision<span class="pull-right"><?= $ticket_count ?></span></a></li>

													</ul>
												</li>
											<?php } ?>
										</ul>
									</li>
								<?php } ?>
								<?php if(in_array('Administration Sort Business',$db_config)) { ?>
									<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && !empty($_GET['filter_business']) ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_business">Business<span class="arrow"></span></a>
										<ul id="tab_admin_<?= $admin_group['id'] ?>_business" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && !empty($_GET['filter_business']) ? 'in' : '' ?>">
											<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`businessid` AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') GROUP BY `contacts`.`contactid`, `contacts`.`name`")) as $row) { ?>
												<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && $_GET['filter_business'] == $row['contactid'] ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_business_<?= $row['contactid'] ?>"><?= $row['name'] ?><span class="arrow"></span></a>
													<ul id="tab_admin_<?= $admin_group['id'] ?>_business_<?= $row['contactid'] ?>" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && $_GET['filter_business'] == $row['contactid'] ? 'in' : '' ?>">
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_pending____'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_pending____'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_pending____<?= $row['contactid'] ?>&filter_business=<?= $row['contactid'] ?>">Pending<span class="pull-right"><?= $ticket_count ?></span></a></li>
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_approved____'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_approved____'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_approved____<?= $row['contactid'] ?>&filter_business=<?= $row['contactid'] ?>">Approved<span class="pull-right"><?= $ticket_count ?></span></a></li>
														<?php $ticket_count = get_administration_tickets($dbc, 'administration_'.$admin_group['id'].'_revision____'.$row['contactid'], 0, $ticket_conf_list)->num_rows; ?>
														<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_revision____'.$row['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=administration_<?= $admin_group['id'] ?>_revision____<?= $row['contactid'] ?>&filter_business=<?= $row['contactid'] ?>">In Revision<span class="pull-right"><?= $ticket_count ?></span></a></li>

													</ul>
												</li>
											<?php } ?>
										</ul>
									</li>
								<?php } ?>
							<?php } ?>
						</ul>
					</li>
				<?php }
			} ?>
			<?php if(in_array('Invoicing',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'invoice') === TRUE && !($strict_view > 0)) { ?>
				<li class="sidebar-higher-level highest-level"><a class="top-a cursor-hand <?= $_GET['tab'] == 'invoice' ? 'active blue' : 'collapsed' ?>" data-parent="#accordion" data-toggle="collapse" data-target="#tab_invoice">Accounting<span class="arrow"></span></a>
					<ul id="tab_invoice" class="top-ul collapse <?= $_GET['tab'] == 'invoice' ? 'in' : '' ?>">
						<?php $inv_count = $dbc->query("SELECT SUM(IF(`invoice`.`invoiceid` IS NULL, 1, 0)) `unbilled`, SUM(IF(`invoice`.`invoiceid` IS NULL, 0, 1)) `billed` FROM `tickets` LEFT JOIN `invoice` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `tickets`.`deleted`=0 ".(in_array('Administration',$db_config) ?"AND IFNULL(`approvals`,'') != ''" : ''))->fetch_assoc(); ?>
						<li class="sidebar-lower-level <?= $_GET['tab'] == 'invoice' && $_GET['status'] == 'unbilled' ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=invoice&status=unbilled">Unbilled<span class="pull-right"><?= $inv_count['unbilled'] ?></span></a></li>
						<li class="sidebar-lower-level <?= $_GET['tab'] == 'invoice' && $_GET['status'] == 'billed' ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=invoice&status=billed">Billed<span class="pull-right"><?= $inv_count['billed'] > 25 ? 'Last 25' : $inv_count['billed'] ?></span></a></li>
					</ul>
				</li>
			<?php } ?>
			<?php if(in_array('Manifest',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'manifest') === TRUE && !($strict_view > 0)) {
				$manifest_fields = explode(',',get_config($dbc, 'ticket_manifest_fields'));
                $manifest_conf = [];
                foreach($ticket_conf_list as $ticket_type_id) {
                    $manifest_conf[] = 'type '.$ticket_type_id;
                }
                if(in_array_any($manifest_conf,$manifest_fields)) {
                    $recent_inventory = get_config($dbc, 'recent_inventory'); ?>
                    <li class="sidebar-higher-level highest-level"><a class="top-a cursor-hand <?= $_GET['tab'] == 'manifest' ? 'active blue' : 'collapsed' ?>" data-parent="#accordion" data-toggle="collapse" data-target="#tab_manifests">Manifests<span class="arrow"></span></a>
                        <ul id="tab_manifests" class="top-ul collapse <?= $_GET['tab'] == 'manifest' ? 'in' : '' ?>">
                            <li class="sidebar-lower-level <?= $_GET['tab'] == 'manifest' && $_GET['site'] == 'recent' ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=manifest&site=recent">Last <?= $recent_manifests ?> Manifests</a></li>
                            <?php if(in_array('sort_top',$manifest_fields)) { ?>
                                <li class="sidebar-lower-level <?= $_GET['tab'] == 'manifest' && $_GET['site'] == 'top_25' ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=manifest&site=top_25">Last <?= $recent_inventory ?> Line Items</a></li>
                            <?php } ?>
                            <?php $project_type_list = [''=>''];
                            if(in_array('sort_project',$manifest_fields)) {
                                $project_type_list = $project_types;
                            }
                            $ticket_filter = '';
                            if(in_array_starts('type ',$manifest_fields)) {
                                $type_filters = [];
                                foreach($manifest_fields as $config_field) {
                                    $config_field = explode(' ',$config_field);
                                    if($config_field[0] == 'type' && count($config_field) == 2) {
                                        $type_filters[] = $config_field[1];
                                    }
                                }
                                $ticket_filter = " AND `tickets`.`ticket_type` IN ('".implode("','",$type_filters)."')";
                            }
                            $filter_inv = in_array('hide qty',$manifest_fields) ? 'AND IFNULL(`inventory`.`quantity`,`ticket_attached`.`qty`-`ticket_attached`.`used`) > 0' : '';
                            foreach($project_type_list as $type_id => $type_name) {
                                if(in_array('project_type '.$type_id, $manifest_fields) || !in_array_starts('project_type ',$manifest_fields)) {
                                    if(!empty($type_name)) { ?>
                                        <li><a class="cursor-hand <?= $_GET['type'] == $type_id ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_manifests_type_<?= $type_id ?>"><?= $type_name ?><span class="arrow"></span></a>
                                            <ul id="tab_manifests_type_<?= $type_id ?>" class="collapse <?= $_GET['type'] == $type_id ? 'in' : '' ?>">
                                    <?php } ?>
                                    <?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `category`, `last_name`, `first_name`, `name`, `site_name`, `display_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category`='".SITES_CAT."' UNION SELECT 'na', 'AAA', '', '', '', 'Unassigned', ''")) as $site) {
                                        $filter_proj = in_array('sort_project',$manifest_fields) && !empty($type_id) ? "AND `tickets`.`projectid` IN (SELECT `projectid` FROM `project` WHERE `projecttype`='".$type_id."')" : '';
                                        $piece_count = $dbc->query("SELECT COUNT(DISTINCT CONCAT(".(in_array('group pieces po',$manifest_fields) && $site['site_name'] != 'Unassigned' ? "IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`)," : "").(in_array('group pieces',$manifest_fields) ? "`tickets`.`ticketid`" : "`ticket_attached`.`id`").")) numrows FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` AND `ticket_attached`.`src_table`='inventory' LEFT JOIN `ticket_attached` `piece` ON `ticket_attached`.`line_id`=`piece`.`id` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`type`='origin' WHERE `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND `ticket_attached`.`src_table` IN ('inventory','inventory_general') AND CONCAT(',',IF(`ticket_attached`.`siteid` IN ('0','',',,') OR `ticket_attached`.`siteid` IS NULL,IF(`piece`.`siteid` IN ('0','',',,') OR `piece`.`siteid` IS NULL,IF(`tickets`.`siteid` IN ('0','',',,') OR `tickets`.`siteid` IS NULL, 'na',`tickets`.`siteid`),`piece`.`siteid`),`ticket_attached`.`siteid`),',top_25,') LIKE '%,".$site['contactid'].",%' $filter_inv $ticket_filter $filter_proj")->fetch_assoc(); ?>
                                        <li class="sidebar-lower-level <?= $_GET['tab'] == 'manifest' && ($_GET['type'] == $type_id || empty($type_name)) && $_GET['site'] == $site['contactid'] ? 'active blue' : '' ?>"><a href="?<?= $current_tile ?>tab=manifest&site=<?= $site['contactid'] ?>&type=<?= $type_id ?>"><?= $site['full_name'] ?><span class="pull-right"><?= $piece_count['numrows'] ?></span></a></li>
                                    <?php }
                                    if(!empty($type_name)) { ?>
                                            </ul>
                                        </li>
                                    <?php }
                                }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>
			<?php } ?>
			<?php if(in_array('Export',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'export') === TRUE && !($strict_view > 0)) { ?>
				<a href="?<?= $current_tile ?>tab=export"><li class="<?= $_GET['tab'] == 'export' ? 'active blue' : '' ?>">Import / Export</li></a>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
<div class="main-content-screen scale-to-fill has-main-screen <?= IFRAME_PAGE ? '' : 'hide-titles-mob' ?>" style="<?= IFRAME_PAGE ? 'height:auto;' : '' ?>">
	<div class="loading_overlay" style="display:none;"><div class="loading_wheel"></div></div>
	<div class="main-screen standard-dashboard-body override-main-screen form-horizontal ticket_list" style="<?= IFRAME_PAGE ? 'height:auto;' : '' ?>">
		<?php if($_GET['form_list'] > 0) {
			$form = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='{$_GET['form_list']}'")->fetch_assoc();
			$form['file_name'] = config_safe_str($form['pdf_name']);
		} ?>
		<div class="standard-<?= substr($_GET['tab'],0,14) == 'administration' || $_GET['tab'] == 'invoice' ? '' : 'dashboard-' ?>body-title">
			<h3><?= $ticket_tile.($_GET['form_list'] > 0 ? ': '.$form['pdf_name'] : (substr($_GET['tab'],0,14) == 'administration' ? ': Administration' : (substr($_GET['tab'],0,14) == 'invoice' ? ': Accounting - '.($_GET['status'] == 'billed' ? 'Billed' : 'Unbilled').' '.$ticket_tile : ($_GET['tab'] == 'manifest' && $_GET['site'] == 'recent' ? ': Last '.$recent_manifests.' Manifests '.(IFRAME_PAGE ? '<a href="../blank_loading_page.php" class="pull-right"><img class="inline-img" src="../img/icons/cancel.png"></a>' : '').'<a href="../Reports/report_daily_manifest_summary.php?type=operations" class="pull-right"><img class="inline-img" src="../img/icons/pie-chart.png"></a>' : ($_GET['tab'] == 'manifest' ? (IFRAME_PAGE ? '<a href="../blank_loading_page.php" class="pull-right"><img class="inline-img" src="../img/icons/cancel.png"></a>' : '').': '.($_GET['manifestid'] > 0 ? 'Edit Manifest' : 'Create Manifests').' '.($_GET['site'] > 0 ? '<a href="?tile_name='.$_GET['tile_name'].'&tab=manifest&site=recent&siteid='.$_GET['site'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>' : '').'<a href="../Reports/report_daily_manifest_summary.php?type=operations" class="pull-right"><img class="inline-img" src="../img/icons/pie-chart.png"></a>' : ''))))) ?></h3><?php
				$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='tickets_summary'"));
				if ( !empty($notes['note']) ) { ?>
					<div class="notice popover-examples ticket_note_div" data-type="ticket_summary" style="display: none;">
						<div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
						<div class="col-sm-11"><span class="notice-name">NOTE:</span>
						<?= $notes['note'] ?></div>
						<div class="clearfix"></div>
					</div><?php
				}
				 ?>
			<?php
			foreach ($tickets_tabs as $ticket_type => $tickets_tab) {
				$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='tickets_".$ticket_type."'"));
				if ( !empty($notes['note']) ) { ?>
					<div class="notice popover-examples ticket_note_div" data-type="ticket_<?= $ticket_type ?>" style="display: none;">
						<div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
						<div class="col-sm-11"><span class="notice-name">NOTE:</span>
						<?= $notes['note'] ?></div>
						<div class="clearfix"></div>
					</div><?php
				}
			} ?>
		</div>
		<div class="standard-<?= substr($_GET['tab'],0,14) == 'administration' || $_GET['tab'] == 'invoice' ? '' : 'dashboard-' ?>body-content">
			<?php if($_GET['tab'] == 'export') {
				include('ticket_import.php');
			} else if($_GET['form_list'] > 0) {
				$tickets = mysqli_query($dbc, "SELECT `project`.*, `tickets`.*, `tickets`.`status` as ticket_status FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND '{$_GET['ticket_type']}' IN ('', `tickets`.`ticket_type`) AND `ticketid` IN (SELECT `ticketid` FROM `ticket_pdf_field_values` WHERE `pdf_type`='".filter_var($_GET['form_list'],FILTER_SANITIZE_STRING)."' AND `deleted`=0) ");
				while($ticket = $tickets->fetch_assoc()) { ?>
					<div class="dashboard-item form-horizontal">
						<h3><a href="../Ticket/download/<?= $form['file_name'] ?>_<?= $ticket['ticketid'] ?>.pdf"><?= get_ticket_label($dbc, $ticket).': '.$form['pdf_name'] ?></a>
						<div class="clearfix"></div>
					</div>
				<?php }
			} else if(substr($_GET['tab'],0,14) == 'administration') { ?>
				<div class="col-sm-12">
					<?php include('../Project/project_administration.php'); ?>
				</div>
			<?php } else if($_GET['tab'] == 'invoice') { ?>
				<div class="col-sm-12 gap-top" id="no-more-tables">
					<?php include('ticket_invoice.php'); ?>
				</div>
			<?php } else if($_GET['tab'] == 'manifest' && $_GET['manifestid'] > 0) { ?>
				<div class="col-sm-12" id="no-more-tables">
					<?php include('ticket_manifest_edit.php'); ?>
				</div>
			<?php } else if($_GET['tab'] == 'manifest') { ?>
				<div class="col-sm-12" id="no-more-tables">
					<?php include('ticket_manifests.php'); ?>
				</div>
			<?php } else {
				echo "<h4>Please select a tab from the left.</h4>";
			} ?>
            <div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="main-content-screen has-main-screen double-pad-top show-on-mob" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>padding: 1em; width: 100%;">
	<div class="loading_overlay" style="display:none;"><div class="loading_wheel"></div></div>
	<input type="text" class="form-control search_list" placeholder="Search <?= $ticket_tile ?>">
	<div class="main-screen override-main-screen form-horizontal">
		<div class="standard-dashboard-body-content">
			<h4 class="col-sm-12">Enter a search term to display <?= $ticket_tile ?> here<h4>
		</div>
		<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="mobile_accordions">
			<?php $filter = '';
			$file_name = '';
			if(!in_array('Disable',$db_summary)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title higher_level_clickable">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#mobile_summary">
								Summary<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="mobile_summary" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="summary">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('ALL',$db_sort)) {
				foreach($ticket_tabs as $type => $type_name) {
					if(empty($_GET['tile_name']) || $_GET['tile_name'] == $type) {
						$type = 'ticket_'.$type;
						if(!empty($_GET['tile_name'])) {
							$type = 'ticket';
						}
						$filter = '';
						$filter_join = '';
						$file_name = '';
						$row_type = '';
						if(strpos($type,'form_') !== FALSE) {
							$formid = substr($type, 5);
							$pdf_info = $dbc->query("SELECT `pdf_name`, `revisions` FROM `ticket_pdf` WHERE `id`='$formid'")->fetch_assoc();
							$file_name = config_safe_str($pdf_info['pdf_name']);
							$filter = " AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_pdf_field_values` WHERE `pdf_type`='$formid' AND `deleted`=0)";
							$filter_join = " LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`".($pdf_info['revisions'] > 0 ? ", `revision`" : "").") `values` ON `tickets`.`ticketid`=`values`.`ticketid` AND `values`.`pdf_type`='$formid'";
						} else if($type == 'ticket' && $_GET['tile_name'] != '') {
							$row_type = $_GET['tile_name'];
							$filter = " AND `tickets`.`ticket_type`='{$_GET['tile_name']}'";
						} else if($type == 'ticket_other' && $_GET['tile_name'] == '') {
							$filter = " AND `tickets`.`ticket_type`=''";
						} else if(strpos($type,'ticket_') !== FALSE) {
							$row_type = substr($type, 7);
							$filter = " AND `tickets`.`ticket_type`='$row_type'";
						}
						$match_business = '';
						if(!empty(MATCH_CONTACTS)) {
							$match_business .= " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
						}
						$count = $dbc->query("SELECT COUNT(*) `count` FROM `tickets` $filter_join WHERE `deleted`=0 AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")->fetch_assoc(); ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_accordions" href="#mobile_view_all_<?= $type ?>">
										<?= $type_name ?> (<?= $count['count'] ?>)<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="mobile_view_all_<?= $type ?>" class="panel-collapse collapse">
								<div class="panel-body" data-type="<?= $type ?>" data-accordion="view_all">
									Loading...
								</div>
							</div>
						</div>
					<?php }
				}
			} ?>
			<?php if(in_array('Staff',$db_sort) || in_array('Deliverable Date',$db_sort)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#staff_list">
								Staff<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="staff_list" class="panel-collapse collapse">
						<div class="panel-body" id="staff_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="staff_mobile_accordions"> -->
								<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN (SELECT `tickets`.`ticketid`, `tickets`.`contactid`, `tickets`.`internal_qa_contactid`, `tickets`.`deliverable_contactid`, GROUP_CONCAT(`item_id`) `staff_list`, `tickets`.`status` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` AND `ticket_attached`.`src_table`='Staff' AND `ticket_attached`.`deleted`=0 WHERE `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`ticket_type`,'') GROUP BY `tickets`.`ticketid`) `tickets` ON CONCAT(',',IFNULL(`tickets`.`contactid`,''),',',IFNULL(`tickets`.`internal_qa_contactid`,''),',',IFNULL(`tickets`.`deliverable_contactid`,''),',',IFNULL(`tickets`.`staff_list`,''),',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contacts`.`deleted`=0 AND `contacts`.`status`>0 AND `tickets`.`ticketid` > 0 AND `contacts`.`first_name` != '' AND `contacts`.`last_name` != '' AND `contacts`.`contactid` > 0 $filter GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`")) as $row) { ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#staff_mobile_accordions" href="#staff_<?= $row['contactid'] ?>" class="double-pad-left">
													Staff: <?= $row['first_name'].' '.$row['last_name'].' ('.$row['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="staff_<?= $row['contactid'] ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="<?= $row['contactid'] ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Business',$db_sort) && in_array('Business',$db_config)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#business_list">
								<?= BUSINESS_CAT ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="business_list" class="panel-collapse collapse">
						<div class="panel-body" id="business_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="business_mobile_accordions"> -->
								<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`businessid` AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `contacts`.`contactid`, `contacts`.`name`")) as $row) { ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#business_mobile_accordions" href="#bus_<?= $row['contactid'] ?>" class="double-pad-left">
													<?= BUSINESS_CAT ?>: <?= $row['name'].' ('.$row['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="bus_<?= $row['contactid'] ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="<?= $row['contactid'] ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } else if(in_array('Business',$db_sort) && in_array('Contact',$db_config)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#contact_list">
								Contacts<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="contact_list" class="panel-collapse collapse">
						<div class="panel-body" id="contact_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="contact_mobile_accordions"> -->
								<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, COUNT(*) `count` FROM contacts LEFT JOIN `tickets` ON CONCAT(',',`tickets`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') AND `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') WHERE `tickets`.`ticketid` > 0 AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`")) as $row) { ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#contact_mobile_accordions" href="#contact_<?= $row['contactid'] ?>" class="double-pad-left">
													Contact: <?= $row['first_name'].' '.$ro['last_name'].' ('.$row['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="contact_<?= $row['contactid'] ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="<?= $row['contactid'] ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Project',$db_sort)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#project_list">
								<?= PROJECT_NOUN ?> Tabs<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="project_list" class="panel-collapse collapse">
						<div class="panel-body" id="project_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="project_mobile_accordions"> -->
								<?php foreach($project_types as $cat_tab_value => $cat_tab) {
									$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tickets` WHERE `projectid` IN (SELECT `projectid` FROM `project` WHERE `deleted`=0 AND `projecttype`='$cat_tab_value') AND `deleted`=0 AND `status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")); ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#project_mobile_accordions" href="#project_<?= $cat_tab_value ?>" class="double-pad-left">
													<?= PROJECT_NOUN ?> Tab: <?= $cat_tab.' ('.$row['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="project_<?= $cat_tab_value ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="project_<?= $cat_tab_value ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Project ID',$db_sort)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#projectid_list">
								<?= PROJECT_TILE ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="projectid_list" class="panel-collapse collapse">
						<div class="panel-body" id="projectid_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="project_mobile_accordions"> -->
								<?php $project_list = $dbc->query("SELECT `tickets`.`projectid`, COUNT(*) `count` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`projectid` > 0 AND `tickets`.`deleted` = 0 AND `project`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Done','Archive','Archived','On Hold','Pending') AND '".$_GET['tile_name']."' IN (`tickets`.`ticket_type`,'') $filter GROUP BY `tickets`.`projectid`");
								while($project_item = $project_list->fetch_assoc()) { ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#projectid_mobile_accordions" href="#projectid_<?= $project_item['projectid'] ?>" class="double-pad-left">
													<?= PROJECT_NOUN ?>: <?= get_project_label($dbc, $dbc->query("SELECT * FROM `project` WHERE `projectid` = '".$project_item['projectid']."'")->fetch_assoc()).' ('.$project_item['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="projectid_<?= $project_item['projectid'] ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="projectid_<?= $project_item['projectid'] ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Status',$db_sort)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_accordions" href="#status_list">
								Status<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="status_list" class="panel-collapse collapse">
						<div class="panel-body" id="status_mobile_accordions" style="padding: 0; margin: -1px;">
							<!-- <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="status_mobile_accordions"> -->
								<?php foreach ($ticket_status_list as $cat_tab) {
									$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tickets` WHERE `deleted`=0 AND `status`='$cat_tab' AND '".$_GET['tile_name']."' IN (`ticket_type`,'') $filter")); ?>
									<div class="panel panel-default">
										<div class="panel-heading mobile_load">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#status_mobile_accordions" href="#status_<?= config_safe_str($cat_tab) ?>" class="double-pad-left">
													Status: <?= $cat_tab.' ('.$row['count'].')' ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="status_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
											<div class="panel-body" data-accordion="status_<?= $cat_tab ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php } ?>
							<!-- </div> -->
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php if(!in_array('Disable',$db_summary)) {
	$summary_urls = get_config($dbc, 'ticket_summary_urls'); ?>
	<div class="summary_div" style="display:none;">
		<script>
		google.charts.load("current", {"packages":["corechart"]});
		</script>
		<?php
		$blocks = []; $all_summary = [];
		$total_length = 0;
		$result = array_filter(array_diff($db_summary, $block_list_arr));
		foreach($result as $r){
		    array_push($block_list_arr,$r);
		}
		$block_list_arr = array_filter($block_list_arr);
		if(!empty($block_list_arr)){
		    foreach($block_list_arr as $listarr){
		        //echo '<pre>';print_r($listarr);
		        $disp1 = $disp2 = $disp3 = $disp4 = $disp6 = $disp7 = $disp8 = $disp9 = $disp10 = $disp11 = $disp12 = $disp13 = $disp14 = $disp16 = 'none';
		        if($listarr != '0'){
    		        switch($listarr){
    		            case 'Time Graph':$disp1 = 'block'; break;
    		            case 'Estimated':$disp2 = 'block'; break;
    		            case 'Tracked':$disp3 = 'block'; break;
    		            case 'Today':$disp4 = 'block'; break;
    		            case 'Business':$disp6 = 'block'; break;
    		            case 'Project':$disp7 = 'block'; break;
    		            case 'Project ID':$disp8 = 'block'; break;
    		            case 'Contact':$disp9 = 'block'; break;
    		            case 'Status':$disp10 = 'block'; break;
    		            case 'Mine':$disp11 = 'block'; break;
    		            case 'Created':$disp12 = 'block'; break;
    		            case 'Assigned':$disp13 = 'block'; break;
    		            case 'Top 25 All':$disp14 = 'block'; break;
    		            case 'Top 25 Forms':$disp16 = 'block'; break;
    		            //default:  $disp1 = $disp2 = $disp3 = $disp4 = $disp6 = $disp7 = $disp8 = $disp9 = $disp10 = $disp11 = $disp12 = $disp13 = $disp14 = $disp16 = 'block';
    		                      //break;
    		        }
		        }else{
		            $disp1 = $disp2 = $disp3 = $disp4 = $disp6 = $disp7 = $disp8 = $disp9 = $disp10 = $disp11 = $disp12 = $disp13 = $disp14 = $disp16 = 'block';
		        }
		        if(in_array('Time Graph',$db_summary) && $disp1 == 'block') {
		            array_push($all_summary,'Time Graph');
		            $total_estimated_time = $dbc->query("SELECT SUM(TIME_TO_SEC(`time_length`)) `seconds`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time` FROM `ticket_time_list` WHERE `created_by`='".$_SESSION['contactid']."' AND ((`time_type`='Completion Estimate' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND '".date('Y-m-d')."' BETWEEN `to_do_date` AND IFNULL(`to_do_end_date`,`to_do_date`) AND `contactid` LIKE '%,".$_SESSION['contactid'].",%')) OR (`time_type`='QA Estimate' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND '".date('Y-m-d')."'=`internal_qa_date` AND `contactid` LIKE '%,".$_SESSION['contactid'].",%')))")->fetch_assoc();
		            $total_tracked_time = $dbc->query("SELECT SUM(TIME_TO_SEC(`time`)) `seconds`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time` FROM `ticket_time_list` WHERE `created_by`='".$_SESSION['contactid']."' AND `created_date` LIKE '".date('Y-m-d')."%' AND `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time` FROM `ticket_timer` WHERE `deleted` = 0 AND `created_by`='".$_SESSION['contactid']."' AND `created_date` LIKE '".date('Y-m-d')."%') `time_list`")->fetch_assoc();
		            if($total_estimated_time['seconds'] + $total_tracked_time['seconds'] > 0) {
		                if($total_tracked_time['seconds'] > $total_estimated_time['seconds']) {
		                    $total_estimated_time['seconds'] = $total_tracked_time['seconds'];
		                }
		                $percent = round($total_tracked_time['seconds'] / $total_estimated_time['seconds'] * 100,3);
		                $blocks[] = [350, '<div class="overview-block">
                    <img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png">
					<div id="time_chart" style="width: 100%; height: 350px;"></div>
                    <input type="hidden" name="id" value="Time Graph">
				</div>
				<script>
				google.charts.setOnLoadCallback(drawTimeChart);

				function drawTimeChart() {

				var data = google.visualization.arrayToDataTable([
						["My Tracked Time", "Hours"],
						["Tracked Time - '.$total_tracked_time['time'].'", '.$total_tracked_time['seconds'].'],
						["Remaining Estimated Time - '.$total_estimated_time['time'].'", '.($total_estimated_time['seconds'] - $total_tracked_time['seconds']).']
					]);

					var options = {
						title: "My Tracked Time",
						pieHole: 0.5,
						tooltip: { text: "none" },
						slices: {
							0: { color: "#00aeef" },
							1: { color: "#84C6E4" },
						}
					};

					var chart = new google.visualization.PieChart(document.getElementById("time_chart"));

					chart.draw(data, options);
				}
				</script>'];
		                $total_length += 350;
		            } else { array_push($all_summary,'Time Graph');
		                $blocks[] = [68, '<div class="overview-block">
					<h4>Today\'s Time Graph: No Time Found<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>
                    <input type="hidden" name="id" value="Time Graph">
				</div>'];
		                $total_length += 68;
		            }
		        }
		        if(in_array('Estimated',$db_summary)  && $disp2 == 'block') {
		            array_push($all_summary,'Estimated');
		            $total_estimated_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time` FROM `ticket_time_list` WHERE `created_by`='".$_SESSION['contactid']."' AND ((`time_type`='Completion Estimate' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND '".date('Y-m-d')."' BETWEEN `to_do_date` AND IFNULL(`to_do_end_date`,`to_do_date`) AND `contactid` LIKE '%,".$_SESSION['contactid'].",%')) OR (`time_type`='QA Estimate' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND '".date('Y-m-d')."'=`internal_qa_date` AND `contactid` LIKE '%,".$_SESSION['contactid'].",%')))")->fetch_assoc()['time'];
		            $blocks[] = [68, '<div class="overview-block">
				<h4>Today\'s Estimated Time: '.$total_estimated_time.'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>
                <input type="hidden" name="id" value="Estimated">
			</div>'];
		            $total_length += 68;
		        }
		        if(in_array('Tracked',$db_summary)  && $disp3 == 'block') {
		            array_push($all_summary,'Tracked');
		            $total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time` FROM `ticket_time_list` WHERE `created_by`='".$_SESSION['contactid']."' AND `created_date` LIKE '".date('Y-m-d')."%' AND `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time` FROM `ticket_timer` WHERE `deleted` = 0 AND `created_by`='".$_SESSION['contactid']."' AND `created_date` LIKE '".date('Y-m-d')."%') `time_list`")->fetch_assoc()['time'];
		            $blocks[] = [68, '<div class="overview-block">
				<h4>Today\'s Tracked Time: '.$total_tracked_time.'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>
                <input type="hidden" name="id" value="Tracked">
			</div>'];
		            $total_length += 68;
		        }
		        if(in_array('Today',$db_summary) && $disp4 == 'block') {
		            $block_length = 68;
		            $block = '<div class="overview-block">
				<h4>Today\'s '.$ticket_tile.': '.date('F jS, Y').'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>
                <input type="hidden" name="id" value="Today">';
		            $today_tickets = $dbc->query("SELECT * FROM (SELECT *, `to_do_start_time` `ticket_start_time` FROM `tickets` WHERE `deleted`=0 AND '".date('Y-m-d')."' BETWEEN `to_do_date` AND IFNULL(`to_do_end_date`,`to_do_date`) AND `contactid` LIKE '%,".$_SESSION['contactid'].",%' UNION SELECT *, `internal_qa_start_time` `ticket_start_time` FROM `tickets` WHERE `deleted`=0 AND '".date('Y-m-d')."'=`internal_qa_date` AND `contactid` LIKE '%,".$_SESSION['contactid'].",%') `tickets` WHERE `deleted`=0 AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."') ORDER BY `ticket_start_time` ASC, `ticketid` DESC");
		            while($ticket = $today_tickets->fetch_assoc()) {
		                if($summary_urls == 'slider') {
		                    $block .= '<p><a href="'.WEBSITE_URL.'/Ticket/index.php?'.$current_tile.'edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.get_ticket_label($dbc, $ticket).' - '.$ticket['status'].'</a></p>';
		                } else {
		                    $block .= '<p><a href="index.php?'.$current_tile.'edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.'/Ticket/index.php?tile_name='.$_GET['tile_name']).'">'.get_ticket_label($dbc, $ticket).' - '.$ticket['status'].'</a></p>';
		                }
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        $week_start = date('Y-m-d', strtotime('last Sunday', strtotime(date('Y-m-d'))));
		        $week_end = date('Y-m-d', strtotime('next Saturday', strtotime(date('Y-m-d'))));
		        if(date('l') == 'Sunday') {
		            $week_start = date('Y-m-d');
		        } else if(date('l') == 'Saturday') {
		            $week_end = date('Y-m-d');
		        }
		        $overviews = ['Day Overview' => ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')],
		            'Week Overview' => ['start_date' => $week_start, 'end_date' => $week_end],
		            'Month Overview' => ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')]
		        ];
		        foreach($overviews as $overview_key => $overview) {
		            array_push($all_summary,$overview);
		            $start_date = $overview['start_date'];
		            $end_date = $overview['end_date'];
		            if($listarr!='0') {$disp5 = 'none';}
		            if($listarr == $overview_key){$disp5 = 'block';}
		            if(in_array($overview_key,$db_summary) && $disp5 == 'block') {
		                $block_length = 68;
		                $block = '<div class="overview-block">
                    <input type="hidden" name="id" value="'.$overview_key.'">
					<h4>'.$overview_key.': '.($start_date == $end_date ? date('F jS, Y', strtotime($start_date)) : date('F jS, Y', strtotime($start_date)).' - '.date('F jS, Y', strtotime($end_date))).'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		                $tickets = $dbc->query("SELECT *, IF(`status` = 'Internal QA',`internal_qa_date`,IF(`status` = 'Customer QA',`deliverable_date`,`to_do_date`)) `ticket_start_date` FROM `tickets` WHERE (`to_do_date` BETWEEN '$start_date' AND '$end_date' OR `to_do_end_date` BETWEEN '$start_date' AND '$end_date' OR `internal_qa_date` BETWEEN '$start_date' AND '$end_date' OR `deliverable_date` BETWEEN '$start_date' AND '$end_date') AND `deleted` = 0 AND `status` != 'Archive' ORDER BY `ticket_start_date`");
		                while($ticket = $tickets->fetch_assoc()) {
		                    if($summary_urls == 'slider') {
		                        $block .= '<p><a href="'.WEBSITE_URL.'/Ticket/index.php?'.$current_tile.'edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.$ticket['ticket_start_date'].': '.get_ticket_label($dbc, $ticket).' - '.$ticket['status'].'</a></p>';
		                    } else {
		                        $block .= '<p><a href="index.php?'.$current_tile.'edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.'/Ticket/index.php?tile_name='.$_GET['tile_name']).'">'.$ticket['ticket_start_date'].': '.get_ticket_label($dbc, $ticket).' - '.$ticket['status'].'</a></p>';
		                    }
		                    $block_length += 17;
		                }
		                $block .= '</div>';
		                $blocks[] = [$block_length, $block];
		                $total_length += $block_length;
		            }
		        }
		        if(in_array('Business',$db_summary) && $disp6 == 'block') {
		            array_push($all_summary,'Business');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Business">
				<h4>'.$ticket_tile.' per '.BUSINESS_CAT.'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `businessid` FROM `tickets` WHERE `deleted`=0 AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `status` != 'Archive' AND `businessid` > 0 $match_business GROUP BY `businessid`");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p>'.(in_array('Business',$db_sort) ? '<a class="cursor-hand" onclick="$(\'[data-business=\\\''.$ticket['businessid'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').get_contact($dbc, $ticket['businessid'],'name').(in_array('Business',$db_sort) ? '</a>' : '').': '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Project',$db_summary) && $disp7 == 'block') {
		            array_push($all_summary,'Project');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Project">
				<h4>'.TICKET_TILE.' per '.PROJECT_NOUN.' Tab<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            foreach($project_types as $cat_tab_value => $cat_tab) {
		                $ticket = $dbc->query("SELECT COUNT(*) count FROM `tickets` WHERE `deleted`=0 AND `status` != 'Archive' AND `projectid` > 0  AND `projectid` IN (SELECT `projectid` FROM `project` WHERE `deleted` = 0 AND `projecttype` = '$cat_tab_value')")->fetch_assoc();
		                $block .= '<p>'.(in_array('Project',$db_sort) ? '<a class="cursor-hand" onclick="$(\'[data-project=\\\''.$cat_tab_value.'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').$cat_tab.(in_array('Project',$db_sort) ? '</a>' : '').': '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Project ID',$db_summary) && $disp8 == 'block') {
		            array_push($all_summary,'Project ID');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Project ID">
				<h4>'.TICKET_TILE.' per '.PROJECT_NOUN.'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $project_list = $dbc->query("SELECT `tickets`.`projectid`, COUNT(*) `count` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`projectid` > 0 AND `tickets`.`deleted` = 0 AND `project`.`deleted` = 0 AND `tickets`.`status` != 'Archive' GROUP BY `tickets`.`projectid`");
		            while($project_item = $project_list->fetch_assoc()) {
		                $block .= '<p>'.(in_array('Project ID',$db_sort) ? '<a class="cursor-hand" onclick="$(\'[data-projectid=\\\''.$project_item['projectid'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').get_project_label($dbc, $dbc->query("SELECT * FROM `project` WHERE `projectid` = '".$project_item['projectid']."'")->fetch_assoc()).(in_array('Project ID',$db_sort) ? '</a>' : '').': '.$project_item['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Contact',$db_summary) && $disp9 == 'block') {
		            array_push($all_summary,'Contact');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Contact">
				<h4>'.$ticket_tile.' per '.(get_config($dbc, 'ticket_project_contact') ?: 'Contact').'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `clientid` FROM `tickets` WHERE `deleted`=0 AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `status` != 'Archive' AND `clientid` > 0 $match_business GROUP BY `clientid`");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p>'.(in_array('Business',$db_sort) && !in_array('Business',$db_config) && in_array('Contact',$db_config) ? '<a class="cursor-hand" onclick="$(\'[data-contact=\\\''.$ticket['clientid'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').get_contact($dbc, $ticket['clientid']).(in_array('Business',$db_sort) && !in_array('Business',$db_config) && in_array('Contact',$db_config) ? '</a>' : '').': '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Status',$db_summary) && $disp10 == 'block') {
		            array_push($all_summary,'Status');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Status">
				<h4>'.$ticket_noun.' Summary<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `tickets`.`status` FROM `tickets` WHERE `tickets`.`deleted`=0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `tickets`.`status` != 'Archive' $match_business GROUP BY `tickets`.`status`");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p><a class="cursor-hand" onclick="'.(in_array('Status',$db_sort) ? '$(\'[data-status=\\\''.$ticket['status'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });' : '').'">'.$ticket['status'].'</a>: '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Mine',$db_summary) && $disp11 == 'block') {
		            array_push($all_summary,'Mine');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Mine">
				<h4>My '.$ticket_tile.'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `tickets`.`status` FROM `tickets` WHERE CONCAT(IFNULL(`tickets`.`contactid`,''),',',IFNULL(`internal_qa_contactid`,''),',',IFNULL(`deliverable_contactid`,'')) LIKE CONCAT('%".$_SESSION['contactid']."%') AND `tickets`.`deleted`=0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `tickets`.`status` != 'Archive' GROUP BY `tickets`.`status`");
		            $ticket_status = get_config($dbc, "ticket_status");
		            $ticket_status_color = explode(',', get_config($dbc, "ticket_status_color"));
		            while($ticket = $tickets->fetch_assoc()) {
		                $var_count = explode($ticket['status'], $ticket_status);
		                $occr = substr_count($var_count[0], ",");
		                $color_apply = '';
		                if (strpos($ticket_status, $ticket['status']) !== false) {
		                    $color_apply = $ticket_status_color[$occr];
		                }
		                $c_a = 'style="background-color:'.$color_apply.';height: 12px;width: 12px;margin-top: 4px;"';

		                $block .= '<div class="row"><div class="col-sm-1"><a class="cursor-hand" onclick="'.(in_array('Status',$db_sort) ? '$(\'[data-status=\\\''.$ticket['status'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });' : '').'$(\'[data-staff='.$_SESSION['contactid'].']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });"><div '. $c_a .'></div></div><div class="col-sm-8">'.$ticket['status'].'</a>: '.$ticket['count'].'</div></div>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Created',$db_summary) && $disp12 == 'block') {
		            array_push($all_summary,'Created');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Created">
				<h4>'.$ticket_tile.' Created by Staff<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `created_by` FROM `tickets` WHERE `deleted`=0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `status` != 'Archive' AND `created_by` > 0 $match_business GROUP BY `created_by`");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p>'.(in_array('Staff Create',$db_sort) ? '<a class="cursor-hand" onclick="$(\'[data-creator=\\\''.$ticket['created_by'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').''.get_contact($dbc, $ticket['created_by']).(in_array('Staff Create',$db_sort) ? '</a>' : '').': '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Assigned',$db_summary) && $disp13 == 'block') {
		            array_push($all_summary,'Assigned');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Assigned">
				<h4>'.$ticket_tile.' Assigned to Staff<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT COUNT(*) count, `contacts`.`contactid` FROM `tickets` LEFT JOIN `contacts` ON `contacts`.`deleted`=0 AND `contacts`.`status` > 0 AND `contacts`.`show_hide_user` > 0 AND CONCAT(',',IFNULL(`tickets`.`contactid`,''),',',IFNULL(`internal_qa_contactid`,''),',',IFNULL(`deliverable_contactid`,''),',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `tickets`.`deleted`=0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `tickets`.`status` != 'Archive' AND `contacts`.`contactid` > 0 $match_business GROUP BY `contacts`.`contactid`");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p>'.(in_array('Staff',$db_sort) ? '<a class="cursor-hand" onclick="$(\'[data-staff=\\\''.$ticket['contactid'].'\\\']\').first().click().parents(\'li\').each(function() { $(this).find(\'a\').first().filter(\'.collapsed\').click(); });">' : '').get_contact($dbc, $ticket['contactid']).(in_array('Staff',$db_sort) ? '</a>' : '').': '.$ticket['count'].'</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        if(in_array('Top 25 All',$db_summary) && $disp14 == 'block') {
		            array_push($all_summary,'Top 25 All');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Top 25 All">
				<h4>Last 25 '.$ticket_tile.(in_array('ALL',$db_sort) ? '<a class="pull-right small" href="" onclick="$(\'[data-type=ticket] [data-status=ALL_STATUS]\').click().closest(\'[data-type]\').find(\'[data-toggle=collapse]\').first().filter(\'.collapsed\').click(); return false;">View All</a>' : '').'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		            $tickets = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `status` NOT IN ('Archive','Archived','Done') $match_business ORDER BY `ticketid` DESC LIMIT 0, 25");
		            while($ticket = $tickets->fetch_assoc()) {
		                if($summary_urls == 'slider') {
		                    $block .= '<p><a href="'.WEBSITE_URL.'/Ticket/index.php?'.$current_tile.'edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.get_ticket_label($dbc, $ticket).'</a></p>';
		                } else {
		                    $block .= '<p><a href="index.php?'.$current_tile.'edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.'/Ticket/index.php?tile_name='.$_GET['tile_name']).'">'.get_ticket_label($dbc, $ticket).'</a></p>';
		                }
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        foreach($ticket_tabs as $type => $label) {
		            array_push($all_summary,'Top 25 '.$type);
		            if($listarr!='0') { $disp15 = 'none';}
		            if($listarr == 'Top 25 '.$type){$disp15 = 'block';}
		            if(in_array('Top 25 '.$type,$db_summary) && $disp15 == 'block') {
		                $block_length = 68;
		                $block = '<div class="overview-block">
                    <input type="hidden" name="id" value="Top 25 '.$type.'">
					<h4>Last 25 '.$label.(in_array('ALL',$db_sort) ? '<a class="pull-right small" href="" onclick="$(\'[data-type=ticket_'.$type.'] [data-status=ALL_STATUS]\').click().closest(\'[data-type]\').find(\'[data-toggle=collapse]\').first().filter(\'.collapsed\').click(); return false;">View All</a>' : '').'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		                $tickets = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') $match_business AND `ticket_type`='$type' ORDER BY `ticketid` DESC LIMIT 0, 25");
		                while($ticket = $tickets->fetch_assoc()) {
		                    if($summary_urls == 'slider') {
		                        $block .= '<p><a href="'.WEBSITE_URL.'/Ticket/index.php?'.$current_tile.'edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.get_ticket_label($dbc, $ticket).'</a></p>';
		                    } else {
		                        $block .= '<p><a href="index.php?'.$current_tile.'edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.'/Ticket/index.php?'.$current_tile).'">'.get_ticket_label($dbc, $ticket).'</a></p>';
		                    }
		                    $block_length += 17;
		                }
		                $block .= '</div>';
		                $blocks[] = [$block_length, $block];
		                $total_length += $block_length;
		            }
		        }
		        if(in_array('Top 25 Forms',$db_summary) && $disp16 == 'block') {
		            array_push($all_summary,'Top 25 Forms');
		            $block_length = 68;
		            $block = '<div class="overview-block">
                <input type="hidden" name="id" value="Top 25 Forms">
				<h4>Last 25 Forms<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		                // $tickets = $dbc->query("SELECT `tickets`.*, `revision`, `last_revision` FROM (SELECT MAX(`id`) `formid`, `ticketid`, `pdf_type`, MAX(`revision`) `revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`".($form['revisions'] > 0 ? ", `revision`" : "").") `forms` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `forms`.`pdf_type`=`revisions`.`pdf_type` AND `forms`.`ticketid`=`revisions`.`ticketid` LEFT JOIN `tickets` ON `forms`.`ticketid`=`tickets`.`ticketid` WHERE `forms`.`pdf_type`='".$form['id']."' AND `tickets`.`ticketid` > 0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') $match_business ORDER BY `formid` DESC LIMIT 0,25");
		            $tickets = $dbc->query("SELECT `tickets`.*, `forms`.`pdf_type`, `ticket_pdf`.`pdf_name`, `revision`, `last_revision` FROM (SELECT MAX(`id`) `formid`, `ticketid`, `pdf_type`, MAX(`revision`) `revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `forms` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `forms`.`pdf_type`=`revisions`.`pdf_type` AND `forms`.`ticketid`=`revisions`.`ticketid` LEFT JOIN `tickets` ON `forms`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `ticket_pdf` ON `forms`.`pdf_type`=`ticket_pdf`.`id` WHERE `tickets`.`ticketid` > 0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') $match_business ORDER BY `formid` DESC LIMIT 0,25");
		            while($ticket = $tickets->fetch_assoc()) {
		                $block .= '<p><a target="_blank" href="../Ticket/download/'.config_safe_str($ticket['pdf_name']).'_'.$ticket['ticketid'].'.pdf">'.get_ticket_label($dbc, $ticket).' - '.$ticket['pdf_name'].'</a>';
		                if($tile_security['edit'] > 0) {
	                        if($summary_urls == 'slider') {
	                            $block .= '<span class="pull-right"><a href="" onclick="overlayIFrameSlider(\'?'.$current_tile.'custom_form='.$ticket['pdf_type'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=edit\'); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a><a href="" onclick="overlayIFrameSlider(\'?'.$current_tile.'custom_form='.$ticket['pdf_type'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=new\'); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/ticket_pdf_revisions.php?'.$current_tile.'form='.$ticket['pdf_type'].'&ticketid='.$ticket['ticketid'].'\', \'auto\', false, true); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/eyeball.png" class="inline-img theme-color-icon no-toggle" title="View Revisions"></a>'.($tile_security['config'] > 0 ? '<a href="" onclick="remForm(\''.$ticket['pdf_type'].'\',\''.$ticket['ticketid'].'\',\''.$ticket['revision'].'\',$(this).closest(\'p\')); return false;" class="pull-right small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>' : '').'</span>';
	                        } else {
	                            $block .= '<span class="pull-right"><a href="?'.$current_tile.'custom_form='.$ticket['pdf_type'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=edit" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a><a href="?'.$current_tile.'custom_form='.$ticket['pdf_type'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=new&revision_mode=new" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/ticket_pdf_revisions.php?'.$current_tile.'form='.$ticket['pdf_type'].'&ticketid='.$ticket['ticketid'].'\', \'auto\', false, true); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/eyeball.png" class="inline-img theme-color-icon no-toggle" title="View Revisions"></a>'.($tile_security['config'] > 0 ? '<a href="" onclick="remForm(\''.$ticket['pdf_type'].'\',\''.$ticket['ticketid'].'\',\''.$ticket['revision'].'\',$(this).closest(\'p\')); return false;" class="pull-right small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>' : '').'</span>';
	                        }
		                }
		                $block .= '</p>';
		                $block_length += 17;
		            }
		            $block .= '</div>';
		            $blocks[] = [$block_length, $block];
		            $total_length += $block_length;
		        }
		        $forms = $dbc->query("SELECT `id`, `pdf_name`, `revisions` FROM `ticket_pdf` WHERE `deleted`=0 AND IFNULL(`dashboard`,'')!='hidden' ORDER BY `pdf_name`");
		        $a = 0;
		        while($form = $forms->fetch_assoc()) {
		            if($listarr!='0') {$disp17 = 'none';}
		            if($listarr == 'Top 25 Form '.$form['id']){$disp17 = 'block';}
		            if(in_array('Top 25 Form '.$form['id'],$db_summary) && $disp17 == 'block') {
		                array_push($all_summary,$a);
		                $block_length = 68;
		                $block = '<div class="overview-block">
                    <input type="hidden" name="id" value="Top 25 Form '.$a.'">
					<h4>Last 25 '.$form['pdf_name'].(in_array('ALL',$db_sort) ? '<a class="pull-right small" href="" onclick="$(\'[data-type=form_'.$form['id'].'] [data-status=ALL_STATUS]\').click().closest(\'[data-type]\').find(\'[data-toggle=collapse]\').first().filter(\'.collapsed\').click(); return false;">View All</a>' : '').'<img class="inline-img pull-right drag_handle" src="../img/icons/drag_handle.png"></h4>';
		                $tickets = $dbc->query("SELECT `tickets`.*, `revision`, `last_revision` FROM (SELECT MAX(`id`) `formid`, `ticketid`, `pdf_type`, MAX(`revision`) `revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`".($form['revisions'] > 0 ? ", `revision`" : "").") `forms` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `forms`.`pdf_type`=`revisions`.`pdf_type` AND `forms`.`ticketid`=`revisions`.`ticketid` LEFT JOIN `tickets` ON `forms`.`ticketid`=`tickets`.`ticketid` WHERE `forms`.`pdf_type`='".$form['id']."' AND `tickets`.`ticketid` > 0 AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') $match_business ORDER BY `formid` DESC LIMIT 0,25");
		                while($ticket = $tickets->fetch_assoc()) {
		                    $link = '../Ticket/download/'.config_safe_str($form['pdf_name']).'_'.$ticket['revision'].'_'.$ticket['ticketid'].'.pdf';
		                    if(!file_exists($link)) {
		                        $link = '../Ticket/ticket_pdf_custom.php?'.$current_tile.'ticketid='.$ticket['ticketid'].'&form='.$form['id'].'&revision='.$ticket['revision'];
		                    }
		                    $block .= '<p><a target="_blank" href="'.$link.'">'.get_ticket_label($dbc, $ticket).($form['revisions'] > 0 ? ' Revision #'.$ticket['revision'].' of '.$ticket['last_revision'] : '').'</a>';
		                    if($tile_security['edit'] > 0) {
		                        if($summary_urls == 'slider') {
		                            $block .= '<span class="pull-right"><a href="" onclick="overlayIFrameSlider(\'?'.$current_tile.'custom_form='.$form['id'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=edit\'); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a><a href="" onclick="overlayIFrameSlider(\'?'.$current_tile.'custom_form='.$form['id'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=new\'); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/ticket_pdf_revisions.php?'.$current_tile.'form='.$form['id'].'&ticketid='.$ticket['ticketid'].'\', \'auto\', false, true); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/eyeball.png" class="inline-img theme-color-icon no-toggle" title="View Revisions"></a>'.($tile_security['config'] > 0 ? '<a href="" onclick="remForm(\''.$form['id'].'\',\''.$ticket['ticketid'].'\',\''.$ticket['revision'].'\',$(this).closest(\'p\')); return false;" class="pull-right small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>' : '').'</span>';
		                        } else {
		                            $block .= '<span class="pull-right"><a href="?'.$current_tile.'custom_form='.$form['id'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=edit&revision_mode=edit" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a><a href="?'.$current_tile.'custom_form='.$form['id'].'&revision='.$ticket['revision'].'&ticketid='.$ticket['ticketid'].'&pdf_mode=new&revision_mode=new" class="small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/ticket_pdf_revisions.php?'.$current_tile.'form='.$form['id'].'&ticketid='.$ticket['ticketid'].'\', \'auto\', false, true); return false;" class="small"><img src="'.WEBSITE_URL.'/img/icons/eyeball.png" class="inline-img theme-color-icon no-toggle" title="View Revisions"></a>'.($tile_security['config'] > 0 ? '<a href="" onclick="remForm(\''.$form['id'].'\',\''.$ticket['ticketid'].'\',\''.$ticket['revision'].'\',$(this).closest(\'p\')); return false;" class="pull-right small"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>' : '').'</span>';
		                        }
		                    }
		                    $block .= '</p>';
		                    $block_length += 17;
		                }
		                $block .= '</div>';
		                $blocks[] = [$block_length, $block];
		                $total_length += $block_length;
		            } $a++;
		        }
		        $display_column = 0;
		        $displayed_length = 0;


		    }
		}
		?>
		<div class="sort-block-group ui-sortable">
			<div class="col-sm-6 block-group" id="sort_div">
				<?php foreach($blocks as $block_count => $block) {
					if(($block_count+1)/(round(count($blocks)/2)+1) == 1) {
						$displayed_length = 0;
						$display_column = 1;
						echo '</div><div class="col-sm-6 block-group" id="sort_div1">'.$block[1].'';
					} else {
						$displayed_length += $block[0];
						echo $block[1];
					}
				} ?>
			</div>
		</div>
	</div>

<?php } ?>

<script>
	$('.sidebar-higher-level.highest-level ul li.sidebar-higher-level').on('click', function (e) {
		e.preventDefault();
		if($(this).children('a').hasClass('collapsed')){
			$(this).removeClass('li-collapsed-active-show');
		}else{
			$(this).addClass('li-collapsed-active-show');
		}
	});

</script>
