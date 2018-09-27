<script>
$(document).ready(function() {
    /* Timer */
    $('.icons_div .start-timer-btn').on('click', function() {
    });

    $('.icons_div .stop-timer-btn').on('click', function() {
    });


    /* Timer */


	$('.icons_div .archive-icon').off('click').click(function() {
		var item = $(this).closest('.icons_div');
		var title = $(this).closest('.standard-body-title');
		$.ajax({
			url: 'projects_ajax.php?action=archive',
			method: 'POST',
			data: { id: item.data('id') },
            success: function(result) {
                alert('Project Archived');
            }
		});
		item.hide();
        title.find('h3').addClass('text-red');
	});

	$('.icons_div .email-icon').off('click').click(function() {
		// var item = $(this).closest('.dashboard-item,.standard-body-title');
		// var select = item.find('.select_users');
		// select.find('.cancel_button').off('click').click(function() {
		// 	select.find('select option:selected').removeAttr('selected');
		// 	select.hide();
		// 	return false;
		// });
		// select.find('.submit_button').off('click').click(function() {
		// 	if(select.find('select').val() != '' && confirm('Are you sure you want to send an e-mail to the selected user(s)?')) {
		// 		var users = [];
		// 		select.find('select option:selected').each(function() {
		// 			users.push(this.value);
		// 			$(this).removeAttr('selected');
		// 			select.find('select').trigger('change.select2');
		// 		});
		// 		$.ajax({
		// 			method: 'POST',
		// 			url: 'projects_ajax.php?action=quick_actions',
		// 			data: {
		// 				id: item.data('id'),
		// 				id_field: item.data('id-field'),
		// 				table: item.data('table'),
		// 				field: 'email',
		// 				value: users
		// 			},
		// 			success: function(result) {
		// 				select.hide();
		// 				select.find('select').trigger('change.select2');
		// 				item.find('h4').append(result);
		// 			}
		// 		});
		// 	}
		// 	return false;
		// });
		// select.show();
	});

	$('.icons_div .attach-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table','project_document');
			fileData.append('folder','download');
			fileData.append('id',item.data('id'));
			fileData.append('id_field','ticketid');
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "projects_ajax.php?action=quick_actions",
				data: fileData
			});
            $(this).hide().val('');
		}).click();
	});

    $('.icons_div .timer-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_timer.php?tile=projects&id='+item.data('id'), 'auto', false, true);
    });

    $('.icons_div .reminder-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=projects&id='+item.data('id'), 'auto', false, true);
    });

    $('.icons_div .flag-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item,.standard-body-title');
        $.ajax({
            url: 'projects_ajax.php?action=flag_colour',
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

    $('.icons_div .manual-flag-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        $('.flag_target').removeClass('flag_target');
        $(item).closest('.dashboard-item,.standard-body-title').addClass('flag_target');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=projects&id='+item.data('id'), 'auto', false, true);
    });
});

function saveNote(sel) {
    var projectid = $(sel).data('projectid');
    var note = sel.value;
    if (note!='') {
        $.ajax({
            url: 'projects_ajax.php?action=saveNote&projectid='+projectid+'&note='+note,
            success: function(response) {
               alert("Note saved.");
            }
        });
    }
}

</script>
<?php $quick_actions = explode(',',get_config($dbc, 'quick_action_icons')); ?>
<!-- All icons -->
<div class="icons_div" data-id="<?= $project['projectid'] ?>">
    <div class="action-icons">
        <!-- Status Report Icon -->
        <?php if($status_report) { ?>
            <a href="Status Report" onclick="overlayIFrameSlider('edit_project_scope_status_report.php?projectid='+id,'auto',true,true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/pie-chart.png" class="inline-img no-toggle" title="Status Report"></a>
        <?php } ?>
        <!-- Status Report Icon -->
        <?php if(!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions)) { ?>
            <a href="Flag This!" onclick="return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img no-toggle flag-icon" title="Flag This!" /></a>
        <?php } ?>

        <?php if(in_array('flag_manual',$quick_actions)) { ?>
            <a href="Flag This!" onclick="return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img no-toggle manual-flag-icon" title="Flag This!" /></a>
        <?php } ?>

        <?php if(in_array('reply',$quick_actions)) { ?>
            <a href="#" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=projects&id=<?= $projectid ?>','auto', false, true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reply-icon.png" class="inline-img no-toggle reply-icon" title="Add Note" /></a>
            <!-- Note -->
        <?php } ?>

        <?php if(in_array('email',$quick_actions)) { ?>
            <!-- Email -->
            <a href="Add Email" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=projects&id='+id,'auto',false,true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" class="inline-img no-toggle email-icon" title="Send Email"></a>
            <!-- Email -->
        <?php } ?>

        <?php if(in_array('reminder',$quick_actions)) { ?>
            <a href="Add Reminder" onclick="return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle reminder-icon" title="Schedule Reminder"></a>
            <!-- reminder -->
        <?php } ?>

        <?php if(in_array('attach',$quick_actions)) { ?>
            <a href="Add File" onclick="return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" class="inline-img no-toggle attach-icon" title="Attach File"></a>
            <!-- document -->
        <?php } ?>

        <?php if(in_array('timer',$quick_actions)) { ?>
            <a href="Add Timer" onclick="return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-timer2-icon.png" class="inline-img no-toggle timer-icon" title="Start Timer" /></a>
            <!-- Timer -->
        <?php } ?>

        <?php if(in_array('archive',$quick_actions)) { ?>
            <!-- archive -->
            <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle archive-icon" title="Archive">
            <!-- archive -->
        <?php } ?>
     </div>
</div>
 <!-- All icons -->
 
<div class="clearfix"></div>
<!-- Timer -->
<div class="timer" style="display:none;">
    <input type="text" name="timer_<?= $projectid ?>" id="timer_value" class="form-control timer" placeholder="0 sec" />
    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $projectid ?>">Stop</a><br />
    <input type="hidden" value="" name="track_time" />
    <span class="added-time"></span>
</div>
<!-- Timer -->

<!-- Note -->
<input type="text" class="form-control gap-top" name="notes" id="notes" value="" style="display:none;" data-table="project_comment" data-projectid="<?= $projectid; ?>" onkeypress="javascript:if(event.keyCode==13){ saveNote(this); $(this).val('').hide(); };" onblur="saveNote(this); $(this).val('').hide();">

<!-- reminder -->
<input type='text' name='reminder' value='' class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;">
<div class="select_users" style="display:none;">
    <select data-placeholder="Select Staff" multiple class="chosen-select-deselect"><option></option>
    <?php foreach($staff_list as $staff) { ?>
        <option value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
    <?php } ?>
    </select>
    <button class="submit_button btn brand-btn pull-right">Submit</button>
    <button class="cancel_button btn brand-btn pull-right">Cancel</button>
</div>

<!-- document -->
<input type='file' name='document' value='' style="display:none;">