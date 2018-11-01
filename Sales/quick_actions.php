<?php include_once('project_dialog.php'); ?>
<!-- Quick Action Scripts -->
<script>
$(document).ready(function() {
    $('.track_time .start').off('click',start_time).click(start_time);
    $('.track_time .stop').off('click',stop_time).click(stop_time);
});

var start_time = function() {
    $(this).closest('.track_time').find('.timer').timer({
        editable: true
    });
    $(this).hide();
    $(this).next('.stop').show();
}

var stop_time = function() {
    var item = $(this).closest('.info-block-detail,.standard-body-title');
    var salesid = $(this).attr('data-salesId');
    item.find('.timer').timer('stop');
    $(this).hide();
    $(this).prev('.start').show();
    var timer_value = item.find('.timer').val();
    item.find('.timer').timer('remove');
    item.find('.track_time').toggle();
    if ( timer_value != '' ) {
        $.ajax({
            method: 'POST',
            url: 'sales_ajax_all.php?action=lead_time',
            data: { id: salesid, time: timer_value },
            success:function(response){
                window.location.reload();
            }
        });
    }
}

var archive_sales_lead = function(sel) {
    var id = sel.id;
    var arr = id.split('_');
    var salesid = arr[1];
    $.ajax({
        url: 'sales_ajax_all.php?fill=archive_sales_lead&salesid='+salesid,
        type: "GET",
        success: function() {
            $(id).closest('.info-block-detail,.standard-body-title').hide();
        }
    });
}

var saveNote = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
    //var salesid = item.data('id');
	var salesid = $(sel).attr('data-salesId');
    overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=sales&id='+salesid, 'auto', false, false);
}

var viewHistory = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
	var salesid = item.data('id');
    overlayIFrameSlider('history.php?id='+salesid, 'auto', true, true);
}

var flagLead = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
	$.ajax({
		url: 'sales_ajax_all.php?action=flag_colour',
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
}

var flagLeadManual = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
    item.addClass('flag_target');
	//var salesid = item.data('id');
    var salesid = $(sel).attr('data-salesId');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=sales&id='+salesid,'auto',false,true);
	/*item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').show();
	item.find('[name=flag_cancel]').off('click').click(function() {
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		return false;
	});
	item.find('[name=flag_off]').off('click').click(function() {
		item.find('[name=colour]').val('');
		item.find('[name=label]').val('');
		item.find('[name=flag_start]').val('');
		item.find('[name=flag_end]').val('');
		item.find('[name=flag_it]').click();
		return false;
	});
	item.find('[name=flag_it]').off('click').click(function() {
		$.ajax({
			url: 'sales_ajax_all.php?action=manual_flag_colour',
			method: 'POST',
			data: {
				field: 'manual_flag_colour',
				value: item.find('[name=colour]').val(),
				table: item.data('table'),
				label: item.find('[name=label]').val(),
				start: item.find('[name=flag_start]').val(),
				end: item.find('[name=flag_end]').val(),
				id: item.data('id'),
				id_field: item.data('id-field')
			}
		});
		$('.flag_target').data('colour',item.find('[name=colour]').val());
		$('.flag_target').css('background-color','#'+item.find('[name=colour]').val());
		$('.flag_target').find('.flag-label').text(item.find('[name=label]').val());
		$('.flag_target').removeClass('flag_target');
		return false;
	});*/
}

var addDocument = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
    var salesid = $(sel).attr('data-salesId');
	item.find('[type=file]').off('change').change(function() {
		var fileData = new FormData();
		fileData.append('file',$(this)[0].files[0]);
		fileData.append('id', salesid);
		$.ajax({
			contentType: false,
			processData: false,
			method: "POST",
			url: "sales_ajax_all.php?action=add_document",
			data: fileData,
            success:function(response){
                window.location.reload();
            }
		});
	}).click();
}

