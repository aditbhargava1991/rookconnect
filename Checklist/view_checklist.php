<?php include_once('../include.php');
$security = get_security($dbc, 'checklist');
$link = '?edit=';
if($_GET['tab'] == 'checklists' && $_GET['status'] == 'project') {
	$link = '?'.http_build_query($_GET).'&checklistid=';
	$_GET['edit'] = $_GET['checklistid'];
}
$override_block = $_GET['override_block'];
$hide_header = $_GET['hide_header'];
$diff_function = '';
if($_GET['different_function_name'] == 'true') {
	$diff_function = 'cl_';
}
?>
<script type="text/javascript" src="../Checklist/checklist.js"></script>
<script>
setTimeout(function() {

var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
    return $( this ).outerWidth( true );
}).get() );

var maxHeight = -1;

/*
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
		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important; background-color: #fff;');
	<?php } else { ?>
        $(this).attr('style', 'height:'+maxHeight+'px !important; background-color: #fff;');
	<?PHP } ?>
});
*/
}, 200);

$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
    $(window).resize(function() {
        $('.checklist_screen').css('overflow-y','hidden');
        $('.standard-body-content').outerHeight($(window).height() - $('.has-main-screen').offset().top - $('footer').outerHeight() - $('.standard-body-title').height());
        $('.standard-body-content').css('overflow-y','auto');
	}).resize();
});

