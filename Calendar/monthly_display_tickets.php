<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
function ticket_choose_user(target, id, date) {
	var title	= 'Select a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendalert'+'&offline='+offline_mode,
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendemail'+'&offline='+offline_mode,
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendreminder'+'&offline='+offline_mode,
						data: { id: id, schedule: date, user: $(this).closest('body').find('select').val() },
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
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target);
	$('.iframe_title').text(title);
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
}
function ticket_send_alert(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	ticket_choose_user('alert', ticket_id);
}
function ticket_send_email(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tickets&id='+ticket_id, 'auto', false, true);
}
function ticket_send_reminder(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reminder_'+ticket_id+']').show().focus();
	$('[name=ticket_reminder_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reminder_'+ticket_id+']').blur(function() {
		$(this).hide();
	});
	$('[name=ticket_reminder_'+ticket_id+']').change(function() {
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			ticket_choose_user('reminder', ticket_id, date);
		}
	});
}
function ticket_add_note(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reply_'+ticket_id+']').show().focus();
	$('[name=ticket_reply_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reply_'+ticket_id+']').blur(function() {
		$(this).hide();
		var note = $(this).val().trim();
		$(this).val('');
		if(note != '') {
			$.ajax({
				method: 'POST',
				url: 'calendar_ajax_all.php?fill=ticketsendnote'+'&offline='+offline_mode,
				data: { id: ticket_id, note: note },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
}
function ticket_add_time(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_time_'+ticket_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'calendar_ajax_all.php?fill=ticketquicktime'+'&offline='+offline_mode,
				data: { id: ticket_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=ticket_time_'+ticket_id+']').timepicker('show');
}
function ticket_attach_file(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	var file_id = 'ticket_attach_'+ticket_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$(ticket).parents('li').find('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "calendar_ajax_all.php?fill=ticketsendupload&id="+ticket_id+'&offline='+offline_mode,
			data: fileData,
			complete: function(result) { console.log(result.responseText); }
		});
	});
	$(ticket).parents('li').find('[name='+file_id+']').click();
}
function ticket_flag_item(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$.ajax({
		method: "POST",
		url: "calendar_ajax_all.php?fill=ticketflag"+'&offline='+offline_mode,
		data: { id: ticket_id },
		complete: function(result) {
			console.log(result.responseText);
			$(ticket).closest('.pull-right').siblings('a').css('color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function ticket_archive(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	if(confirm("Are you sure you want to archive this ticket?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "calendar_ajax_all.php?fill=ticketquickarchive&id="+ticket_id+'&offline='+offline_mode,
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response.responseText);
				$(ticket).parents('li').hide();
			}
		});
	}
}
</script>

<?php
if($_GET['block_type'] == 'team') {
	$result = mysqli_query($dbc,"SELECT * FROM `teams` WHERE `teamid` = '$contact_id' AND `deleted` = 0");
} else if($_GET['mode'] == 'client') {
	$result = mysqli_query($dbc,"SELECT * FROM contacts WHERE deleted=0 AND `contactid` = '$contact_id'");
} else {
	$result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `contactid` = '$contact_id'");
}