var sendEmail = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
	//var salesid = item.data('id');
    var salesid = $(sel).attr('data-salesId');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=sales&salesid='+salesid,'auto',false,true);
}
var email_action = function() {
	var item = $(this).closest('.info-block-detail,.standard-body-title');
	$.ajax({
		url: 'sales_ajax_all.php?action=send_email',
		method: 'POST',
		data: {
			value: item.find('.select_users select').val(),
			id: item.data('id')
		}
	});
	item.find('.select_users').hide();
	item.find('.select_users select').val('').trigger('change.select2');
}

var createProject = function(sel) {
    var salesid = sel.id;
    $.ajax({
        url: 'sales_ajax_all.php?fill=changeCustCat&salesid='+salesid,
        type: "GET",
        success: function(response) {
            location.replace('../Project/projects.php?edit=0&type=favourite&salesid='+salesid);
        }
    });
}

var setReminder = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
	//var salesid = item.data('id');
    var salesid = $(sel).attr('data-salesId');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=sales&id='+salesid,'auto',false,false);
}

var addTime = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
    var salesid = $(sel).attr('data-salesId');
	item.find('[name=time_add]').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'sales_ajax_all.php?action=lead_time',
				data: { id: salesid, time: time+':00' },
                 success:function(response){
                    window.location.reload();
                }
			});
		}
	});
	item.find('[name=time_add]').timepicker('show');
}

/*var trackTime = function(sel) {
	var item = $(sel).closest('.info-block-detail,.standard-body-title');
    var salesid = $(sel).attr('data-salesId');
    item.find('.timer').timer('remove');
    item.find('.start').show();
    item.find('.stop').hide();
	item.find('.track_time').toggle();
}*/

function trackTime(sel) {
    var salesid = $(sel).attr('data-salesId');
    overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_timer.php?tile=sales&id='+salesid, 'auto', false, false);
}