function <?= $diff_function ?>choose_user(target, type, id, date) {
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
function <?= $diff_function ?>send_alert(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	<?= $diff_function ?>choose_user('alert', type, checklist_id);
}
function <?= $diff_function ?>send_email(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=checklists&id='+checklist_id+'&type='+type, 'auto', false, true);
}
function <?= $diff_function ?>send_reminder(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
			<?= $diff_function ?>choose_user('reminder', type, checklist_id, date);
		}
	});
}
function <?= $diff_function ?>send_reply(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
	var type = 'checklist';
	if(checklist_id.toString().substring(0,5) == 'BOARD') {
		var type = 'checklist board';
		checklist_id = checklist_id.substring(5);
	}
    
    overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=checklists&id='+checklist_id, 'auto', false, true);
    
	/* Function before notes slider
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
			var save_reply = reply + " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> [PROFILE <?= $_SESSION['contactid'] ?>] at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: '../Checklist/checklist_ajax.php?fill=checklistreply',
				data: { id: checklist_id, reply: save_reply },
				complete: function(result) { reloadChecklistScreen($(checklist).closest('.checklist_screen')); }
			})
		}
	}); */
}
function <?= $diff_function ?>edit_item(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
			complete: function(result) { reloadChecklistScreen($(checklist).closest('.checklist_screen')); }
		})
	});
}
function <?= $diff_function ?>add_time(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
function <?= $diff_function ?>attach_file(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
				reloadChecklistScreen($(checklist).closest('.checklist_screen'));
			}
		});
	});
	$('[name='+file_id+']').click();
}
function <?= $diff_function ?>manual_flag_item(checklist) {
	var item = $(checklist).closest('li');
	item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').show();
	item.find('[name=flag_cancel]').off('click').click(function() {
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		return false;
	});
	item.find('[name=flag_off]').off('click').click(function() {
		item.find('[name=colour]').val('FFFFFF');
		item.find('[name=label]').val('');
		item.find('[name=flag_start]').val('');
		item.find('[name=flag_end]').val('');
		item.find('[name=flag_it]').click();
		return false;
	});
	item.find('[name=flag_it]').off('click').click(function() {
		$.ajax({
			url: '../Checklist/checklist_ajax.php?fill=checklistflagmanual',
			method: 'POST',
			data: {
				value: item.find('[name=colour]').val(),
				label: item.find('[name=label]').val(),
				start: item.find('[name=flag_start]').val(),
				end: item.find('[name=flag_end]').val(),
				id: item.find('[data-checklist]').data('checklist')
			}
		});
		item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
		item.data('colour',item.find('[name=colour]').val());
		item.css('background-color','#'+item.find('[name=colour]').val());
		item.find('.flag-label').text(item.find('[name=label]').val());
		return false;
	});
}
function <?= $diff_function ?>flag_item(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
function <?= $diff_function ?>archive(checklist) {
	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
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
				reloadChecklistScreen($(checklist).closest('.checklist_screen'));
				//console.log(response.responseText);
			}
		});
	}
	else if(confirm("Are you sure you want to archive this checklist?")) {
		if($('[name="from_tile"]').val() == 'tickets') {
			$.ajax({
				type: "GET",
				url: "../Checklist/checklist_ajax.php?fill=delete_checklist_board&remove_checklist=all&subtab="+'<?= $_GET['subtabid'] ?>'+"&checklistid=" + checklist_id,
				dataType: "html",
				success:function(response){
					$(checklist).closest('.checklist_screen').remove();
				}
			});
		} else {
			window.location = "<?php echo WEBSITE_URL; ?>/delete_restore.php?action=delete&remove_checklist=all&subtab="+'<?= $_GET['subtabid'] ?>'+"&checklistid=" + checklist_id;
		}
	}
}
function <?= $diff_function ?>export_pdf(checklist) {
	checklist_id = $(checklist).parents('span').data('checklist').substring(5);
	var type = 'checklist board';
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Checklist/checklist_ajax.php?fill=export_pdf&checklistid="+checklist_id,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(response != 'NO ID') {
				window.open(response, '_blank');
			}
		}
	});
}
function <?= $diff_function ?>mark_favourite(button) {
	if(button.src.includes('full')) {
		button.src = '<?= WEBSITE_URL ?>/img/blank_favourite.png';
	} else {
		button.src = '<?= WEBSITE_URL ?>/img/full_favourite.png';
	}
	$.ajax({
		type: "GET",
		url: "../Checklist/checklist_ajax.php?fill=mark_favourite&checklistid="+$(button).data('id')+"&status="+button.src.includes('full'),
		dataType: "html",
		success: function(response) {
			reloadChecklistScreen($(checklist).closest('.checklist_screen'));
		}
	});
}
function <?= $diff_function ?>flag_item_manual(checklist) {
       	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
		var type = 'checklist';
		if(checklist_id.toString().substring(0,5) == 'BOARD') {
			checklist_id = checklist_id.substring(5);
		}

       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=checklist_name&id='+checklist_id, 'auto', false, true);
}
function <?= $diff_function ?>common_flag_item_manual(checklist) {
       	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
		var type = 'checklist';
		if(checklist_id.toString().substring(0,5) == 'BOARD') {
			checklist_id = checklist_id.substring(5);
		}

       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=common_checklist_flag&id='+checklist_id, 'auto', false, true);
}
function setManualFlag(tasklistid, colour, label) {
    window.location.reload();
	//var item = $('.dashboard-item[data-id="'+tasklistid+'"]');
	//item.data('colour',colour);
	//item.css('background-color','#'+colour);
	//item.find('.flag-label').text(label);
}
function <?= $diff_function ?>send_checklist_reminder(checklist) {
       	checklist_id = $(checklist).closest('[data-checklist]').data('checklist');
		var type = 'checklist';
		if(checklist_id.toString().substring(0,5) == 'BOARD') {
			var type = 'checklist board';
			checklist_id = checklist_id.substring(5);
		}
       overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=checklist&id='+checklist_id, 'auto', false, true);
}
</script>
<?php $checklistid = $_GET['view'];
$checklist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid`='$checklistid'"));
if(!in_array($_SESSION['contactid'],explode(',',$checklist['assign_staff'])) && $checklist['assign_staff'] != ',ALL,' && $_GET['status'] != 'project' && $override_block != 'true') {
	die("<script> alert('BLOCKED! - ".$checklist['assign_staff']."'); window.location.replace('?'); </script>");
}

$quick_actions = explode(',',get_config($dbc, 'quick_action_icons')); ?>

<form name="form_sites1" method="post" action="" class="form-inline" role="form" <?= ($checklist['flag_colour'] == '' ? '' : 'style="background-color: #'.$checklist['flag_colour'].';"') ?>>
<?php if($hide_header != 'true') { ?>
    <div class="standard-body-title">
        <h3>
            <div class="pull-left">
                <div class="pull-left id-circle-other pad-top pad-left">
                    <?= $checklist['checklist_name']; ?>
                    <small style="display:block; margin-top:5px;"><?= ucwords($checklist['checklist_type']); ?></small>
                </div>
                <div class="pull-left mobile-offset-top-10"><?php
                    foreach(array_filter(array_unique(explode(',',$checklist['assign_staff']))) as $assigned_staff) {
                        if($assigned_staff == 'ALL') {
                            echo '<div class="pull-left id-circle" style="background-color:#6DCFF6">All</div>';
                        } else {
                            profile_id($dbc, $assigned_staff);
                        }
                    } ?>
                </div>
            </div>
            <span class="pull-right hide-on-mobile" data-checklist="BOARD<?= $checklistid; ?>">
                <div class="pull-left cursor-hand id-circle-other offset-top-5 gap-right"><img src="<?= WEBSITE_URL ?>/img/<?= (in_array($checklist['checklistid'],explode(',',$user_settings['checklist_fav'])) ? 'full' : 'blank') ?>_favourite.png" onclick="<?= $diff_function ?>mark_favourite(this);" data-id="<?= $checklist['checklistid'] ?>" class="inline-img no-toggle" title="Favourite" /></div>
                <?php if(in_array('flag',$quick_actions)) { ?><span class="header-icon no-toggle" title="Flag This!" onclick="<?= $diff_function ?>flag_item(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img" /></span><?php } ?>
                <?php if(in_array('flag_manual',$quick_actions)) { ?><span class="header-icon no-toggle" title="Flag This!" onclick="<?php echo $diff_function;?>common_flag_item_manual(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" class="inline-img" /></span><?php } ?>
                <?php /* if(in_array('alert',$quick_actions)) { ?><span class="header-icon no-toggle" title="Send Alert" onclick="<?= $diff_function ?>send_alert(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" class="inline-img" /></span><?php } */ ?>
                <?php if(in_array('email',$quick_actions)) { ?><span class="header-icon no-toggle" title="Send Email" onclick="<?= $diff_function ?>send_email(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" class="inline-img" /></span><?php } ?>
                <?php if(in_array('reminder',$quick_actions)) { ?><span class="header-icon no-toggle" title="Schedule Reminder" onclick="<?= $diff_function ?>send_checklist_reminder(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" class="inline-img" /></span><?php } ?>
                <?php if(in_array('attach',$quick_actions)) { ?><span class="header-icon no-toggle" title="Attach File" onclick="<?= $diff_function ?>attach_file(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" class="inline-img" /></span><?php } ?>
                <span class="header-icon no-toggle" title="Download Checklist" onclick="<?= $diff_function ?>export_pdf(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-download-icon.png" class="inline-img" /></span>
                <span class="header-icon no-toggle" title="Share Checklist" onclick="overlayIFrameSlider('../Checklist/share_staff.php?edit=<?= $_GET['view'] ?>', 'auto', false, false, 'auto', true);"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-share-icon.png" class="inline-img" /></span>
                <?php if($security['edit'] > 0 && in_array('archive',$quick_actions)) { ?><span class="header-icon no-toggle" title="Archive Checklist" onclick="<?= $diff_function ?>archive(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" class="inline-img" /></span><?php }
                /*
                if(trim($checklist['assign_staff'],',') == $_SESSION['contactid']) { ?>
                    <span class="block-label" style="margin: 0.25em;">Private</span><?php
                } */ ?>
                <?php if($security['edit'] > 0) { ?><a class="header-icon no-toggle" title="Checklist Settings"" onclick="overlayIFrameSlider('edit_checklist.php<?= $link.$_GET['view'] ?>', 'auto', false, false);"><img src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="inline-img" width="25" /></a><?php } ?>
                <br />
                <input type="text" name="reminder_board_<?php echo $checklistid; ?>" style="display:none; margin-top:2em;" class="form-control datepicker" />
            </span>
            <div class="clearfix"></div>
        </h3>
        <div class="clearfix"></div>
    </div>
<?php } ?>

<?php
$reset_time = date('H:i:s', strtotime($checklist['reset_time']));
$reset_date = '';
if($checklist['checklist_type'] != 'ongoing') {
	$reset_date = '';
	if($reset_time > date('h:i:s')) {
		$reset = 'past';
	} else {
		$reset = 'last';
	}
	switch($checklist['checklist_type']) {
	case 'daily':
		$reset_date = date('Y-m-').($reset == 'past' ? date('d') : date('d') - 1).' '.$reset_time;
		break;
	case 'weekly':
		$current_day_of_week = date('w');
		if($current_day_of_week == $checklist['reset_day'] && $reset == 'past') {
			$reset_date = date('Y-m-d ').$reset_time;
		} else {
			$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
			$reset_date = date('Y-m-d ', strtotime('Last '.$weekdays[$checklist['reset_day']])).$reset_time;
		}
		break;
	case 'monthly':
		if(date('d') == $checklist['reset_day'] && $reset == 'past') {
			$reset_date = date('Y-m-d ').$reset_time;
		} else {
			$day = date('d');
			$month = date('m');
			if($day < $checklist['reset_day']) {
				$month--;
			}
			$reset_date = date('Y-m-d ', strtotime(date("Y-$month-$day"))).$reset_time;
		}
		break;
	}
	mysqli_query($dbc, "UPDATE `checklist_name` SET `checked`=0 WHERE `time_checked` < '$reset_date' AND `checklistid` = '$checklistid' AND `deleted`=0");
}

echo '<div class="clearfix"></div>';

$query_check_credentials = "SELECT * FROM checklist_document WHERE checklistid='$checklistid' AND `deleted`=0 ORDER BY checklistdocid DESC";
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) { ?>
	<script>
	function archive_checklist_document(a) {
		$.ajax({
			method: 'POST',
			url: '../Checklist/checklist_ajax.php?fill=checklist_doc_remove',
			data: { doc: $(a).data('docid')},
			success: function() {
				$(a).closest('tr').remove();
			}
		});
	}
	</script>
	<?php echo "<table class='table table-bordered' style='width:100%;'>
	<tr class='hidden-xs hidden-sm'>
	<th>Document / Link / Note</th>
	<th>Date</th>
	<th>Added By</th>
	<th>Function</th>
	</tr>";
	while($row = mysqli_fetch_array($result)) {
		echo '<tr>';
		$by = $row['created_by'];
		echo '<td data-title="Document / Link / Note">';
		if($row['link'] != '') {
			echo '<a href="'.(strpos($row['link'],'http') === FALSE ? 'http://' : '').$row['link'].'" target="_blank">'.$row['link'].'</a>';
		} else if($row['document'] != '') {
			echo '<a href="../Checklist/download/'.$row['document'].'" target="_blank">'.$row['document'].'</a>';
		} else {
			echo html_entity_decode($row['notes']);
		}
		echo '</td>';
		echo '<td data-title="Date">'.$row['created_date'].'</td>';
		echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
		echo '<td data-title="Function">'.($security['edit'] > 0 ? '<a href="" data-docid="'.$row['checklistdocid'].'" onclick="archive_checklist_document(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;"></a>' : '').'</td>';
		echo '</tr>';
	}
	echo '</table>';
} ?>

    <div class="standard-body-content" style="margin-top:-1px">
        <input type="file" name="attach_board_<?= $checklistid; ?>" style="display:none;" />
        <input type="hidden" name="checklistid" value="<?= $checklistid ?>" />
        <input type="hidden" name="from_tile" value="<?= $_GET['from_tile'] ?>" /><?php
        echo '<ul id="sortable'.$i.'" class="connectedChecklist full-width">';
			if($security['edit'] > 0) {
				echo '<li class="new_task_box no-sort">
					<div class="col-sm-1"></div>
					<div class="col-sm-10"><input onChange="changeEndAme(this)" name="add_checklist" placeholder="Add new item to the checklist..." id="add_new_task '.$checklistid.'" type="text" class="form-control" /></div>
					<div class="clearfix"></div>
				</li>';
			}

            $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND ((checked = 0 AND '".(count(array_filter(explode(',',$checklist['ticketid']))) > 1 ? $_GET['ticketid'] : '')."' = '') OR (CONCAT(',',IFNULL(ticket_checked,''),',') NOT LIKE '%,".$_GET['ticketid'].",%') AND '".$_GET['ticketid']."' != '') AND deleted = 0 ORDER BY priority");

            $first_class = 1;
			$colours = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
            while($row = mysqli_fetch_array( $result )) {
				if(strtotime($row['flag_start']) > time() || strtotime($row['flag_end'].' + 1 day') < time()) {
					$row['flag_colour'] = '';
				}
                echo '<li id="'.$row['checklistnameid'].'" class="ui-state-default '. ($first_class==1 ? 'ui-state-default-first' : '') .'" '.($row['flag_colour'] == '' ? '' : 'style="background-color: #'.$row['flag_colour'].';"').'>';
                $first_class=0;
                    echo '<span class="">
                        <!-- <div class="col-sm-1 col-xs-2 middle-valign text-center"><input title="Complete" type="checkbox" onclick="checklistChange(this);" data-ticket="'.(count(array_filter(explode(',',$checklist['ticketid']))) > 1 ? $_GET['ticketid'] : '').'" value="'.$row['checklistnameid'].'" name="checklistnameid[]" /></div> -->';

                        echo '<span class="col-sm-12 middle-valign" data-checklist="'.$row['checklistnameid'].'">';
							if(in_array('flag_manual',$quick_actions)) { ?>
								<span class="col-sm-3 text-center flag_field_labels" style="display:none;">Label</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Colour</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Start Date</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">End Date</span>
								<div class="col-sm-3"><input type='text' name='label' value='<?= $row['flag_label'] ?>' class="form-control" style="display:none;"></div>
								<div class="col-sm-3"><select name='colour' class="form-control" style="display:none;background-color:#<?= $row['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
										<option value="FFFFFF" style="background-color:#FFFFFF;">No Flag</option>
										<?php foreach($colours as $flag_colour) { ?>
											<option <?= $row['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"></option>
										<?php } ?>
									</select></div>
								<div class="col-sm-3"><input type='text' name='flag_start' value='<?= $row['flag_start'] ?>' class="form-control datepicker" style="display:none;"></div>
								<div class="col-sm-3"><input type='text' name='flag_end' value='<?= $row['flag_end'] ?>' class="form-control datepicker" style="display:none;"></div>
								<button class="btn brand-btn pull-right" name="flag_it" onclick="return false;" style="display:none;">Flag This</button>
								<button class="btn brand-btn pull-right" name="flag_cancel" onclick="return false;" style="display:none;">Cancel</button>
								<button class="btn brand-btn pull-right" name="flag_off" onclick="return false;" style="display:none;">Remove Flag</button>
							<?php }
                            $checklist_title = explode('<p>',html_entity_decode($row['checklist']))[0];
                            echo '<span class="display-field col-sm-12">
                                <span class="col-xs-12 col-sm-11">
                                    <input title="Complete" type="checkbox" onclick="checklistChange(this);" data-ticket="'.(count(array_filter(explode(',',$checklist['ticketid']))) > 1 ? $_GET['ticketid'] : '').'" value="'.$row['checklistnameid'].'" name="checklistnameid[]" />&nbsp;
                                    <input type="text" name="edit_'.$row['checklistnameid'].'" style="display:none;" class="form-control pull-left" value="'.$checklist_title.'" />';
                                    echo '<label class="large-checkbox-label">#'.$row['checklistnameid'].': '.$checklist_title .'<span class="no-toggle offset-left-5" title="Edit" onclick="'.$diff_function.'edit_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" style="height:100%;" onclick="return false;" class="inline-img cursor-hand"></span>'. preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode(str_replace($checklist_title, '', $row['checklist'])));
                                    echo $security['edit'] > 0 && in_array('edit',$quick_actions) ? '</label>' : '';
                                echo '</span>';
                                echo '<span class="col-sm-1">';
                                    echo '<span class="middle-valign drag_handle-container pull-right"><img class="drag_handle no-toggle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="margin:0.25em; height:1.25em; width:1.25em;" title="Drag" /></span>';
                                echo '</span>';
                            echo '</span>';
							foreach(explode(',',$row['alerts_enabled']) as $alertid) {
								if($alertid > 0) {
									echo '<span class="pull-left small">';
									profile_id($dbc, $alertid);
									echo ' Assigned to '.get_contact($dbc, $alertid).'</span>';
								}
							}
                        echo '</span>';

                        echo '<span class="col-sm-12 gap-top" data-checklist="'.$row['checklistnameid'].'">
							<span class="action-icons">';
								//echo $security['edit'] > 0 && in_array('edit',$quick_actions) ? '<span class="" title="Edit" onclick="'.$diff_function.'edit_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-edit-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('flag_manual',$quick_actions) ? '<span class="no-toggle" title="Flag This!" onclick="'.$diff_function.'flag_item_manual(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo !in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<span class="no-toggle" title="Flag This!" onclick="'.$diff_function.'flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('reply',$quick_actions) ? '<span class="no-toggle" title="Add Note" onclick="'.$diff_function.'send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('attach',$quick_actions) ? '<span class="no-toggle" title="Attach File" onclick="'.$diff_function.'attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('alert',$quick_actions) ? '<span class="no-toggle" title="Send Alert" onclick="'.$diff_function.'send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('email',$quick_actions) ? '<span class="no-toggle" title="Send Email" onclick="'.$diff_function.'send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo in_array('reminder',$quick_actions) ? '<span class="no-toggle" title="Schedule Reminder" onclick="'.$diff_function.'send_checklist_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>': '';
								echo in_array('time',$quick_actions) ? '<span class="no-toggle" title="Add Time" onclick="'.$diff_function.'add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								echo $security['edit'] > 0 && in_array('archive',$quick_actions) ? '<span class="no-toggle" title="Archive Item" onclick="'.$diff_function.'archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
								//echo '<span class=" middle-valign text-center drag_handle-container"><img class="drag_handle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="margin:0.25em; height:1.25em; width:1.25em;" /></span>';
								echo '<div class="clearfix"></div>';
							echo '</span>';
							echo '<div class="clearfix"></div>';

                            $documents = mysqli_query($dbc, "SELECT * FROM checklist_name_document WHERE checklistnameid='".$row['checklistnameid']."' AND `deleted`=0");
                            while($doc = mysqli_fetch_array($documents)) {
                                echo '<div class="double-gap-top offset-left-5 gap-bottom"><a href="../Checklist/download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a></div>';
                            }
                            
                            echo '<input type="text" name="reply_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
                            echo '<input type="text" name="checklist_time_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
                            echo '<input type="text" name="reminder_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker reminder" onfocusout="$(this).hide();" />';
                            echo '<input type="file" name="attach_'.$row['checklistnameid'].'" style="display:none;" class="form-control" />';
						echo '</span>';
						echo '<div class="clearfix"></div>';
                    // echo '<div class="col-sm-1 middle-valign text-center drag_handle-container"></div>';
                echo '</li>';
            }

			if($security['edit'] > 0) {
				echo '<li class="new_task_box no-sort">
					<div class="col-sm-1"></div>
					<div class="col-sm-10"><input onChange="changeEndAme(this)" name="add_checklist" placeholder="Add new item to the checklist..." id="add_new_task '.$checklistid.'" type="text" class="form-control" /></div>
					<div class="clearfix"></div>
				</li>';
			}

        echo '</ul>';


        $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND ((checked = 1 AND '".(count(array_filter(explode(',',$checklist['ticketid']))) > 1 ? $_GET['ticketid'] : '')."' = '') OR (CONCAT(',',IFNULL(ticket_checked,''),',') LIKE '%,".$_GET['ticketid'].",%' AND '".$_GET['ticketid']."' != '')) AND deleted = 0 ORDER BY `time_checked`");
        if ( $result->num_rows > 0 ) {
            echo '<div class="clearfix double-gap-top"></div>';

            echo '<h4 class="connectedChecklistTitle full-width pad-left">Completed</h4>';
            echo '<ul class="connectedChecklist full-width border-bottom-none double-gap-bottom">';
                while($row = mysqli_fetch_array( $result )) {
                    $info = ' : '.$row['updated_date']. ' : '.$row['updated_by'];
                    echo '<li id="'.$row['checklistnameid'].'" title="Incomplete" class="ui-state-default no-sort">';
                        echo '<div class="col-sm-1 col-xs-2 middle-valign text-center"><input type="checkbox" onclick="checklistChange(this);" data-ticket="'.(count(array_filter(explode(',',$checklist['ticketid']))) > 1 ? $_GET['ticketid'] : '').'" checked value="'.$row['checklistnameid'].'" name="checklistnameid[]"></div>';
                        echo '<div class="col-sm-11 middle-valign">#'.$row['checklistnameid'].': '.preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($row['checklist'])) . $info;
                            $documents = mysqli_query($dbc, "SELECT * FROM checklist_name_document WHERE checklistnameid='".$row['checklistnameid']."' AND `deleted`=0");
                            while($doc = mysqli_fetch_array($documents)) {
                                echo '<br /><a href="../Checklist/download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a>';
                            }
                        echo '</div>';
                    echo '</li>';
                }
            echo '</ul>';
        } ?>
    </div>

</form>