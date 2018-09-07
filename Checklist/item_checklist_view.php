<script>
$(document).ready(function() {
	var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
		return $( this ).outerWidth( true );
	}).get() );

	var maxHeight = -1;

	$('.ui-sortable').each(function() {
		maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
	});

	$(function() {
		$(".connectedChecklist").width(maxWidth).height(maxHeight);
	});
	
	$( '.connectedChecklist' ).each(function () {
		this.style.setProperty( 'height', maxHeight, 'important' );
		this.style.setProperty( 'width', maxWidth, 'important' );

		<?php if($check_table_orient == 1) { ?>
			$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
		<?php } else { ?>
			$(this).attr('style', 'height:'+maxHeight+'px !important;');
		<?php } ?>
	});
	
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
	
    $( ".connectedChecklist" ).sortable({
		beforeStop: function(e, ui) { ui.helper.removeClass('popped-field'); },
		connectWith: ".connectedChecklist",
		delay: 500,
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		start: function(e, ui) {ui.helper.addClass('popped-field'); },
		update: function( event, ui ) {
			var lineid = $(ui.item).find('input[type=checkbox]').val();
			var afterid = $(ui.item.prev()).find('input[type=checkbox]').val();
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "../Checklist/checklist_ajax.php?action=item_priority&lineid="+lineid+"&afterid="+afterid,
				dataType: "html",   //expect html to be returned
				success: function(response){
					console.log(response);
				}
			});
		}
    }).disableSelection();
});
$(document).on('change', 'select[name="search_cat"]', function() { filterCategory(this.value); });

