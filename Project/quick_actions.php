<script>

    /* Timer */
    $('.start-timer-btn').on('click', function() {
        $(this).closest('div').find('.timer').timer({
            editable: true
        });
        $(this).addClass('hidden');
        $(this).next('.stop-timer-btn').removeClass('hidden');
    });

    $('.stop-timer-btn').on('click', function() {
		$(this).closest('div').find('.timer').timer('stop');
		$(this).addClass('hidden');
		$('#timer_value').addClass('hidden');


		//$(this).prev('.start-timer-btn').removeClass('hidden');

        var projectid = $(this).data('id');

        var timer_value = $(this).closest('div').find('#timer_value').val();

		$(this).closest('div').find('.timer').timer('remove');

		if ( projectid!='' && typeof projectid!='undefined' && timer_value!='' ) {
            $.ajax({
                type: "GET",
                url: "projects_ajax.php?action=timer&projectid="+projectid+"&timer_value="+timer_value,
                dataType: "html",
                success: function(response) {
                    alert('Time added');
                }
            });
        }
    });


    /* Timer */


	$('.archive-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
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

	$('.email-icon').off('click').click(function() {
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

	$('.attach-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item,.standard-body-title');
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

$('.reminder-icon').off('click').click(function() {
    var item = $(this).closest('.dashboard-item,.standard-body-title');
    item.find('[name=reminder]').change(function() {
        var reminder = $(this).val();
        var select = item.find('.select_users');
        select.find('.cancel_button').off('click').click(function() {
            select.find('select option:selected').removeAttr('selected');
            select.find('select').trigger('change.select2');
            select.hide();
            return false;
        });
        select.find('.submit_button').off('click').click(function() {
            if(select.find('select').val() != '' && confirm('Are you sure you want to schedule reminders for the selected user(s)?')) {
                var users = [];
                select.find('select option:selected').each(function() {
                    users.push(this.value);
                    $(this).removeAttr('selected');
                });
                $.ajax({
                    method: 'POST',
                    url: 'projects_ajax.php?action=quick_actions',
                    data: {
                        id: item.data('id'),
                        id_field: item.data('id-field'),
                        table: item.data('table'),
                        field: 'reminder',
                        value: reminder,
                        users: users,
                        ref_id: item.data('id'),
                        ref_id_field: item.data('id-field')
                    },
                    success: function(result) {
                        select.hide();
                        select.find('select').trigger('change.select2');
                        item.find('h4').append(result);
                        alert("Reminder set");
                    }
                });
            }
            return false;
        });
        select.show();
    }).focus();
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
<!-- All icons -->
<div class="action-icons">
    <!-- Status Report Icon -->
    <?php if($status_report) { ?>
        <a href="Status Report" onclick="overlayIFrameSlider('edit_project_scope_status_report.php?projectid='+id,'auto',true,true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/pie-chart.png" class="inline-img no-toggle" title="Status Report"></a>
    <?php } ?>
    <!-- Status Report Icon -->
    <!-- Email -->
    <a href="Add Email" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=projects&id='+id,'auto',false,true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email"></a>
    <!-- Email -->

    <!--<a href="Add Note" onclick="$(this).closest('.dashboard-item').find('[name=notes]').show().focus(); return false;">--><a href="#" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=projects&id=<?= $projectid ?>','auto', false, true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Add Note" /></a>
    <!-- Note -->

     <a href="Add Reminder" onclick="$(this).closest('.dashboard-item,.standard-body-title').find('[name=reminder]').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon" title="Schedule Reminder"></a>
    <!-- reminder -->

    <a href="Add Reminder" onclick="$(this).closest('.dashboard-item,.standard-body-title').find('[name=document]').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File"></a>
    <!-- document -->

    <a href="Add Timer" onclick="$(this).closest('.dashboard-item,.standard-body-title').find('.timer').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-timer2-icon.png" class="inline-img timer-icon" title="Start Timer" /></a>
    <!-- Timer -->

    <!-- archive -->
    <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" class="inline-img archive-icon" title="Archive">
    <!-- archive -->
 </div>
 <!-- All icons -->
 
<br /><div class="clearfix"></div>
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