var openProjectDialog = function(sel) {
    var salesid = sel.id;
    $('#dialog_choose_project').dialog({
        resizable: false,
        height: "auto",
        width: ($(window).width() <= 500 ? $(window).width() : 500),
        modal: true,
        buttons: {
			'Create New <?= PROJECT_NOUN ?>': function() {
				$.ajax({
					url: 'sales_ajax_all.php?fill=changeCustCat&salesid='+salesid,
					type: "GET",
					success: function(response) {
						location.replace('../Project/projects.php?edit=0&type=favourite&salesid='+salesid);
					}
				});
			},
            'Assign': function() {
                var projectid = $('select[name="projectid"] option:selected').val();
                $.ajax({
                    url: 'sales_ajax_all.php?fill=changeCustCat&salesid='+salesid+'&projectid='+projectid,
                    type: "GET",
                    success: function(response) {
                        location.replace('../Project/projects.php?edit='+projectid+'&tab=info&salesid='+salesid);
                    }
                });
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }
    });
}
var businessFilter = function(sel) {
    var business = sel.value;
    var dialog = $(sel).closest('.dialog');
    dialog.find('[name=clientid] option').each(function() {
        if($(this).data('business') != business && business > 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
    dialog.find('[name=clientid]').trigger('change.select2');
    dialog.find('[name=projectid] option').each(function() {
        if($(this).data('business') != business && business > 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
    dialog.find('[name=clientid]').trigger('change.select2');
}
$(document).on('change', '.dialog select[name=businessid]', function() { businessFilter(this); });
var contactFilter = function(sel) {
    var dialog = $(sel).closest('.dialog');
    var business = $(sel).find('option:selected').data('business');
    var contact = sel.value;
    dialog.find('[name=businessid]').val(business).trigger('change.select2');
    dialog.find('[name=projectid] option').each(function() {
        if($(this).data('client') != undefined && $(this).data('client').indexOf(','+contact+',') < 0 && contact > 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
    dialog.find('[name=clientid]').trigger('change.select2');
}
$(document).on('change', '.dialog select[name=clientid]', function() { contactFilter(this); });
</script>
<?php if(!isset($quick_actions)) {
	$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
} ?>
<input type='file' name='document' value='' style="display:none;">
<div class="select_users" style="display:none;">
    <select data-placeholder="Select Staff" multiple class="chosen-select-deselect">
    <?php foreach($staff_list as $staff) { ?>
        <option value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
    <?php } ?>
    </select>
    <button class="btn brand-btn pull-right send">Submit</button>
    <button class="btn brand-btn pull-right cancel">Cancel</button>
    <div class="clearfix"></div>
</div>
<div class="track_time" style="display:none;">
    <div class="col-sm-5">Track Time:</div>
    <div class="col-sm-3"><input type="text" name="timer" style="float:left;" class="form-control timer" placeholder="0 sec" /></div>
    <div class="col-sm-4">
        <button class="btn brand-btn pull-right start">Start</button>
        <button class="btn brand-btn pull-right stop" style="display:none;" data-salesId="<?php echo $_GET['id']?>">Stop</button>
        <button class="btn brand-btn pull-right cancel" onclick="trackTime(this);">Cancel</button>
    </div>
    <div class="clearfix"></div>
</div>
<input type="text" name="time_add" style="display:none; margin-top: 2em;" class="form-control timepicker">
<input type="text" name="time_track" class="datetimepicker form-control" style="display:none;">
<div class="gap-bottom action-icons">
    <?php if($project_security['edit'] > 0) { ?>
        <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="cursor-hand inline-img no-toggle" title="Assign To A <?= PROJECT_NOUN ?>" id="<?=$row['salesid']?>" onclick="openProjectDialog(this); return false;" /><?php
    } ?>
    <?php if($estimates_active > 0) { ?>
        <a href="<?= WEBSITE_URL; ?>/Sales/sale.php?p=details&id=<?= $row['salesid'] ?>&a=estimate#estimate"><img src="<?= WEBSITE_URL; ?>/img/icons/create_project.png" class="inline-img no-toggle" title="Add Estimate" /></a>
    <?php } ?>
    <?php if(in_array('reply',$quick_actions)) { ?>
        <a href="Add Note" onclick="saveNote(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reply-icon.png" class="inline-img no-toggle" title="Add Note" /></a>
    <?php } ?>
    <?php if(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions)) { ?>
        <a href="Flag This!" onclick="flagLead(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" title="Flag This!" /></a>
    <?php } ?>
    <?php if(in_array('flag_manual',$quick_actions)) { ?>
        <a href="Flag This!" onclick="flagLeadManual(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" title="Flag This!" /></a>
    <?php } ?>
    <?php if(in_array('attach',$quick_actions)) { ?>
        <a href="Attach File" onclick="addDocument(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle" title="Attach File" /></a>
    <?php } ?>
    <?php if(in_array('email',$quick_actions)) { ?>
        <a href="Send Email" onclick="sendEmail(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" class="inline-img no-toggle" title="Send Email" /></a>
    <?php } ?>
    <?php if(in_array('reminder',$quick_actions)) { ?>
        <a href="Schedule Reminder" onclick="setReminder(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" title="Schedule Reminder" /></a>
    <?php } ?>
    <?php if(in_array('time',$quick_actions)) { ?>
        <a href="Add Time" onclick="addTime(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-timer-icon.png" class="inline-img no-toggle" title="Add Time" /></a>
    <?php } ?>
    <?php if(in_array('timer',$quick_actions)) { ?>
        <a href="Track Time" onclick="trackTime(this); return false;" data-salesId="<?php echo $_GET['id']?>"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-timer2-icon.png" class="inline-img no-toggle" title="Track Time" /></a>
    <?php } ?>
    <a href="View History" onclick="viewHistory(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/eyeball.png" class="inline-img no-toggle" title="View History" /></a>
    <?php if(in_array('archive',$quick_actions)) { ?>
        <a href="#" id="sales_<?= $row['salesid']; ?>" data-salesid="<?= $row['salesid']; ?>" onclick="archive_sales_lead(this); $(this).closest('.info-block-detail,.standard-body-title').hide(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive" /></a>
    <?php } ?>
    <?= IFRAME_PAGE || $_GET['iframe_slider'] == 1 ? '<a href="../blank_loading_page.php"><img src="../img/icons/cancel.png" class="inline-img"></a>' : '' ?>
</div>
<div class="clearfix"></div>