function addMe(textbox) {
	$(textbox).prop("disabled",true);
	var line_item = textbox.value;
	var typeId = $(textbox).data('checklist');

	$.ajax({    //create an ajax request to load_page.php
		type: "POST",
		url: "../Checklist/checklist_ajax.php?action=add_checklist_item",
		data: { checklist: typeId, line: line_item },
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function checklistChange(checkbox) {
    if($(checkbox).is(':checked')){
        var checked = 1;
    } else {
        var checked = 0;
    }
    $.ajax({
        type: "GET",
        url: "../Checklist/checklist_ajax.php?action=item_checklist&checklistid="+checkbox.value+"&checked="+checked+"&unit="+$('[name=search_equip]').val(),
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
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?action=item_alert',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?action=item_email',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: '../Checklist/checklist_ajax.php?action=item_reminder',
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
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target);
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
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=checklists&id='+checklist_id+'&type='+type, 'auto', false, true);
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
				url: '../Checklist/checklist_ajax.php?action=item_reply',
				data: { id: checklist_id, reply: save_reply },
				complete: function(result) { window.location.reload(); }
			})
		}
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
				url: '../Checklist/checklist_ajax.php?action=item_quick_time',
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
			url: "../Checklist/checklist_ajax.php?action=item_upload&type="+type+"&id="+checklist_id,
			data: fileData,
			complete: function(result) {
				console.log(result.responseText);
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
		url: "../Checklist/checklist_ajax.php?action=item_flag",
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
			url: "../Checklist/checklist_ajax.php?action=item_delete&checklistid="+checklist_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				window.location.reload();
				console.log(response.responseText);
			}
		});
	}
	else if(confirm("Are you sure you want to archive this checklist?")) {
		window.location = "<?php echo WEBSITE_URL; ?>/delete_restore.php?action=delete&remove_item_checklist=all&checklistid=" + checklist_id;
	}
}
</script>
<?php if(!empty($checklist['checklistid'])): ?>
	<div style="display: inline-block; max-width: 48em; width: 100%;">
		<?php $checklistid = $checklist['checklistid'];
		$document_query = "SELECT * FROM item_checklist_document WHERE checklistid='$checklistid' ORDER BY checklistdocid DESC";
		$result = mysqli_query($dbc, $document_query);
		if(mysqli_num_rows($result) > 0) {
			echo "<table class='table table-bordered' style='width:100%;'>
			<tr class='hidden-xs hidden-sm'>
			<th>Link / Document</th>
			<th>Date</th>
			<th>Attached By</th>
			</tr>";
			while($row = mysqli_fetch_array($result)) {
				echo '<tr>';
				if(empty($row['document'])) {
					echo '<td data-title="Link"><a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a></td>';
				} else {
					echo '<td data-title="Document"><a href="download/'.(empty($row['document']) ? $row['link'] : $row['document']).'" target="_blank">'.$row['document'].'</a></td>';
				}
				echo '<td data-title="Date">'.$row['created_date'].'</td>';
				echo '<td data-title="Attached By">'.get_contact($dbc, $row['created_by']).'</td>';
				echo '</tr>';
			}
			echo '</table>';
		} ?>
		<div class="pull-right">
			<span style="cursor: pointer;" data-checklist="BOARD<?php echo $checklistid; ?>">
				<span style="padding: 0.25em 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" style="height:2.5em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Send Alert" onclick="send_alert(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" style="height:2.5em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" style="height:2.5em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" style="height:2.5em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" style="height:2.5em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Archive Checklist" onclick="archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2.5em;"></span>
				<input type="text" name="reminder_board_<?= $checklistid; ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
				<input type="file" name="attach_board_<?= $checklistid; ?>" style="display:none;" />
			</span>
			<input type="file" name="attach_board_<?= $checklistid; ?>" style="display:none;" />
			<div style="display:inline-block">
				<span class="popover-examples list-inline pull-left" style="margin-top:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to edit the current Checklist."><img src="../img/info.png" width="20"></a></span>
				<a href="add_checklist.php?checklistid=<?= $checklistid ?>" class="btn brand-btn mobile-block mobile-100">Edit</a>
			</div>
			<div style="display:inline-block">
				<span class="popover-examples list-inline pull-left" style="margin-top:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to export the current Checklist into a PDF."><img src="../img/info.png" width="20"></a></span>
				<button type="submit" name="export_pdf" value="<?= $checklistid ?>" class="btn brand-btn mobile-block mobile-100">Export <img src="../img/pdf.png"></button>
			</div>
		</div>
		<div class="clearfix"></div>
		<ul class="connectedChecklist">
			<li class="ui-state-default ui-state-disabled no-sort" style="cursor:pointer; font-size: 1.5em;"><?= $checklist['checklist_name'] ?></li>
			<?php $line_items = mysqli_query($dbc, "SELECT * FROM `item_checklist_line` WHERE `checklistid`='$checklistid' AND `deleted`=0 ORDER BY `priority`");
			while($line_item = mysqli_fetch_array($line_items)) {
				echo '<li class="ui-state-default" '.($line_item['flag_colour'] == '' ? '' : 'style="background-color: #'.$line_item['flag_colour'].';"').'>';
				echo '<span style="cursor:pointer; font-size: 1em;"><input type="checkbox" onclick="checklistChange(this);" value="'.$line_item['checklistlineid'].'" style="height: 1.25em; width: 1.25em;" name="checklistlineid[]">';
				echo '<span class="pull-right" style="display:inline-block; width:calc(100% - 2em);" data-checklist="'.$line_item['checklistlineid'].'">';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Time" onclick="add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
				echo '</span>';
				echo '<input type="text" name="reply_'.$line_item['checklistlineid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
				echo '<input type="text" name="checklist_time_'.$line_item['checklistlineid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
				echo '<input type="text" name="reminder_'.$line_item['checklistlineid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
				echo '<input type="file" name="attach_'.$line_item['checklistlineid'].'" style="display:none;" class="form-control" />';
				echo '<br /><span class="display-field">#'.$line_item['checklistlineid'].': '.html_entity_decode($line_item['checklist']).'</span>&nbsp;&nbsp;';
				$documents = mysqli_query($dbc, "SELECT * FROM item_checklist_document WHERE checklistlineid='".$line_item['checklistlineid']."'");
				while($doc = mysqli_fetch_array($documents)) {
					echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a>';
				}
				echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="height:1.5em; width:1.5em;" /></span>';

				echo '</li>';
			} ?>
			<li class="new_task_box no-sort"><input onChange="addMe(this)" name="add_checklist" placeholder="Add New Checklist Item" data-checklist="<?= $checklistid ?>" type="text" class="form-control" /></li>
		</ul>
	</div>
<?php else:
	echo "<h3>No checklists found for this item.</h3>";
endif;