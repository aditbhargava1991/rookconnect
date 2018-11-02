<?php /* Display Time Sheet Block
This will display all of the fields for the Time Sheet with Editable Fields.
This file can be included on each of the pages, so that they display correctly and consistently.
Approval pages and Payroll pages have some additional fields that need to display.

Included in:
    time_cards.php
    time_card_approvals_coordinator.php
    time_card_approvals_manager.php
    payroll.php

Change Log:
    2018-08-07 Created Page
*/

// Get settings
$current_page = basename($_SERVER['SCRIPT_FILENAME']);
if($current_page == 'daysheet.php') {
    $current_page = 'time_cards.php';
}
$layout = get_config($dbc, 'timesheet_layout');
$value_config = explode(',',get_field_config($dbc, 'time_cards'));
if(!in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config) && !in_array('total_hrs',$value_config)) {
    $value_config = array_merge($value_config,['total_hrs']);
}
$timesheet_payroll_fields = ($current_page == 'payroll.php' ? ','.get_config($dbc, 'timesheet_payroll_fields').',' : ',,');
$timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder');
$timesheet_approval_initials = get_config($dbc, 'timesheet_approval_initials');
$timesheet_approval_date = get_config($dbc, 'timesheet_approval_date');
$timesheet_approval_status_comments = get_config($dbc, 'timesheet_approval_status_comments');
$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
$timesheet_rounding = get_config($dbc, 'timesheet_rounding');
$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
$highlight = get_config($dbc, 'timesheet_highlight');
$mg_highlight = get_config($dbc, 'timesheet_manager');
$submit_mode = get_config($dbc, 'timesheet_submit_mode');
$sql_approv = '';
if($current_page == 'payroll.php') {
    $sql_approv = "AND (`approv`='Y' OR `approv`='P')";
} else if($current_page != 'time_cards.php') {
    $sql_approv = "";
} else {
    $sql_approv = "";
}
$colspan = 1 + (in_array('schedule',$value_config) ? 1 : 0) + (in_array('scheduled',$value_config) ? 1 : 0) + (in_array('ticketid',$value_config) ? 1 : 0) + (in_array('show_hours',$value_config) ? 1 : 0)
    + (in_array('total_tracked_hrs',$value_config) ? 1 : 0) + (in_array('start_time',$value_config) || in_array('start_time_editable',$value_config) ? 1 : 0)
    + (in_array('end_time',$value_config) || in_array('end_time_editable',$value_config) ? 1 : 0) + (in_array('planned_hrs',$value_config) ? 1 : 0)
    + (in_array('tracked_hrs',$value_config) ? 1 : 0) + (in_array('total_tracked_time',$value_config) ? 1 : 0) + (in_array('start_day_tile',$value_config) ? 1 : 0) + (in_array('ticket_select',$value_config) ? 1 : 0)
    + (in_array('task_select',$value_config) ? 1 : 0) + (in_array('position_select',$value_config) ? 1 : 0) + (in_array('total_tracked_hrs_task',$value_config) ? 1 : 0); ?>
<script>
$(document).ready(function() {
    $('.overlap_time').hide();
    checkTimeOverlaps();
    initLines();
    $('[name=ticketid]').each(function() {
        getTasks(this);
    });
});
var getTasks = function(sel) {
    var task_name = $(sel).closest('tr').find('select[name="type_of_time"]').val();
    var tasks = $(sel).find('option:selected').data('tasks');
    var tasks_sel = $(sel).closest('tr').find('select[name="type_of_time"]');
    var tasks_html = '<option></option>';
    if(tasks == undefined) {
        return;
    }
    tasks.forEach(function(task) {
        tasks_html += '<option '+(task == task_name ? 'selected' : '')+' value="'+task+'">'+task+'</option>';
    });
    $(tasks_sel).html(tasks_html).trigger('change.select2');
    if($(sel).val() != undefined && $(sel).val() != '') {
        $(sel).closest('tr').find('.view_ticket').data('ticketid', $(sel).val()).show();
    } else {
        $(sel).closest('tr').find('.view_ticket').data('ticketid', '').hide();
    }
}
var checkDrivingTime = function(chk) {
    var block = $(chk).closest('tr');
    if($(chk).is(':checked')) {
        $(block).find('.ticket_task_td').each(function() {
            $(this).find('select').val('').trigger('change');
            $(this).addClass('readonly-block');
        });
    } else {
        $(block).find('.ticket_task_td').removeClass('readonly-block');
    }
}
var initLines = function() {
    $('.edit-row').off('click').click(function() {
        var line = $(this).closest('tr');
        destroyInputs();
        line.find('.readonly-block').removeClass('readonly-block');
        line.find('[readonly]').removeAttr('readonly');
        line.find('.no-datepicker').removeClass('no-datepicker').addClass('datepicker');
        line.find('.no-datetimepicker').removeClass('no-datetimepicker').addClass('datetimepicker');
        line.find('.no-timepicker').removeClass('no-timepicker').addClass('timepicker');
        line.find('.edit_read').hide();
        line.find('.editable').show();
        initInputs();
        initLines();
    });
    $('.add-row').off('click').click(function() {
        var line = $(this).closest('tr');
        destroyInputs();
        var new_line = line.clone();
        line.removeClass('theme-color-border-bottom');
        line.find('.theme-color-border-bottom').removeClass('theme-color-border-bottom');
        new_line.find('input[name^=hours],select[name^=ticketid],select[name^=type_oof_time],input[name^=start_time],input[name^=end_time],input[name^=total_hrs],input[name^=time_cards_id]').val('');
        new_line.find('input[type=checkbox]').removeAttr('checked');
        new_line.find('.readonly-block').removeClass('readonly-block');
        new_line.find('[readonly]').removeAttr('readonly');
        new_line.find('.no-datepicker').removeClass('no-datepicker').addClass('datepicker');
        new_line.find('.no-datetimepicker').removeClass('no-datetimepicker').addClass('datetimepicker');
        new_line.find('.no-timepicker').removeClass('no-timepicker').addClass('timepicker');
        new_line.find('.edit_read').hide();
        new_line.find('.editable').show();
        new_line.find('select').val('');
        new_line.find('input[name!=date][type!=hidden]').val('');
        new_line.find('[name=time_cards_id]').val('');
        new_line.find('span').remove();
        line.after(new_line);
        initInputs();
        initLines();
    });
    $('.rem-row').off('click').click(function() {
        var line = $(this).closest('tr');
        line.find('[name^=deleted]').val(1).change();
        line.hide();
    });
    $('.comment-row').off('click').click(function() {
        var line = $(this).closest('tr');
        line.find('[name^=comment_box]').show().focus();
    });
    $('[name="start_time"],[name="end_time"]').off('change',checkTimeOverlaps).change(checkTimeOverlaps);
    $('input,select').not('[class*=timepicker]').off('change',saveField).change(saveField);
    $('.timepicker').timepicker('option','onClose',function() { saveField(this); });
    $('.datetimepicker').datetimepicker('option','onClose',function() { saveField(this); });
}
var checkTimeOverlaps = function() {
    <?php if(in_array('time_overlaps',$value_config)) { ?>
        var time_list = [];
        var date_list = [];
        $('.timesheet_div table').each(function() {
            $(this).find('tr').each(function() {
                var date = $(this).find('[name="date"]').val();
                if(time_list[date] == undefined) {
                    time_list[date] = [];
                }
                if(date_list.indexOf(date) == -1) {
                    date_list.push(date);
                }

                var start_time = '';
                var end_time = '';
                if($(this).find('[name="start_time"]').val() != undefined && $(this).find('[name="start_time"]').val() != '' && $(this).find('[name="end_time"]').val() != undefined && $(this).find('[name="end_time"]').val() != '') {
                    time_list[date].push($(this));
                }
            });
        });
        date_list.forEach(function(date) {
            time_list[date].forEach(function(tr) {
                $(tr).data('current_row', 1);
                start_time = new Date(date+' '+$(tr).find('[name="start_time"]').val());
                end_time = new Date(date+' '+$(tr).find('[name="end_time"]').val());
                time_list[date].forEach(function(tr2) {
                    if($(tr2).data('current_row') != 1) {
                        start_time2 = new Date(date+' '+$(tr2).find('[name="start_time"]').val());
                        end_time2 = new Date(date+' '+$(tr2).find('[name="end_time"]').val())
                        if((start_time.getTime() > start_time2.getTime() && start_time.getTime() < end_time2.getTime()) || (end_time.getTime() > start_time2.getTime() && end_time.getTime() < end_time2.getTime())) {
                            $(tr).css('background-color', 'red');
                            //$(tr).find('.overlap_time').css("display","block");
                        } else if($(tr).data('background') != '' && $(tr).data('background') != undefined) {
                            $(tr).css('background-color', $(tr).data('background'));
                        } else {
                            $(tr).css('background-color', '');
                        }
                    }
                });
                $(tr).data('current_row', 0);
            });
        });
    <?php } ?>
}