while($row = mysqli_fetch_array( $result )) {
	$completed_tickets = 0;
	$all_tickets = [];
	if($_GET['block_type'] == 'team') {
    	$row['calendar_color'] = '#3ac4f2';

		$staff = (!empty($row['team_name']) ? $row['team_name'] : 'Team #'.$row['teamid']).' ('.get_team_name($dbc, $contact_id, ', ', 1).')';

		$contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$contact_id' AND `deleted` = 0"),MYSQLI_ASSOC);
		$contacts_query = [];
		$contacts_arr = [];
		foreach ($contact_list as $contact) {
			if(strtolower(get_contact($dbc, $contact['contactid'], 'category')) == 'staff') {
				$contacts_query[] = " CONCAT(',',IFNULL(`contactid`,''),',') LIKE '%,".$contact['contactid'].",%'";
				$contacts_arr[] = $contact['contactid'];
			}
		}
		if(!empty($contacts_query)) {
			$contacts_query = " AND ".implode(" AND ", $contacts_query);
			$contacts_arr = array_filter(array_unique($contacts_arr));
			sort($contacts_arr);
			$contacts_arr = implode(',',$contacts_arr);
		} else {
			$contacts_query = " AND 1=0";
			$contacts_arr = ',PLACEHOLDER,';
		}

	    $tickets = mysqli_query($dbc,"SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM tickets WHERE `deleted`=0 AND '$new_today_date' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) AND `status` NOT IN ('Archive', 'Done', 'Internal QA', 'Customer QA')".$contacts_query.$allowed_ticket_types_query);
	    while($row_tickets = mysqli_fetch_array( $tickets )) {
			$ticket_contacts = array_filter(array_unique(explode(',',$row_tickets['contactid'])));
			sort($ticket_contacts);
			if(implode(',',$ticket_contacts) == $contacts_arr) {
	        	$all_tickets[] = $row_tickets;
	        }
	    }
	    if(!empty($all_tickets)) {
	    	$column .= '<div class="calendar_block calendarSortable" data-contact="'.$contact_id.'" data-date="'.$new_today_date.'" data-blocktype="'.$_GET['block_type'].'">';
	        $column .= '<h4><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/teams.php?teamid='.$contact_id.'\'); return false;">'.$staff.'</a></h4>';
	    }
	} else {
	    $contactid = $row['contactid'];
	    $staff = get_staff($dbc, $contactid);
	    if(empty($row['calendar_color'])) {
	    	$row['calendar_color'] = '#3ac4f2';
	    }

	    if($_GET['mode'] == 'client') {
		    $tickets = mysqli_query($dbc,"SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM tickets WHERE `deleted`=0 AND (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),to_do_date)) AND (CONCAT(',',`businessid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`clientid`,',') LIKE '%,".$contactid.",%') AND status NOT IN('Archive', 'Done')".$allowed_ticket_types_query);
			$deleted_tickets = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),to_do_date)) AND (CONCAT(',',`businessid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`clientid`,',') LIKE '%,".$contactid.",%') AND (`deleted` = 1 OR `status` IN ('Archive', 'Done'))".$allowed_ticket_types_query))['num_rows'];
	    } else {
		    $tickets = mysqli_query($dbc,"SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM tickets WHERE `deleted`=0 AND (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),to_do_date)) AND (CONCAT(',',contactid) LIKE '%," . $contactid . ",%' OR CONCAT(',',internal_qa_contactid) LIKE '%," . $contactid . ",%' OR CONCAT(',',deliverable_contactid) LIKE '%," . $contactid . ",%') AND status NOT IN('Archive', 'Done')".$allowed_ticket_types_query);
			$deleted_tickets = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),to_do_date)) AND (CONCAT(',',contactid) LIKE '%," . $contactid . ",%' OR CONCAT(',',internal_qa_contactid) LIKE '%," . $contactid . ",%' OR CONCAT(',',deliverable_contactid) LIKE '%," . $contactid . ",%') AND (`deleted` = 1 OR `status` IN ('Archive', 'Done'))".$allowed_ticket_types_query))['num_rows'];
		}
	    while($row_tickets = mysqli_fetch_array( $tickets )) {
	        if(((($row_tickets['status'] == 'Internal QA') && ($new_today_date == $row_tickets['internal_qa_date']) && (strpos(','.$row_tickets['internal_qa_contactid'].',', ','.$contactid.',') !== FALSE)) || (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($new_today_date == $row_tickets['deliverable_date']) && (strpos(','.$row_tickets['deliverable_contactid'].',', ','.$contactid.',') !== FALSE)) || (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($new_today_date >= $row_tickets['to_do_date'] && $new_today_date <= $row_tickets['to_do_end_date']) && (strpos(','.$row_tickets['contactid'].',', ','.$contactid.',') !== FALSE))) || $_GET['mode'] == 'client') {
	        	$all_tickets[] = $row_tickets;
	        }
	    }
	    if(!empty($all_tickets)) {
	    	$column .= '<div class="calendar_block calendarSortable" data-contact="'.$contactid.'" data-date="'.$new_today_date.'" data-blocktype="'.$_GET['block_type'].'">';
	        $column .= '<h4>'.$staff.'</h4>';
	    }
	}
    foreach($all_tickets as $row_tickets) {
        $date_color = 'block/green.png';
        if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
            $date_color = 'block/red.png';
        }
        if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
            $date_color = 'block/orange.png';
        }

        $status_color = '';
        $status = $row_tickets['status'];
		if ($status == 'Internal QA') {
			if (!empty($row_tickets['internal_qa_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row_tickets['internal_qa_start_time']));
				if (!empty($row_tickets['internal_qa_end_time'])) {
					$duration = (strtotime($row_tickets['internal_qa_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row_tickets['internal_qa_end_time']));
				} else {
					$max_time = explode(':',$row_tickets['max_qa_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row_tickets['max_qa_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		} else if ($status == 'Customer QA') {
			if (!empty($row_tickets['deliverable_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row_tickets['deliverable_start_time']));
				if (!empty($row_tickets['deliverable_end_time'])) {
					$duration = (strtotime($row_tickets['deliverable_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row_tickets['deliverable_end_time']));
				} else {
					$max_time = explode(':',$row_tickets['max_qa_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row_tickets['max_qa_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		} else {
			if (!empty($row_tickets['to_do_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row_tickets['to_do_start_time']));
				if (!empty($row_tickets['to_do_end_time'])) {
					$duration = (strtotime($row_tickets['to_do_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row_tickets['to_do_end_time']));
				} else {
					$max_time = explode(':',$row_tickets['max_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row_tickets['max_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		}
		if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$checkmark_ticket = 'calendar-checkmark-ticket-month';
		} else {
			$checkmark_ticket = '';
		}
		if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$row_tickets['ticket_type']])) {
            $ticket_styling = ' background-color:'.$ticket_tabs_color[$row_tickets['ticket_type']].';';
        } else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
		} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
			$ticket_styling = ' background-color:'.$calendar_incomplete_color[$status].';';
		} else {
			$ticket_styling = ' background-color:'.$row['calendar_color'].';';
		}
		if(in_array($status, $calendar_checkmark_status)) {
			$completed_tickets++;
		}
		$status_icon = get_ticket_status_icon($dbc, $row_tickets['status']);
	    if(!empty($status_icon)) {
	        $icon_img = '';
	    	$icon_background = '';
	    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
    			$icon_background = " background-image: url('".$status_icon."'); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
	    	} else {
		    	if($status_icon == 'initials') {
					$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($row_tickets['status']).'</span>';
		    	} else {
			        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
			    }
			}
	    } else {
	        $icon_img = '';
	    	$icon_background = '';
	    }
        $status_color = 'block/'.$status_array[$status];
	    $recurring_icon = '';
	    if($row_tickets['is_recurrence'] == 1) {
	    	$recurring_icon = "<img src='".WEBSITE_URL."/img/icons/recurring.png' style='width: 1.2em; margin: 0.1em;' class='pull-right' title='Recurring ".TICKET_NOUN."'>";
	    }
		$contactide = $_SESSION['contactid'];
		$get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_settings WHERE contactid='$contactide'"));
		$list_view = $get_table_orient['calendar_list_view'];
		if($list_view == 1) {
       //$column .= '<a class="" href="#" style="color:black; display:block; border-bottom:1px solid black; white-space: nowrap; width:200px; text-overflow: ellipsis; overflow:hidden; padding: 2px;  background-color: '.$row['calendar_color'].';" id="ticket_'.$row_tickets['ticketid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row_tickets['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;" title="#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt=""><img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="" style="margin-left:3px;">&nbsp;#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a>';
		$column .= '<a class="sortable-blocks '.$checkmark_ticket.'" href="" onclick="'.($edit_access == 1 ? 'overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$row_tickets['ticketid'].'\');' : '').'return false;" style="color:black;  display:block; border-bottom:1px solid black; white-space: nowrap; width:200px; text-overflow: ellipsis; overflow:hidden; padding: 2px;'.$ticket_styling.$icon_background.'" id="ticket_'.$row_tickets['ticketid'].'" title="#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'" data-ticket="'.$row_tickets['ticketid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-clientid="'.$row_tickets['clientid'].'" data-businessid="'.$row_tickets['businessid'].'" data-itemtype="ticket" data-timestamp="'.date('Y-m-d H:i:s').'" data-tickettype="'.$row_tickets['ticket_type'].'"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt=""><img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="" style="margin-left:3px;">&nbsp;#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a>';
		} else {
		$column .= '';
		$column .= '<span class="pull-right quick_actions" style="display:inline-block; width:100%;" data-ticket="'.$row_tickets['ticketid'].'">';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="ticket_flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="ticket_send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="ticket_send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="ticket_send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="ticket_attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Note" onclick="ticket_add_note(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Time" onclick="ticket_add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="ticket_archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1em;" onclick="return false;"></span>';
		$column .= '</span>';
		$column .= '<input type="text" name="ticket_reply_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
		$column .= '<input type="text" name="ticket_time_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
		$column .= '<input type="text" name="ticket_reminder_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
		$column .= '<input type="file" name="ticket_attach_'.$row_tickets['ticketid'].'" style="display:none;" class="form-control" />';
		$column .= '<br /><a class="sortable-blocks '.$checkmark_ticket.'" href="" onclick="'.($edit_access == 1 ? 'overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$row_tickets['ticketid'].'\');' : '').'return false;" style="display:block; padding: 5px;color:black;  '.($row_tickets['flag_colour'] != '' ? 'color: #'.$row_tickets['flag_colour'].';' : '').'
        border-radius: 10px;'.$ticket_styling.$icon_background.'" id="ticket_'.$row_tickets['ticketid'].'" data-ticket="'.$row_tickets['ticketid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-clientid="'.$row_tickets['clientid'].'" data-businessid="'.$row_tickets['businessid'].'" data-itemtype="ticket" data-timestamp="'.date('Y-m-d H:i:s').'" data-tickettype="'.$row_tickets['ticket_type'].'">';
		$column .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" style="width:1em;" border="0" alt="">&nbsp;<img src="'.WEBSITE_URL.'/img/block/'.$status_array[$status].'" style="width:1em;" border="0" alt="">'.$icon_img;
		if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
			$column .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
		}
		$column .= $recurring_icon;
		$column .= calendarTicketLabel($dbc, $row_tickets, $max_time, $current_start_time, $current_end_time);
		$column .= '</a><br>';
		//$column .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="">&nbsp;<a class="" href="#" style="display:block; padding: 5px;color:black;border-radius: 10px; background-color: '.$row['calendar_color'].';" id="ticket_'.$row_tickets['ticketid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row_tickets['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a><br>';
		}
    }
    if($ticket_summary != '' && !empty($all_tickets)) {
        $column .= '<span>Completed '.$completed_tickets.' of '.count($all_tickets).' '.(count($all_tickets) == 1 ? TICKET_NOUN : TICKET_TILE);
		if($ticket_summary_deleted == 1 && $deleted_tickets > 0) {
			$column .= '<br />'.$deleted_tickets.' Deleted '.($deleted_tickets == 1 ? TICKET_NOUN : TICKET_TILE);
		}
        $column .= '</span>';
    }

    if($_GET['block_type'] != 'team') {
	    if ($search_client != '') {
	        $tasklist = mysqli_query($dbc,"SELECT * FROM tasklist WHERE DATE(task_tododate) = '$new_today_date'  AND contactid = '$contactid' AND status NOT IN('Archive', 'Done') AND businessid = '$search_client'");
	    } else {
	        $tasklist = mysqli_query($dbc,"SELECT * FROM tasklist WHERE DATE(task_tododate) = '$new_today_date'  AND contactid = '$contactid' AND status NOT IN('Archive', 'Done')");
	    }

	    $num_rows1 = mysqli_num_rows($tasklist);
	    if($num_rows1 > 0 && $num_rows == 0) {
	    	$column .= '<div class="calendar_block calendarSortable" data-contact="'.$contactid.'" data-date="'.$new_today_date.'" data-blocktype="'.$_GET['block_type'].'">';
	        $column .= '<h4>'.$staff.'</h4>';
	    }

	    while($row_tasklist = mysqli_fetch_array( $tasklist )) {
	        $date_color = 'block/green.png';
	        if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
	            $date_color = 'block/red.png';
	        }
	        if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
	            $date_color = 'block/orange.png';
	        }
	        $column .= '<a class="sortable-blocks" style="color:black;  display:block; border-bottom:1px solid black; white-space: nowrap; text-overflow: ellipsis; overflow:hidden; padding: 2px;  background-color: '.$row['calendar_color'].';" '.($edit_access == 1 ? 'href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'"' : 'href="" onclick="return false;"').' id="task_'.$row_tasklist['tasklistid'].'" title="'.$row_tasklist['heading'].'" data-task="'.$row_tasklist['tasklistid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-businessid="'.$row_tasklist['businessid'].'" data-clientid="'.$row_tickets['clientid'].'" data-itemtype="task"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;Task: '.$row_tasklist['heading']. '</a><br>';
	        //$column .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="#"  id="task_'.$row_tasklist['tasklistid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Tasks/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.$row_tasklist['heading']. '</a><br>';
	    }
	}

    $column .= '</div>';
}

if($_GET['block_type'] != 'team') {
	$site_wo = mysqli_query($dbc,"SELECT * FROM `site_work_orders` WHERE ((DATE(work_start_date) <= '$new_today_date' AND DATE(work_end_date) >= '$new_today_date') OR `active` LIKE '$new_today_date%') AND `status` NOT IN ('Pending', 'Archived')");
	while($row_site = mysqli_fetch_array( $site_wo )) {
	    $date_color = 'block/green.png';
	    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
	        $date_color = 'block/red.png';
	    } else if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
	        $date_color = 'block/orange.png';
	    }
		$column .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a class="sortable-blocks" '.($edit_access == 1 ? 'href="'.WEBSITE_URL.'/Site Work Orders/view_work_order.php?workorderid='.$row_site['workorderid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'"' : 'href="" onclick="return false;"').' id="site_wo_'.$row_site['workorderid'].'" data-swo="'.$row_site['workorderid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-clientid="'.$row_tickets['clientid'].'" data-businessid="'.$row_site['businessid'].'" data-itemtype="swo">#'.$row_site['id_label'].'</a><br>';
	}
}