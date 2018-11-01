<?php
if (isset($_POST['add_tickets'])) {
    $calendar_ticket_hover_staff = filter_var($_POST['calendar_ticket_hover_staff'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_hover_staff', $calendar_ticket_hover_staff);

    $calendar_ticket_diff_label = filter_var($_POST['calendar_ticket_diff_label'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_diff_label', $calendar_ticket_diff_label);

    $calendar_ticket_label = filter_var($_POST['calendar_ticket_label'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_label', $calendar_ticket_label);

    $calendar_ticket_check_shifts = filter_var($_POST['calendar_ticket_check_shifts'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_check_shifts', $calendar_ticket_check_shifts);

    $calendar_ticket_check_days_off = filter_var($_POST['calendar_ticket_check_days_off'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_check_days_off', $calendar_ticket_check_days_off);
    $calendar_ticket_color_code_tabs = filter_var($_POST['calendar_ticket_color_code_tabs'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_color_code_tabs', $calendar_ticket_color_code_tabs);
    
    $calendar_ticket_slider = filter_var($_POST['calendar_ticket_slider'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_slider', $calendar_ticket_slider);

    $calendar_checkmark_tickets = filter_var($_POST['calendar_checkmark_tickets'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_checkmark_tickets', $calendar_checkmark_tickets);
    $calendar_checkmark_status = filter_var(implode('*#*',$_POST['calendar_checkmark_status']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_checkmark_status', $calendar_checkmark_status);
    $calendar_highlight_tickets = filter_var($_POST['calendar_highlight_tickets'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_highlight_tickets', $calendar_highlight_tickets);
    $calendar_completed_color = filter_var(implode('*#*',$_POST['calendar_completed_color']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_completed_color', $calendar_completed_color);

    $ticket_status_color_code = filter_var($_POST['ticket_status_color_code'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'ticket_status_color_code', $ticket_status_color_code);
    $ticket_status_color_code_legend = filter_var($_POST['ticket_status_color_code_legend'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'ticket_status_color_code_legend', $ticket_status_color_code_legend);
    $calendar_ticket_card_fields = filter_var(implode(',',$_POST['calendar_ticket_card_fields']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_card_fields', $calendar_ticket_card_fields);
    $calendar_ticket_card_fields_summary = filter_var(implode(',',$_POST['calendar_ticket_card_fields_summary']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_card_fields_summary', $calendar_ticket_card_fields_summary);
    $calendar_ticket_card_fields_summary_deleted = filter_var(implode(',',$_POST['calendar_ticket_card_fields_summary_deleted']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_card_fields_summary_deleted', $calendar_ticket_card_fields_summary_deleted);
    $calendar_ticket_staff_summary_fields = filter_var(implode(',',$_POST['calendar_ticket_staff_summary_fields']), FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_staff_summary_fields', $calendar_ticket_staff_summary_fields);
    $calendar_ticket_status_icon = filter_var($_POST['calendar_ticket_status_icon'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_ticket_status_icon', $calendar_ticket_status_icon);

    $calendar_highlight_incomplete_tickets = filter_var($_POST['calendar_highlight_incomplete_tickets'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_highlight_incomplete_tickets', $calendar_highlight_incomplete_tickets);
    $calendar_incomplete_status = filter_var(implode('*#*',$_POST['calendar_incomplete_status']),FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_incomplete_status', $calendar_incomplete_status);
    $calendar_incomplete_color = filter_var(implode('*#*',$_POST['calendar_incomplete_color']),FILTER_SANITIZE_STRING);
    set_config($dbc, 'calendar_incomplete_color', $calendar_incomplete_color);

    foreach ($_POST['ticket_status'] as $i => $status) {
        $color_code = $_POST['status_color'][$i];
        mysqli_query($dbc, "INSERT INTO `field_config_ticket_status_color` (`status`) SELECT '$status' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_status_color` WHERE `status` = '$status') num WHERE num.rows = 0");
        mysqli_query($dbc, "UPDATE `field_config_ticket_status_color` SET `color` = '$color_code' WHERE `status` = '$status'");
    }
}
?>
<script type="text/javascript">
function displayCompletedColor() {
    if ($('[name="calendar_highlight_tickets"]').is(":checked")) {
        $('#completed_color').show();
    } else {
        $('#completed_color').hide();
    }
}
function colorCodeChange(sel) {
    $(sel).closest('.color_block').find('.color_hex').val(sel.value);
}
function addCheckmarkStatus() {
    destroyInputs('.checkmark_group');
    var block = $('.checkmark_group').last();
    var clone = $(block).clone();

    $(clone).find('.form-control').val('');
    $(clone).find('[name="calendar_completed_color[]"]').val('#00ff00');
    $(clone).find('[name="calendar_completed_color_visual[]"]').val('#00ff00');

    $(block).after(clone);
    initInputs('.checkmark_group');
}
function removeCheckmarkStatus(btn) {
    if($('.checkmark_group').length <= 1) {
        addCheckmarkStatus();
    }

    $(btn).closest('.checkmark_group').remove();
}
function displayIncompleteColor() {
    if ($('[name="calendar_highlight_incomplete_tickets"]').is(":checked")) {
        $('.incomplete_group').show();
    } else {
        $('.incomplete_group').hide();
    }
}
function addIncompleteStatus() {
    destroyInputs('.incomplete_group');
    var block = $('.incomplete_group').last();
    var clone = $(block).clone();

    $(clone).find('.form-control').val('');
    $(clone).find('[name="calendar_incomplete_color[]"]').val('#ff0000');
    $(clone).find('[name="calendar_incomplete_color_visual[]"]').val('#ff0000');

    $(block).after(clone);
    initInputs('.incomplete_group');
}
function removeIncompleteStatus(btn) {
    if($('.incomplete_group').length <= 1) {
        addIncompleteStatus();
    }

    $(btn).closest('.incomplete_group').remove();
}
</script>
<?php
$ticket_statuses = explode(',', get_config($dbc, 'ticket_status'));
$checkmark_tickets = get_config($dbc, 'calendar_checkmark_tickets');
$checkmark_statuses = explode('*#*',get_config($dbc, 'calendar_checkmark_status'));
$highlight_tickets = get_config($dbc, 'calendar_highlight_tickets');
$completed_colors = explode('*#*',get_config($dbc, 'calendar_completed_color'));

$highlight_incomplete_tickets = get_config($dbc, 'calendar_highlight_incomplete_tickets');
$incomplete_statuses = explode('*#*',get_config($dbc, 'calendar_incomplete_status'));
$incomplete_colors = explode('*#*',get_config($dbc, 'calendar_incomplete_color'));

$ticket_status_color_code = get_config($dbc, 'ticket_status_color_code');
$ticket_status_color_code_legend = get_config($dbc, 'ticket_status_color_code_legend');
?>
<h3>Tickets</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_general_settings"><?= TICKET_NOUN ?> General Settings</a>
            </h4>
        </div>
        <div id="collapse_general_settings" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Hover Over To Display Assigned Staff:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_hover_staff = get_config($dbc, 'calendar_ticket_hover_staff'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_hover_staff" value="1" <?= $calendar_ticket_hover_staff == 1 ? 'checked' : '' ?>> Enable</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Different <?= TICKET_NOUN ?> Label In Calendar:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_diff_label = get_config($dbc, 'calendar_ticket_diff_label'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_diff_label" value="1" <?= $calendar_ticket_diff_label == 1 ? 'checked' : '' ?> onchange="if($(this).is(':checked')) { $('.calendar_ticket_label').show(); } else { $('.calendar_ticket_label').hide(); }"> Enable</label> 
                    </div>
                </div>
                <div class="form-group calendar_ticket_label" <?= $calendar_ticket_diff_label != 1 ? 'style="display:none;"' : '' ?>>
                    <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Label:<br><em>Enter how you want a Ticket to appear. You can enter [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [PROJECT_TYPE], [PROJECT_TYPE_CODE], [TICKET_NOUN], [TICKETID], [TICKET_HEADING], [TICKET_DATE], [BUSINESS], [CONTACT], [SITE_NAME], [TICKET_TYPE], [STOP_LOCATION], [STOP_CLIENT].</em></label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_label = get_config($dbc, 'calendar_ticket_label'); ?>
                        <input type="text" name="calendar_ticket_label" value="<?= $calendar_ticket_label ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Check Staff Shift When Booking:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_check_shifts = get_config($dbc, 'calendar_ticket_check_shifts'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_check_shifts" value="1" <?= $calendar_ticket_check_shifts == 1 ? 'checked' : '' ?>> Enable</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Check Staff Days Off When Booking:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_check_days_off = get_config($dbc, 'calendar_ticket_check_days_off'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_check_days_off" value="1" <?= $calendar_ticket_check_days_off == 1 ? 'checked' : '' ?>> Enable</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Color Code By <?= TICKET_NOUN ?> Type:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_color_code_tabs = get_config($dbc, 'calendar_ticket_color_code_tabs'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_color_code_tabs" value="1" <?= $calendar_ticket_color_code_tabs == 1 ? 'checked' : '' ?>> Enable</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $ticket_label_fields = [
        'project'=>PROJECT_NOUN,
        'ticket_type'=>TICKET_NOUN.' Type',
        'customer'=>'Customer',
        'client'=>'Client',
        'site_address'=>'Site Address',
        'service_template'=>'Service Template',
        'assigned'=>'Assigned Staff',
        'preferred'=>'Preferred Staff',
        'time'=>'Time',
        'eta'=>'ETA',
        'available'=>'Time Frame',
        'address'=>'Address',
        'start_date'=>'Date',
        'ticket_notes'=>TICKET_NOUN.' Notes',
        'delivery_notes'=>'Delivery Notes',
        'delivery_sign_off'=>'Delivery Sign Off',
        'status'=>'Status'
    ]; ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_label"><?= TICKET_NOUN ?> Labels</a>
            </h4>
        </div>
        <div id="collapse_label" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Information to display on <?= TICKET_NOUN ?> Cards:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields')); ?>
                        <label class="form-checkbox"><input type="checkbox" checked disabled> <?= TICKET_NOUN ?> Label</label>
                        <?php foreach($ticket_label_fields as $label_key => $label_field) { ?>
                            <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_card_fields[]" value="<?= $label_key ?>" <?= in_array($label_key, $calendar_ticket_card_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_summary_label"><?= TICKET_NOUN ?> Summary - <?= TICKET_NOUN ?> Labels</a>
            </h4>
        </div>
        <div id="collapse_summary_label" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Information to display on <?= TICKET_NOUN ?> Cards:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields_summary')); ?>
                        <label class="form-checkbox"><input type="checkbox" checked disabled> <?= TICKET_NOUN ?> Label</label>
                        <?php foreach($ticket_label_fields as $label_key => $label_field) { ?>
                            <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_card_fields_summary[]" value="<?= $label_key ?>" <?= in_array($label_key, $calendar_ticket_card_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Information to display on deleted <?= TICKET_NOUN ?> Cards:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields_summary_deleted')); ?>
                        <label class="form-checkbox"><input type="checkbox" checked disabled> <?= TICKET_NOUN ?> Label</label>
                        <?php foreach($ticket_label_fields as $label_key => $label_field) { ?>
                            <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_card_fields_summary_deleted[]" value="<?= $label_key ?>" <?= in_array($label_key, $calendar_ticket_card_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Information to display on Staff Summary tabs:</label>
                    <div class="col-sm-8">
                        <?php $calendar_ticket_staff_summary_fields = explode(',',get_config($dbc, 'calendar_ticket_staff_summary_fields')); ?>
                        <label class="form-checkbox"><input type="checkbox" name="calendar_ticket_staff_summary_fields[]" value="client" <?= in_array('client', $calendar_ticket_staff_summary_field) ? 'checked' : '' ?>> Client</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_slider"><?= TICKET_NOUN ?> Slider Window View</a>
            </h4>
        </div>
        <div id="collapse_slider" class="panel-collapse collapse">
            <div class="panel-body">
                <label class="col-sm-4 control-label">Default Slider Window View:</label>
                <div class="col-sm-8">
                    <?php $calendar_ticket_slider = get_config($dbc, 'calendar_ticket_slider'); ?>
                    <label class="form-checkbox"><input type="radio" name="calendar_ticket_slider" value="full" <?= $calendar_ticket_slider != 'accordion' ? 'checked="checked"' : '' ?>"> Full View</label>
                    <label class="form-checkbox"><input type="radio" name="calendar_ticket_slider" value="accordion" <?= $calendar_ticket_slider == 'accordion' ? 'checked="checked"' : '' ?>"> Accordion View</label>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checkmark">Completed <?= TICKET_NOUN ?> Settings</a>
            </h4>
        </div>
        <div id="collapse_checkmark" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="checkmark_tickets" class="col-sm-4 control-label">Checkmark Completed <?= TICKET_TILE ?>:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="calendar_checkmark_tickets" value="1" <?= $checkmark_tickets == 1 ? 'checked' : '' ?>> Enable
                    </div>
                </div>
                <div class="form-group">
                    <label for="highlight_tickets" class="col-sm-4 control-label">Highlight Completed <?= TICKET_TILE ?>:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="calendar_highlight_tickets" value="1" <?= $highlight_tickets == 1 ? 'checked' : '' ?> onchange="displayCompletedColor();"> Enable
                    </div>
                </div>
                <hr>
                <?php foreach($checkmark_statuses as $i => $checkmark_status) { ?>
                    <div class="checkmark_group">
                        <div class="form-group" id="checkmark_status">
                            <label for="checkmark_status" class="col-sm-4 control-label">Completed <?= TICKET_NOUN ?> Status:<br><em>Choose the status that you would like to indicate that a <?= TICKET_NOUN ?> is Complete.</em></label>
                            <div class="col-sm-8">
                                <select name="calendar_checkmark_status[]" data-placeholder="Select a Status..." class="chosen-select-deselect form-control">
                                    <option></option>
                                    <?php foreach ($ticket_statuses as $ticket_status) { ?>
                                        <option value="<?= $ticket_status ?>" <?= $checkmark_status == $ticket_status ? 'selected' : '' ?>><?= $ticket_status ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group color_block" id="completed_color" <?= $highlight_tickets != 1 ? 'style="display:none;"' : '' ?>>
                            <label for="checkmark_status" class="col-sm-4 control-label">Completed <?= TICKET_NOUN ?> Highlight Color:<br><em>Choose the color to highlight Completed <?= TICKET_TILE ?>.</em></label>
                            <div class="col-sm-1">
                                <input onchange="colorCodeChange(this);" class="form-control" type="color" name="calendar_completed_color_visual[]" value="<?= !empty($completed_colors[$i]) ? $completed_colors[$i] : '#00ff00' ?>">
                            </div>
                            <div class="col-sm-7">
                                <input type="text" name="calendar_completed_color[]" class="form-control color_hex" value="<?= !empty($completed_colors[$i]) ? $completed_colors[$i] : '#00ff00' ?>">
                            </div>
                        </div>
                        <div class="form-group pull-right">
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right black-color" onclick="addCheckmarkStatus();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="removeCheckmarkStatus(this);">
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_incomplete">Incomplete <?= TICKET_NOUN ?> Settings</a>
            </h4>
        </div>
        <div id="collapse_incomplete" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="highlight_incomplete_tickets" class="col-sm-4 control-label">Highlight Incomplete <?= TICKET_TILE ?>:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="calendar_highlight_incomplete_tickets" value="1" <?= $highlight_incomplete_tickets == 1 ? 'checked' : '' ?> onchange="displayIncompleteColor();"> Enable
                    </div>
                </div>
                <hr>
                <?php foreach($incomplete_statuses as $i => $incomplete_status) { ?>
                    <div class="incomplete_group" <?= $highlight_incomplete_tickets != 1 ? 'style="display:none;"' : '' ?>>
                        <div class="form-group">
                            <label for="checkmark_status" class="col-sm-4 control-label">Inomplete <?= TICKET_NOUN ?> Status:<br><em>Choose the status that you would like to indicate that a <?= TICKET_NOUN ?> is Incomplete.</em></label>
                            <div class="col-sm-8">
                                <select name="calendar_incomplete_status[]" data-placeholder="Select a Status..." class="chosen-select-deselect form-control">
                                    <option></option>
                                    <?php foreach ($ticket_statuses as $ticket_status) { ?>
                                        <option value="<?= $ticket_status ?>" <?= $incomplete_status == $ticket_status ? 'selected' : '' ?>><?= $ticket_status ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group color_block">
                            <label for="checkmark_status" class="col-sm-4 control-label">Incomplete <?= TICKET_NOUN ?> Highlight Color:<br><em>Choose the color to highlight Incomplete <?= TICKET_TILE ?>.</em></label>
                            <div class="col-sm-1">
                                <input onchange="colorCodeChange(this);" class="form-control" type="color" name="calendar_incomplete_color_visual[]" value="<?= !empty($incomplete_colors[$i]) ? $incomplete_colors[$i] : '#ff0000' ?>">
                            </div>
                            <div class="col-sm-7">
                                <input type="text" name="calendar_incomplete_color[]" class="form-control color_hex" value="<?= !empty($incomplete_colors[$i]) ? $incomplete_colors[$i] : '#ff0000' ?>">
                            </div>
                        </div>
                        <div class="form-group pull-right">
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right black-color" onclick="addIncompleteStatus();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="removeIncompleteStatus(this);">
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#checkmark_status_color">Status Color Code</a>
            </h4>
        </div>
        <div id="checkmark_status_color" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="ticket_status_color_code" class="col-sm-4 control-label">Use Status Color Code:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="ticket_status_color_code" value="1" <?= $ticket_status_color_code == 1 ? 'checked' : '' ?>> Enable
                    </div>
                </div>
                <div class="form-group">
                    <label for="ticket_status_color_code_legend" class="col-sm-4 control-label">Display Status Color Code Legend:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="ticket_status_color_code_legend" value="1" <?= $ticket_status_color_code_legend == 1 ? 'checked' : '' ?>> Enable
                    </div>
                </div>
                <?php foreach ($ticket_statuses as $ticket_status) {
                    $color_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'")); ?>
                    <div class="form-group color_block">
                        <input type="hidden" name="ticket_status[]" value="<?= $ticket_status ?>">
                        <label class="col-sm-4 control-label"><?= $ticket_status ?>:</label>
                        <div class="col-sm-1">
                            <input onchange="colorCodeChange(this);" class="form-control" type="color" name="status_color_picker[]" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="status_color[]" class="form-control color_hex" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
                        </div>
                    </div>
                <?php } ?>
				<!--<?php $ticket_status = "Today";
				$color_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'")); ?>
				<div class="form-group color_block">
					<input type="hidden" name="ticket_status[]" value="<?= $ticket_status ?>">
					<label class="col-sm-4 control-label">Today + Following Day:</label>
					<div class="col-sm-1">
						<input onchange="colorCodeChange(this);" class="form-control" type="color" name="status_color_picker[]" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
					<div class="col-sm-7">
						<input type="text" name="status_color[]" class="form-control" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
				</div>
				<?php $ticket_status = "Recent";
				$color_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'")); ?>
				<div class="form-group color_block">
					<input type="hidden" name="ticket_status[]" value="<?= $ticket_status ?>">
					<label class="col-sm-4 control-label">Last 2 Days:</label>
					<div class="col-sm-1">
						<input onchange="colorCodeChange(this);" class="form-control" type="color" name="status_color_picker[]" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
					<div class="col-sm-7">
						<input type="text" name="status_color[]" class="form-control" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
				</div>
				<?php $ticket_status = "Old";
				$color_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'")); ?>
				<div class="form-group color_block">
					<input type="hidden" name="ticket_status[]" value="<?= $ticket_status ?>">
					<label class="col-sm-4 control-label">Older than 2 Days:</label>
					<div class="col-sm-1">
						<input onchange="colorCodeChange(this);" class="form-control" type="color" name="status_color_picker[]" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
					<div class="col-sm-7">
						<input type="text" name="status_color[]" class="form-control" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
					</div>
				</div>-->
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#ticket_status_icons">Status Icon Display</a>
            </h4>
        </div>
        <div id="ticket_status_icons" class="panel-collapse collapse">
            <div class="panel-body">
                <label class="col-sm-4 control-label">Status Icon Display:</label>
                <div class="col-sm-8">
                    <?php $calendar_ticket_status_icon = get_config($dbc, 'calendar_ticket_status_icon'); ?>
                    <label class="form-checkbox"><input type="radio" name="calendar_ticket_status_icon" value="" <?= $calendar_ticket_status_icon != 'background' ? 'checked' : '' ?>> Top Right Icon</label>
                    <label class="form-checkbox"><input type="radio" name="calendar_ticket_status_icon" value="background" <?= $calendar_ticket_status_icon == 'background' ? 'checked' : '' ?>> Background Image</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_tickets" value="add_tickets" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>