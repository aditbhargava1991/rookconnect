<?php
error_reporting(0);
include_once('../include.php');
$folder = FOLDER_NAME;
?>
<script>
function saveQuickIcon() {
	var tab_list = [];
	$('[name="contact_quick_action_icons[]"]:checked').not(':disabled').each(function() {
		tab_list.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "../Contacts/contacts_ajax.php?action=setting_quick_icon&tab_list="+tab_list,
		dataType: "html",   //expect html to be returned
	});
}
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Security:</h3>
</div>
<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">

        <form class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">Enable Quick Action Icons</label>
                <div class="col-sm-8">
                    <?php $contact_quick_action_icons = explode(',',get_config($dbc, 'contact_quick_action_icons')); ?>

                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('flag',$contact_quick_action_icons) ? 'checked' : '' ?> value="flag"> <img class="inline-img" src="../img/icons/color-wheel.png"> Highlight</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('flag_manual',$contact_quick_action_icons) ? 'checked' : '' ?> value="flag_manual"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Manually Flag with Label</label>

                        <!--<label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('reply',$contact_quick_action_icons) ? 'checked' : '' ?> value="reply"> <img class="inline-img" src="../img/icons/ROOK-reply-icon.png"> Reply</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('attach',$contact_quick_action_icons) ? 'checked' : '' ?> value="attach"> <img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"> Attach</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('alert',$contact_quick_action_icons) ? 'checked' : '' ?> value="alert"> <img class="inline-img" src="../img/icons/ROOK-alert-icon.png"> Alerts</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('email',$contact_quick_action_icons) ? 'checked' : '' ?> value="email"> <img class="inline-img" src="../img/icons/ROOK-email-icon.png"> Email</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('reminder',$contact_quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('time',$contact_quick_action_icons) ? 'checked' : '' ?> value="time"> <img class="inline-img" src="../img/icons/ROOK-timer-icon.png"> Add Time</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('timer',$contact_quick_action_icons) ? 'checked' : '' ?> value="timer"> <img class="inline-img" src="../img/icons/ROOK-timer2-icon.png"> Track Time</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('archive',$contact_quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/trash-icon-red.png"> Archive</label>
                        <label class="form-checkbox"><input type="checkbox" onchange="saveQuickIcon(this)" name="contact_quick_action_icons[]" <?= in_array('hide_all',$contact_quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=contact_quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
                        -->

                </div>
            </div>
        </form>

    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_additions.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>