var useProfileSig = function(chk) {
	if($(chk).is(':checked')) {
		$('.profile_sig_box').show();
		$('.timesheet_sig_box').hide();
	} else {
		$('.profile_sig_box').hide();
		$('.timesheet_sig_box').show();
	}
}
</script>
<?php // Create Table ?>

<input type="hidden" name="current_page" value="<?= $current_page ?>">
<div id="no-more-tables" class="double-gap-bottom" style="max-width:100%; overflow-x:scroll;">
    <table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
            <?php $hourly_comp = $dbc->query("SELECT * FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Staff' AND `start_date` <= '$search_start_date' AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'$search_start_date') >= '$search_start_date' AND (`description` IN ('ALL','$search_staff') OR `item_id`='$search_staff') ORDER BY `item_id`='$search_staff' DESC, `description`='$search_staff' DESC, `uom`='Hourly' DESC")->fetch_assoc();
            $hourly_comp = empty($hourly_comp['cost']) ? (empty($hourly_comp['cust_price']) ? $hourly_comp['hourly'] : $hourly_comp['cost']) : $hourly_comp['cost'];
            set_stat_hours($dbc, $search_staff, $search_start_date, $search_end_date);
            $start_of_year = date('Y-01-01', strtotime($search_start_date));
            $sql = "SELECT IFNULL(SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)),0) SICK_HRS,
                IFNULL(SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)),0) STAT_AVAIL,
                IFNULL(SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)),0) STAT_HRS,
                IFNULL(SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)),0) VACA_AVAIL,
                IFNULL(SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)),0) VACA_HRS
                FROM `time_cards` WHERE `staff`='$search_staff' AND `date` < '$search_start_date' AND `date` >= '$start_of_year' AND (`approv`='Y' OR `approv`='P') AND `deleted`=0";
            $year_to_date = mysqli_fetch_array(mysqli_query($dbc, $sql));

            $stat_hours = $year_to_date['STAT_AVAIL'];
            $stat_taken = $year_to_date['STAT_HRS'];
            $vacation_hours = $year_to_date['VACA_AVAIL'];
            $vacation_taken = $year_to_date['VACA_HRS'];
            $sick_taken = $year_to_date['SICK_HRS']; ?>
            <td colspan="<?= $colspan ?>">Balance Forward Y.T.D.</td>
            <?php if(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('start_day_tile_separate',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('direct_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('indirect_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('extra_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('relief_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('sleep_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('training_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('sick_hrs',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('sick_used',$value_config)) { ?><th style='text-align:center;'><?= $sick_taken; ?></th><?php } ?>
            <?php if(in_array('stat_hrs',$value_config)) { ?><th style='text-align:center;'><?= $stat_hours; ?></th><?php } ?>
            <?php if(in_array('stat_used',$value_config)) { ?><th style='text-align:center;'><?= $stat_taken; ?></th><?php } ?>
            <?php if(in_array('vaca_hrs',$value_config)) { ?><th style='text-align:center;'><?= $vacation_hours; ?></th><?php } ?>
            <?php if(in_array('vaca_used',$value_config)) { ?><th style='text-align:center;'><?= $vacation_taken; ?></th><?php } ?>
            <?php if(in_array('breaks',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('view_ticket',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(in_array('compensation',$value_config)) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE) { ?><th style='text-align:center;'></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE) { ?><td style='text-align:center;'></td><?php } ?>
            <?php if($current_page != 'time_cards.php') { ?>
            <?php if($timesheet_approval_status_comments == 1) { ?><td style='text-align:center;'></td><?php } ?>
            <?php if($timesheet_approval_initials == 1) { ?><td style='text-align:center;'></td><?php } ?>
            <?php if($timesheet_approval_date == 1) { ?><td style='text-align:center;'></td><?php } ?>
            <?php } ?>
            <th colspan="<?= (in_array('comment_box',$value_config) ? 1 : 0) + ($current_page != 'time_cards.php' ? 1 : (in_array('signature',$value_config) ? 1 : 0)) ?>"></th>
        </tr>
        <tr class='hidden-xs hidden-sm'>
            <th style='text-align:center; vertical-align:bottom;'><div style="min-width:<?= (in_array('editable_dates',$value_config) ? '9em;' : '6em;') ?>">Date</div></th>
            <?php if(in_array('schedule',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:6em;">Schedule</div></th><?php } ?>
            <?php if(in_array('scheduled',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;">Scheduled Hours</div></th><?php } ?>
            <?php if(in_array('ticketid',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;"><?= TICKET_NOUN ?></div></th><?php } ?>
            <?php if(in_array('show_hours',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;">Hours</div></th><?php } ?>
            <?php if(in_array('total_tracked_hrs',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:3.5em;">Total Tracked Hours</div></th><?php } ?>
            <?php if(in_array('start_time',$value_config) || in_array('start_time_editable',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:6em;">Start Time</div></th><?php } ?>
            <?php if(in_array('end_time',$value_config) || in_array('end_time_editable',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:6em;">End Time</div></th><?php } ?>
            <?php if(in_array('planned_hrs',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:3.5em;">Planned Hours</div></th><?php } ?>
            <?php if(in_array('tracked_hrs',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:3.5em;">Tracked Hours</div></th><?php } ?>
            <?php if(in_array('total_tracked_time',$value_config)) {; ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:3.5em;">Total Tracked Time</div></th><?php } ?>
            <?php if(in_array('start_day_tile',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;"><?= $timesheet_start_tile ?></div></th><?php } ?>
            <?php if(in_array('ticket_select',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:10em;"><?= TICKET_NOUN ?></div></th><?php } ?>
            <?php if(in_array('task_select',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:8em;">Task</div></th><?php } ?>
            <?php if(in_array('position_select',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:8em;">Position</div></th><?php } ?>
            <?php if(in_array('total_tracked_hrs_task',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:3.5em;">Time Tracked</div></th><?php } ?>
            <?php if(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;"><?= in_array('payable_hrs',$value_config) ? 'Payable' : (in_array('total_hrs',$value_config) ? 'Total' : 'Regular') ?> Hours</div></th><?php } ?>
            <?php if(in_array('start_day_tile_separate',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;"><?= $timesheet_start_tile ?></div></th><?php } ?>
            <?php if(in_array('direct_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Direct Hours</div></th><?php } ?>
            <?php if(in_array('indirect_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Indirect Hours</div></th><?php } ?>
            <?php if(in_array('extra_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Extra Hours</div></th><?php } ?>
            <?php if(in_array('relief_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Relief Hours</div></th><?php } ?>
            <?php if(in_array('sleep_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Sleep Hours</div></th><?php } ?>
            <?php if(in_array('training_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Training Hours</div></th><?php } ?>
            <?php if(in_array('sick_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Sick Time Adjustment</div></th><?php } ?>
            <?php if(in_array('sick_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Sick Hrs. Taken</div></th><?php } ?>
            <?php if(in_array('stat_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Stat Hours</div></th><?php } ?>
            <?php if(in_array('stat_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Stat. Hrs. Taken</div></th><?php } ?>
            <?php if(in_array('vaca_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Vacation Hours</div></th><?php } ?>
            <?php if(in_array('vaca_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Vacation Hrs. Taken</div></th><?php } ?>
            <?php if(in_array('breaks',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Breaks</div></th><?php } ?>
            <?php if(in_array('view_ticket',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;"><?= TICKET_NOUN ?></div></th><?php } ?>
            <?php if(in_array('compensation',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Hourly Compensation</div></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Expenses Owed</div></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Mileage</div></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Mileage Rate</div></th><?php } ?>
            <?php if(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:4em;">Mileage Total</div></th><?php } ?>

            <?php if($current_page != 'time_cards.php') { ?>
            <?php if($timesheet_approval_status_comments == 1) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;">Status</div></th><?php } ?>
            <?php if($timesheet_approval_initials == 1) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;">Approved By</div></th><?php } ?>
            <?php if($timesheet_approval_date == 1) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:5em;">Approved Date</div></th><?php } ?>
            <?php } ?>

            <?php // if(in_array('comment_box',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div style="min-width:6em;">Function</div></th><?php //} ?>
            <?php if($current_page == 'time_cards.php' && in_array('signature',$value_config)) { ?><th><div style="min-width:5em;">Parent / Guardian Signature</div></th><?php } ?>
            <?php if($current_page != 'time_cards.php') { ?><th><span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title=""
                data-original-title="Check the boxes on multiple lines, then click Sign and click <?= $current_page == 'payroll.php' ? 'Mark Paid' : 'Approve' ?>."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?= $current_page == 'payroll.php' ? 'Paid' : 'Approve' ?>
                <?php if(in_array('approve_all', $value_config) && in_array($current_page, ['time_card_approvals_coordinator.php','time_card_approvals_manager.php'])) { ?><br><label><input type="checkbox" name="select_all_approve" onclick="approveAll(this);"> Select All<?php } ?></th><?php } ?>
        </tr>
        <?php // Get Ticket, task, and position lists
        if(!isset($position_list) && in_array('position_select', $value_config)) {
            $position_list = $_SERVER['DBC']->query("SELECT `position` FROM (SELECT `name` `position` FROM `positions` WHERE `deleted`=0 UNION SELECT `type_of_time` `position` FROM `time_cards` WHERE `deleted`=0) `list` WHERE IFNULL(`position`,'') != '' GROUP BY `position` ORDER BY `position`")->fetch_all();
        }
        if(!isset($ticket_list) && in_array('ticket_select',$value_config)) {
            $ticket_list = $dbc->query("SELECT `ticketid`,`ticket_label`,`ticket_label_date`,`last_updated_time`,`projectid`,`ticket_type`,`main_ticketid`,`sub_ticket`,`heading`,`created_date`,`to_do_date`,`businessid`,`clientid`,`siteid`,`salesorderid`,`status` FROM `tickets` WHERE `deleted` = 0 AND `status` != 'Archive'")->fetch_all(MYSQLI_ASSOC);
        }
        if(!isset($task_list) && in_array('task_select',$value_config)) {
            $task_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `task_types` WHERE `deleted` = 0 ORDER BY `category`"),MYSQLI_ASSOC);
        }
        $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break','Direct Hrs.','Indirect Hrs.'),`total_hrs`,0)) REG_HRS,
            SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
            SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
            SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
            SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
            SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
            SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS,
            SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
            GROUP_CONCAT(DISTINCT `comment_box` SEPARATOR ', ') COMMENTS, SUM(`timer_tracked`) TRACKED_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `type_of_time`, `ticket_attached_id`, `ticketid`, `start_time`, `end_time`, `approv`, `approve_by`, `approve_date`, `end_time_from`
            FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' $sql_approv AND `deleted`=0 GROUP BY `date`";
        $post_i = '';
        if(in_array($layout,['position_dropdown', 'ticket_task','multi_line'])) {
            $sql .= ", `time_cards_id`";
            $post_i = 0;
        }
        $sql .= " ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC";
        $result = mysqli_query($dbc, $sql);
        $date = $search_start_date;
        $mileage_total = 0;
        $mileage_rate_total = 0;
        $mileage_cost_total = 0;
        $row = mysqli_fetch_array($result);
        $date_total = ['HOURS'=>0,'REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'COMP_TOTAL'=>0];
        $total = ['HOURS'=>0,'REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'COMP_TOTAL'=>0];
        while(strtotime($date) <= strtotime($search_end_date)) {
            $ids = ['HOURS'=>0,'REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
            $attached_ticketid = 0;
            $timecardid = 0;
            $ticket_attached_id = 0;
            $time_type = '';
            $driving_time = '';
            $hl_colour = '';
            $start_time = '';
            $end_time = '';
            $tracked = '';
            $approv = '';
            $mileage = 0;
            $mileage_rate = 0;
            $mileage_cost = 0;
            $mod = '';
            if($date < $last_period && $current_page == 'time_cards.php' && in_array($layout,['position_dropdown', 'ticket_task'])) {
                $mod = 'readonly';
            }
            if($row['date'] == $date) {
                foreach($config['hours_types'] as $hours_type) {
                    if($row[$hours_type] > 0) {
                        switch($timesheet_rounding) {
                            case 'up':
                                $row[$hours_type] = ceil($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
                                break;
                            case 'down':
                                $row[$hours_type] = floor($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
                                break;
                            case 'nearest':
                                $row[$hours_type] = round($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
                                break;
                        }
                    }
                }
                $hl_colour = ($row['MANAGER'] > 0 && $mg_highlight != '#000000' && $mg_highlight != '' ? $mg_highlight : ($row['HIGHLIGHT'] > 0 && $highlight != '#000000' && $highlight != '' ? $highlight : ''));
                $show_separator = 0;
                $hrs = ['REG'=>$row['REG_HRS'],'DIRECT'=>$row['DIRECT_HRS'],'INDIRECT'=>$row['INDIRCET_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],
                    'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'BREAKS'=>$row['BREAKS']];
                $comments = '';
                if(in_array('project',$value_config)) {
                    foreach(explode(',',$row['PROJECTS']) as $projectid) {
                        if($projectid > 0) {
                            $comments .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"))).'<br />';
                        }
                    }
                }
                if(in_array('search_client',$value_config)) {
                    foreach(explode(',',$row['CLIENTS']) as $clientid) {
                        if($clientid > 0) {
                            $comments .= get_contact($dbc, $clientid).'<br />';
                        }
                    }
                }
                $comments .= html_entity_decode($row['COMMENTS']);
                if(empty(strip_tags($comments))) {
                    $comments = $timesheet_comment_placeholder;
                }
                foreach($total as $key => $value) {
                    $total[$key] += $hrs[$key];
                    $date_total[$key] += $hrs[$key];
                }
                $timecardid = $row['time_cards_id'];
                $ticket_attached_id = $row['ticket_attached_id'];
                $attached_ticketid = $row['ticketid'];
                $time_type = $row['type_of_time'];
                $tracked = time_decimal2time($row['TRACKED_HRS']);
                $start_time = !empty($row['start_time']) ? date('h:i a', strtotime($row['start_time'])) : '';
                $end_time = !empty($row['end_time']) ? date('h:i a', strtotime($row['end_time'])) : '';
                $approv = $row['approv'];
                $approve_by = $row['approve_by'];
                $approve_date = $row['approve_date'];
                $end_time_from = $row['end_time_from'];
                if($current_page == 'payroll.php' && $approv == 'P') {
                    $mod = 'readonly';
                } else if($current_page == 'time_cards.php' && $approv != 'N') {
                    $mod = 'readonly';
                } else if($current_page != 'time_cards.php' && $current_page != 'payroll.php' && $approv != 'N') {
                    $mod = 'readonly';
                }
                if($approv == 'N') {
                    $approval_status = 'Pending';
                } else if($approv == 'Y') {
                    $approval_status = 'Approved';
                } else if($approv == 'P') {
                    $approval_status = 'Paid';
                }

                if(in_array($layout,['position_dropdown', 'ticket_task','multi_line'])) {
                    switch($time_type) {
                        case 'Direct Hrs.':
                            $ids['DIRECT'] = $timecardid;
                            break;
                        case 'Indirect Hrs.':
                            $ids['INDIRECT'] = $timecardid;
                            break;
                        case 'Extra Hrs.':
                            $ids['EXTRA'] = $timecardid;
                            break;
                        case 'Relief Hrs.':
                            $ids['RELIEF'] = $timecardid;
                            break;
                        case 'Sleep Hrs.':
                            $ids['SLEEP'] = $timecardid;
                            break;
                        case 'Sick Time Adj.':
                            $ids['SICK_ADJ'] = $timecardid;
                            break;
                        case 'Sick Hrs.Taken':
                            $ids['SICK'] = $timecardid;
                            break;
                        case 'Stat Hrs.':
                            $ids['STAT_AVAIL'] = $timecardid;
                            break;
                        case 'Stat Hrs.Taken':
                            $ids['STAT'] = $timecardid;
                            break;
                        case 'Vac Hrs.':
                            $ids['VACA_AVAIL'] = $timecardid;
                            break;
                        case 'Vac Hrs.Taken':
                            $ids['VACA'] = $timecardid;
                            break;
                        case 'Break':
                            $ids['BREAKS'] = $timecardid;
                            break;
                        case 'Regular Hrs.':
                        default:
                            $ids['TRAINING'] = $timecardid;
                            $ids['DRIVE'] = $timecardid;
                            $ids['HOURS'] = $timecardid;
                            $ids['REG'] = $timecardid;
                            break;
                    }
                }

                if(in_array('training_hrs',$value_config) && $timecardid > 0) {
                    if(is_training_hrs($dbc, $timecardid)) {
                        $hrs['TRAINING'] = $hrs['REG'];
                        $hrs['REG'] = 0;
                        $total['REG'] -= $hrs['TRAINING'];
                        $total['TRAINING'] += $hrs['TRAINING'];
                        $date_total['REG'] -= $hrs['TRAINING'];
                        $date_total['TRAINING'] += $hrs['TRAINING'];
                    } else {
                        $hrs['TRAINING'] = 0;
                    }
                } else {
                    $hrs['TRAINING'] = 0;
                }
                if(in_array('start_day_tile_separate',$value_config) && !($row['ticketid'] > 0)) {
                    $hrs['DRIVE'] = $hrs['REG'];
                    $hrs['REG'] = 0;
                    $total['REG'] -= $hrs['DRIVE'];
                    $total['DRIVE'] += $hrs['DRIVE'];
                    $date_total['REG'] -= $hrs['DRIVE'];
                    $date_total['DRIVE'] += $hrs['DRIVE'];
                } else if(in_array('start_day_tile',$value_config) && empty($row['ticketid'])) {
                    $driving_time = 'Driving Time';
                } else {
                    $hrs['DRIVE'] = 0;
                }

                //Mileage
                $mileage_start = $date.' 00:00:00';
                $mileage_end = $date.' 23:59:59';
                $mileage = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`mileage`) `mileage_total` FROM `mileage` WHERE `deleted` = 0 AND `staffid` = '$search_staff' AND `ticketid` = '$attached_ticketid' AND '$attached_ticketid' > 0 AND (`start` BETWEEN '$mileage_start' AND '$mileage_end' OR `end` BETWEEN '$mileage_start' AND '$mileage_end')"))['mileage_total'];
                $mileage_total += $mileage;

                //Mileage Rate
                $mileage_customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `clientid` FROM `tickets` WHERE `ticketid` = '$attached_ticketid' AND '$attached_ticketid' > 0"))['clientid'];
                $mileage_rate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `mileage` `price` FROM `rate_card` WHERE `clientid` = '$mileage_customer' AND '$mileage_customer' > 0 AND `deleted` = 0 AND `on_off` = 1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
                    SELECT `cust_price` `price` FROM `company_rate_card` WHERE LOWER(`tile_name`)='mileage' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"))['price'];
                $mileage_rate_total += $mileage_rate;

                //Mileage Calculated Cost
                $mileage_cost = $mileage * $mileage_rate;
                $mileage_cost_total += $mileage_cost;

                $row = mysqli_fetch_array($result);
                if($row['date'] != $date) {
                    $show_separator = 1;
                }
            } else {
                $date_total = ['HOURS'=>0,'REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'COMP_TOTAL'=>0];
                $hrs = ['REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0];
                $comments = '';
                $mileage = 0;
                $mileage_rate = 0;
                $mileage_cost = 0;
                $show_separator = 1;
            }
            $expenses_owed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total`) `expenses_owed` FROM `expense` WHERE `deleted` = 0 AND `staff` = '$search_staff' AND `status` = 'Approved' AND `approval_date` = '$date'"))['expenses_owed'];
            $comp_owed = array_sum($hrs) * $hourly_comp;
            $date_total['COMP_TOTAL'] += $comp_owed;
            $total['COMP_TOTAL'] += $comp_owed;
            $hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(`dayoff_type` != '',`dayoff_type`,CONCAT(`starttime`,' - ',`endtime`)) FROM `contacts_shifts` WHERE `deleted`=0 AND `contactid`='$search_staff' AND '$date' BETWEEN `startdate` AND `enddate` ORDER BY `startdate` DESC"))[0];
            $day_of_week = date('l', $date);
            $shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date);
            if(!empty($shifts)) {
                $hours = '';
                foreach ($shifts as $shift) {
                    $hours .= $shift['starttime'].' - '.$shift['endtime'].'<br>';
                }
            } else {
                $hours = $schedule_list[date('w',strtotime($date))];
            }
            //Planned & Tracked Hours
            $ticket_labels = get_ticket_labels($dbc, $date, $search_staff, $layout, $timecardid);
            $planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff, $layout, $timecardid);
            $tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff, $layout, $timecardid);
            $total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff, $layout, $timecardid);
            $ticket_options = '';
            foreach($ticket_list as $ticket) {
                $ticket_options .= "<option data-tasks='".json_encode(explode(',', $ticket['task_available']))."' ".($ticket['ticketid'] == $attached_ticketid ? 'selected' : '').' value="'.$ticket['ticketid'].'">'.get_ticket_label($dbc, $ticket).'</option>';
            }
            $task_options = '';
            foreach($task_list as $task) {
                $task_options .= '<option '.($time_type == $task['description'] ? 'selected' : '').' value="'.$task['description'].'">'.$task['description'].'</option>';
            }
            $position_options = '';
            foreach($position_list as $position) {
                $position_options .= '<option '.($position[0] == $time_type ? 'selected' : '').' value="'.$position[0].'">'.$position[0].'</option>';
            }
            echo '<tr style="'.(empty($hl_colour) ? '' : 'background-color:'.$hl_colour.';').'" data-background="'.$hl_colour.'" class="'.($show_separator==1 && !in_array('total_per_day',$value_config) ? 'theme-color-border-bottom' : '').'">
                <input type="hidden" name="date" value="'.$date.'">
                <input type="hidden" name="staff" value="'.$search_staff.'">
                <input type="hidden" name="siteid" value="'.$search_site.'">
                <input type="hidden" name="projectid" value="'.$search_project.'">
                <input type="hidden" name="clientid" value="'.$search_client.'">
                <input type="hidden" name="ticketid" value="'.$search_ticket.'">
                <input type="hidden" name="deleted" value="0">
                <input type="hidden" name="ticketattachedid" value="'.$ticket_attached_id.'">
                <td data-title="Date" style="text-align:center">'.(in_array('editable_dates',$value_config) ? '<input type="text" name="date" '.$mod.' value="'.$date.'" class="form-control '.($mod != 'readonly' ? 'datepicker' : 'no-datepicker').'">' : $date).'<div class="overlap_time">Overlapping Time Conflict</div></td>
                '.(in_array('schedule',$value_config) ? '<td data-title="Schedule">'.$hours.'</td>' : '').'
                '.(in_array('scheduled',$value_config) ? '<td data-title="Scheduled Hours"></td>' : '').'
                '.(in_array('ticketid',$value_config) ? '<td data-title="'.TICKET_NOUN.'">'.$ticket_labels.'</td>' : '').'
                '.(in_array('show_hours',$value_config) ? '<td data-title="Hours" style="text-align:center">'.$hours.'</td>' : '').'
                '.(in_array('total_tracked_hrs',$value_config) ? '<td data-title="Total Tracked Hours" style="text-align:center">'.(empty($hrs['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))).'</td>' : '').'
                '.(in_array('start_time_editable',$value_config) ? '<td data-title="Start Time" style="text-align:center"><input type="text" name="start_time" class="form-control '.($mod != 'readonly' ? 'datetimepicker' : 'no-datetimepicker').' editable" '.$mod.' value="'.$start_time.'" '.(in_array('calculate_hours_start_end',$value_config) ? 'onchange="calculateHoursByStartEndTimes(this);"' : '').'></td>' : '').'
                '.((!in_array('start_time_editable',$value_config) && in_array('start_time',$value_config)) ? '<td data-title="Start Time" style="text-align:center">'.$start_time.'</td>' : '').'
                '.(in_array('end_time_editable',$value_config) ? '<td data-title="End Time" style="text-align:center"><input type="text" name="end_time" class="form-control '.($mod != 'readonly' ? 'datetimepicker' : 'no-datetimepicker').'" '.$mod.' value="'.$end_time.'" '.(in_array('calculate_hours_start_end',$value_config) ? 'onchange="calculateHoursByStartEndTimes(this);"' : '' ).'></td>' : '').'
                '.((!in_array('end_time_editable',$value_config) && in_array('end_time',$value_config)) ? '<td data-title="End Time" style="text-align:center">'.$end_time.'</td>' : '').'
                '.(in_array('planned_hrs',$value_config) ? '<td data-title="Planned Hours" style="text-align:center">'.$planned_hrs.'</td>' : '').'
                '.(in_array('tracked_hrs',$value_config) ? '<td data-title="Tracked Hours" style="text-align:center">'.$tracked_hrs.'</td>' : '').'
                '.(in_array('total_tracked_time',$value_config) ? '<td data-title="Total Tracked Time" style="text-align:center">'.$total_tracked_time.'</td>' : '').'
                '.(in_array('start_day_tile',$value_config) ? '<td data-title="'.$timesheet_start_tile.'" style="text-align:center" '.($mod == 'readonly' ? 'class="readonly-block"' : '').'><input type="checkbox" '.($driving_time == 'Driving Time' ? 'checked' : '').' name="driving_time" value="1" class="" onclick="checkDrivingTime(this);"></td>' : '').'
                '.(in_array('ticket_select',$value_config) ? '<td data-title="'.TICKET_NOUN.'" class="ticket_task_td '.((in_array('start_day_tile',$value_config) && $driving_time == 'Driving Time') || $mod == 'readonly' ? 'readonly-block' : '').' '.($show_separator==1 ? 'theme-color-border-bottom' : '').'"><select name="ticketid" class="chosen-select-deselect" data-placeholder="Select a '.TICKET_NOUN.'" onchange="getTasks(this);"><option/>'.$ticket_options.'</select></td>' : '').'
                '.(in_array('task_select',$value_config) ? '<td data-title="Task" class="ticket_task_td '.((in_array('start_day_tile',$value_config) && $driving_time == 'Driving Time') || $mod == 'readonly' ? 'readonly-block' : '').' '.($show_separator==1 ? 'theme-color-border-bottom' : '').'"><select name="type_of_time" class="chosen-select-deselect" data-placeholder="Select a Task"><option/>'.$task_options.'</select></td>' : '').'
    			'.(in_array('position_select',$value_config) ? '<td data-title="Position" style="text-align:center" '.($mod == 'readonly' ? 'class="readonly-block"' : '').'><select name="type_of_time" class="chosen-select-deselect" data-placeholder="Select Position"><option />'.$position_options.'</select></td>' : '').'
                '.(in_array('total_tracked_hrs_task',$value_config) ? '<td data-title="Time Tracked">'.$tracked.'</td>' : '').'
                '.(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : (in_array('total_hrs',$value_config) ? 'Total Hours' : 'Regular')).' Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['REG'].'"><input type="hidden" name="type_of_time" value="Regular Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="Extra Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['DRIVE'].'"><input type="hidden" name="type_of_time" value="Regular Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['DRIVE']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DRIVE'],2) : time_decimal2time($hrs['DRIVE']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('direct_hrs',$value_config) ? '<td data-title="Direct Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['DIRECT'].'"><input type="hidden" name="type_of_time" value="Direct Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['DIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DIRECT'],2) : time_decimal2time($hrs['DIRECT']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('indirect_hrs',$value_config) ? '<td data-title="Indirect Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['INDIRECT'].'"><input type="hidden" name="type_of_time" value="Indirect Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['INDIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['INDIRECT'],2) : time_decimal2time($hrs['INDIRECT']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['EXTRA'].'"><input type="hidden" name="type_of_time" value="Extra Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['EXTRA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['EXTRA'],2) : time_decimal2time($hrs['EXTRA']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['RELIEF'].'"><input type="hidden" name="type_of_time" value="Relief Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['RELIEF']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['RELIEF'],2) : time_decimal2time($hrs['RELIEF']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['SLEEP'].'"><input type="hidden" name="type_of_time" value="Sleep Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['SLEEP']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SLEEP'],2) : time_decimal2time($hrs['SLEEP']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['TRAINING'].'"><input type="hidden" name="type_of_time" value="Regular Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['TRAINING']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRAINING'],2) : time_decimal2time($hrs['TRAINING']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['SICK_ADJ'].'"><input type="hidden" name="type_of_time" value="Sick Time Adj."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['SICK_ADJ']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK_ADJ'],2) : time_decimal2time($hrs['SICK_ADJ']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['SICK'].'"><input type="hidden" name="type_of_time" value="Sick Hrs.Taken"><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['SICK']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK'],2) : time_decimal2time($hrs['SICK']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'" '.(in_array('sick_used_conflicts',$value_config) ? 'data-check-conflicts="1"' : '').'></td>' : '').'
                '.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['STAT_AVAIL'].'"><input type="hidden" name="type_of_time" value="Stat Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['STAT_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT_AVAIL'],2) : time_decimal2time($hrs['STAT_AVAIL']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['STAT'].'"><input type="hidden" name="type_of_time" value="Stat Hrs.Taken"><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['STAT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT'],2) : time_decimal2time($hrs['STAT']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['VACA_AVAIL'].'"><input type="hidden" name="type_of_time" value="Vac Hrs."><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['VACA_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA_AVAIL'],2) : time_decimal2time($hrs['VACA_AVAIL']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken" style="text-align:center"><input type="hidden" name="time_cards_id" value="'.$ids['VACA'].'"><input type="hidden" name="type_of_time" value="Vac Hrs.Taken"><input type="text" '.($mod == 'readonly' ? 'readonly' : '').' name="total_hrs" value="'.(empty($hrs['VACA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA'],2) : time_decimal2time($hrs['VACA']))).'" class="form-control '.($mod == 'readonly' ? 'no-timepicker' : 'timepicker').'"></td>' : '').'
                '.(in_array('breaks',$value_config) ? '<td data-title="Breaks" style="text-align:center">'.(empty($hrs['BREAKS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['BREAKS'],2) : time_decimal2time($hrs['BREAKS']))).'</td>' : '').'
                '.(in_array('view_ticket',$value_config) ? '<td data-title="'.TICKET_NOUN.'" style="text-align:center">'.(!empty($attached_ticketid) ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?edit='.$attached_ticketid.'&date='.$date.'&calendar_view=true\',\'auto\',false,true, $(\'#timesheet_div\').outerHeight()); return false;" data-ticketid="'.$attached_ticketid.'" class="view_ticket" '.($attached_ticketid > 0 ? '' : 'style="display:none;"').'>View</a>' : '').'</td>' : '').'
                '.(in_array('compensation',$value_config) ? '<td data-title="Hourly Compensation" style="text-align:center">'.number_format($comp_owed,2).'</td>' : '').'
                '.(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td data-title="Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').'
                '.(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td data-title="Mileage">'.($mileage > 0 ? number_format($mileage,2) : '0.00').'</td>' : '').'
                '.(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td data-title="Mileage Rate">$'.($mileage_rate > 0 ? number_format($mileage_rate,2) : '0.00').'</td>' : '').'

                '.(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td data-title="Mileage Total">$'.($mileage_cost > 0 ? number_format($mileage_cost,2) : '0.00').'</td>' : '');

                 if($current_page != 'time_cards.php') {
                    echo ($timesheet_approval_status_comments == 1 ? '<td data-title="Status">'.$approval_status.'</td>' : '').'
                '.($timesheet_approval_initials == 1 ? '<td data-title="Approval">'.get_contact($dbc, $approve_by).'</td>' : '').'
                '.($timesheet_approval_date == 1 ? '<td data-title="Approval Date">'.$approve_date.'</td>' : '');
                 }

                if($end_time_from != '') {
                    $end_time_from = '<br><b>Time Stopped by '.$end_time_from.'</b>';
                }
                echo (in_array('comment_box',$value_config) ? '<td data-title="Comments"><span>'.$comments.$end_time_from.'</span><img class="inline-img comment-row pull-right no-toggle" src="../img/icons/ROOK-reply-icon.png" title="Add Note"><input type="text" class="form-control" name="comment_box" value="'.$row['COMMENTS'].'" style="display:none;">'.($current_page != 'time_cards.php' && $mod == 'readonly' && $approv == 'Y' ? '<img class="inline-img edit-row pull-right no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit">' : '').(in_array($layout,['multi_line','ticket_task','position_dropdown']) ? '<img class="inline-img rem-row pull-right" src="../img/remove.png"><img class="inline-img add-row pull-right no-toggle" src="../img/icons/ROOK-add-icon.png" title="Edit">' : '').'</td>' : '').'
                '.(in_array('signature',$value_config) && $current_page == 'time_cards.php' ? '<td data-title="Signature" style="text-align:center" class="'.($show_separator==1 ? 'theme-color-border-bottom' : '').'">'.(!empty($all_signatures[$date]) ? '<img src="../Timesheet/download/'.$all_signatures[$date].'" style="height: 50%; width: auto;">' : ($security['edit'] > 0 ? '<label class="form-checkbox"><input type="checkbox" name="add_signature" onclick="addSignature(this);" value="'.$date.'"></label>' : '')).'</td>' : '').'
                '.($current_page != 'time_cards.php' ? '<td data-title="Select to Mark Paid"><label '.($mod == 'readonly' ? 'class="readonly-block"' : '').'><input type="checkbox" name="approv" data-uncheck="'.($current_page == 'payroll.php' ? 'Y' : 'N').'" value="'.($current_page == 'payroll.php' ? 'P' : 'Y').'" '.($mod == 'readonly' ? ($current_page == 'payroll.php' && $approv == 'P' ? 'checked' : ($current_page != 'payroll.php' && $approv == 'Y' ? 'checked' : '')).' readonly' : '').' /></label><img src="../img/empty.png" class="statusIcon inline-img no-toggle no-margin"></td>' : '');
            echo '</tr>';
            if(in_array('total_per_day',$value_config) && $date != $row['date']) {
                echo '<tr style="font-weight: bold;" class="'.($show_separator==1 ? 'theme-color-border-bottom' : '').'">
                    <td data-title="" colspan="'.$colspan.'">Day Totals</td>
                    '.(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : (in_array('total_hrs',$value_config) ? 'Total Hours' : 'Regular')).' Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['REG'],2) : time_decimal2time($date_total['REG'])).'</td>' : '').'
                    '.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['DRIVE'],2) : time_decimal2time($date_total['DRIVE'])).'</td>' : '').'
                    '.(in_array('direct_hrs',$value_config) ? '<td data-title="Direct Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['DIRECT'],2) : time_decimal2time($date_total['DIRECT'])).'</td>' : '').'
                    '.(in_array('indirect_hrs',$value_config) ? '<td data-title="Indirect Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['INDIRECT'],2) : time_decimal2time($date_total['INDIRECT'])).'</td>' : '').'
                    '.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['EXTRA'],2) : time_decimal2time($date_total['EXTRA'])).'</td>' : '').'
                    '.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['RELIEF'],2) : time_decimal2time($date_total['RELIEF'])).'</td>' : '').'
                    '.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SLEEP'],2) : time_decimal2time($date_total['SLEEP'])).'</td>' : '').'
                    '.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['TRAINING'],2) : time_decimal2time($date_total['TRAINING'])).'</td>' : '').'
                    '.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SICK_ADJ'],2) : time_decimal2time($date_total['SICK_ADJ'])).'</td>' : '').'
                    '.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SICK'],2) : time_decimal2time($date_total['SICK'])).'</td>' : '').'
                    '.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['STAT_AVAIL'],2) : time_decimal2time($date_total['STAT_AVAIL'])).'</td>' : '').'
                    '.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['STAT'],2) : time_decimal2time($date_total['STAT'])).'</td>' : '').'
                    '.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['VACA_AVAIL'],2) : time_decimal2time($date_total['VACA_AVAIL'])).'</td>' : '').'
                    '.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['VACA'],2) : time_decimal2time($date_total['VACA'])).'</td>' : '').'
                    '.(in_array('breaks',$value_config) ? '<td data-title="Breaks">'.($timesheet_time_format == 'decimal' ? number_format($date_total['BREAKS'],2) : time_decimal2time($date_total['BREAKS'])).'</td>' : '').'
                    '.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
                    '.(in_array('compensation',$value_config) ? '<td data-title="Total Hourly Compensation">'.number_format($date_total['COMP_TOTAL'],2).'</td>' : '').'
                    '.(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td data-title="Total Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').'
                    '.(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td data-title="Total Mileage">'.($mileage_total > 0 ? number_format($mileage_total,2) : '0.00').'</td>' : '').'
                    '.(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td data-title="Total Mileage Rate">$'.($mileage_rate_total > 0 ? number_format($mileage_rate_total,2) : '0.00').'</td>' : '').'
                    '.(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td data-title="Total Mileage Cost">$'.($mileage_cost_total > 0 ? number_format($mileage_cost_total,2) : '0.00').'</td>' : '');

                    if($current_page != 'time_cards.php') {
                    echo ($timesheet_approval_status_comments == 1 ? '<td data-title="Status"></td>' : '').'
                    '.($timesheet_approval_initials == 1 ? '<td data-title="Approval"></td>' : '').'
                    '.($timesheet_approval_date == 1 ? '<td data-title="Approval Date"></td>' : '');
                    }

                    echo '<td data-title="" colspan="'.((in_array('comment_box',$value_config) ? 1 : 0) + ($current_page != 'time_cards.php' ? 1 : (in_array('signature',$value_config) ? 1 : 0))).'"></td>
                </tr>';
            }
            if(!in_array($layout,['position_dropdown', 'ticket_task','multi_line']) || $date != $row['date']) {
                $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $date_total = ['HOURS'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
            }
            $post_i++;
        }
        //$expenses_owed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total`) `expenses_owed` FROM `expense` WHERE `deleted` = 0 AND `staff` = '$search_staff' AND `status` = 'Approved' AND `approval_date` BETWEEN '$search_start_date' AND '$search_end_date'"))['expenses_owed'];
        echo '<tr>
            <td data-title="" colspan="'.$colspan.'">Totals</td>
            '.(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : (in_array('total_hrs',$value_config) ? 'Total Hours' : 'Regular')).' Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).'</td>' : '').'
            '.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['DRIVE'],2) : time_decimal2time($total['DRIVE'])).'</td>' : '').'
            '.(in_array('direct_hrs',$value_config) ? '<td data-title="Direct Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['DIRECT'],2) : time_decimal2time($total['DIRECT'])).'</td>' : '').'
            '.(in_array('indirect_hrs',$value_config) ? '<td data-title="Indirect Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['INDIRECT'],2) : time_decimal2time($total['INDIRECT'])).'</td>' : '').'
            '.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA'])).'</td>' : '').'
            '.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF'])).'</td>' : '').'
            '.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP'])).'</td>' : '').'
            '.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING'])).'</td>' : '').'
            '.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ'])).'</td>' : '').'
            '.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK'])).'</td>' : '').'
            '.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL'])).'</td>' : '').'
            '.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT'])).'</td>' : '').'
            '.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL'])).'</td>' : '').'
            '.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA'])).'</td>' : '').'
            '.(in_array('breaks',$value_config) ? '<td data-title="Breaks">'.($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS'])).'</td>' : '').'
            '.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('compensation',$value_config) ? '<td data-title="Total Hourly Compensation">'.number_format($total['COMP_TOTAL'],2).'</td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td data-title="Total Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td data-title="Total Mileage">'.($mileage_total > 0 ? number_format($mileage_total,2) : '0.00').'</td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td data-title="Total Mileage Rate">$'.($mileage_rate_total > 0 ? number_format($mileage_rate_total,2) : '0.00').'</td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td data-title="Total Mileage Cost">$'.($mileage_cost_total > 0 ? number_format($mileage_cost_total,2) : '0.00').'</td>' : '');

            if($current_page != 'time_cards.php') {
           echo ($timesheet_approval_status_comments == 1 ? '<td data-title="Status"></td>' : '').'
            '.($timesheet_approval_initials == 1 ? '<td data-title="Approval"></td>' : '').'
            '.($timesheet_approval_date == 1 ? '<td data-title="Approval Date"></td>' : '');
            }

            echo '<td data-title="" colspan="'.((in_array('comment_box',$value_config) ? 1 : 0) + ($current_page != 'time_cards.php' ? 1 : (in_array('signature',$value_config) ? 1 : 0))).'"></td>
        </tr>';
        echo '<tr>
            <td colspan="'.$colspan.'">Year-to-date Totals</td>
            '.(in_array('total_hrs',$value_config) || in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('start_day_tile_separate',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('direct_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('indirect_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('extra_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('relief_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('sleep_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('training_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('sick_hrs',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK']+$sick_taken,2) : time_decimal2time($total['SICK']+$sick_taken)).'</td>' : '').'
            '.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL']+$stat_hours,2) : time_decimal2time($total['STAT_AVAIL']+$stat_hours)).'</td>' : '').'
            '.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT']+$stat_taken,2) : time_decimal2time($total['STAT']+$stat_taken)).'</td>' : '').'
            '.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL']+$vacation_hours,2) : time_decimal2time($total['VACA_AVAIL']+$vacation_hours)).'</td>' : '').'
            '.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA']+$vacation_taken,2) : time_decimal2time($total['VACA']+$vacation_taken)).'</td>' : '').'
            '.(in_array('breaks',$value_config) ? '<td data-title="Breaks"></td>' : '').'
            '.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
            '.(in_array('compensation',$value_config) ? '<td data-title=""></td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td data-title=""></td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td data-title=""></td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td data-title=""></td>' : '').'
            '.(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td data-title=""></td>' : '');

            if($current_page != 'time_cards.php') {
            echo ($timesheet_approval_status_comments == 1 ? '<td data-title="Status"></td>' : '').'
            '.($timesheet_approval_initials == 1 ? '<td data-title="Approval"></td>' : '').'
            '.($timesheet_approval_date == 1 ? '<td data-title="Approval Date"></td>' : '');
            }

            echo '<td colspan="'.((in_array('comment_box',$value_config) ? 1 : 0) + ($current_page != 'time_cards.php' ? 1 : (in_array('signature',$value_config) ? 1 : 0))).'"></td>
        </tr>'; ?>
        <?php while($row = mysqli_fetch_array( $result ))
        {
            $time_cards_id = $row['time_cards_id'];

            echo "<tr>";


            foreach($value['data'] as $tab_name => $tabs) {
                foreach($tabs as $field) {
                    if (strpos($value_config, ','.$field[2].',') !== FALSE) {

                        if($field[1] == 'tab') {
                            // do nothing
                        } else if($field[2] == 'staff') {
                            echo '<td>';
                            echo get_staff($dbc, $row[$field[2]]);
                            echo '</td>';
                        } else if($field[2] == 'business') {
                            echo '<td>';
                            echo get_client($dbc, $row[$field[2]]);
                            echo '</td>';
                        } else {
                            echo '<td>';
                            echo $row[$field[2]];
                            echo '</td>';
                        }

                    }
                }
            }

            echo '<td style="text-align:center;"><input type="checkbox" name="element" value="'.$time_cards_id.'" /></td>';
            echo "</tr>";
        } ?>
    </table>
</div>
<?php if($current_page != 'time_cards.php') {
    $profile_sig = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_description` WHERE `contactid` = '".$_SESSION['contactid']."'"))['stored_signature'];
    if(!empty($profile_sig)) {
        if(!file_exists('../Contacts/signatures/contact_sign_'.$_SESSION['contactid'].'.png')) {
            if(!file_exists('../Contacts/signatures')) {
                mkdir('../Contacts/signatures', 0777, true);
            }
            include_once('../phpsign/signature-to-image.php');
            $signature = sigJsonToImage(html_entity_decode($profile_sig));
            imagepng($signature, '../Contacts/signatures/contact_sign_'.$_SESSION['contactid'].'.png');
        }
        echo '<label class="form-checkbox"><input type="checkbox" name="use_profile_sig" value="1" onchange="useProfileSig(this);"> Use Profile Signature</input></label>';
        echo '<div class="profile_sig_box" style="display: none;"><img src="../Contacts/signatures/contact_sign_'.$_SESSION['contactid'].'.png"></div>';
    }
}
if($current_page == 'payroll.php') { ?>
    <div class="form-group">
        <label for="<?= $field[2] ?>" class="col-sm-2 control-label">Payroll Signature: </label>
        <div class="col-sm-8">
            <?php include ('../phpsign/sign.php'); ?>
        </div>
    </div>
<?php } else if($current_page != 'time_cards.php') { ?>
    <div class="form-group">
        <label for="<?= $field[2] ?>" class="col-sm-2 control-label">Approval Signature: </label>
        <div class="col-sm-8">
            <?php include ('../phpsign/sign.php'); ?>
    </div>
<?php } ?>
<div class="clearfix"></div>
<?php include('../Reports/compensation_function.php');
$stat_holidays = [];
foreach(mysqli_fetch_all(mysqli_query($dbc, "SELECT `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0")) as $stat_day) {
    $stat_holidays[] = $stat_day[0];
}
$stat_holidays = implode(',', $stat_holidays);
echo report_compensation($dbc, $search_start_date, $search_end_date, '', '', '', $search_staff, $stat_holidays, ['New','Refund','Adjustment']); ?>
<div class="clearfix"></div>
<?php include('../Timesheet/time_cards_summary.php'); ?>