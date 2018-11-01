<script>
$(document).ready(function() {
	$('[name=ticket_min_hours],[name=timesheet_hour_intervals],[name=ticket_next_step_timesheet]').change(saveFields);
});
function saveFields() {
	if(this.name == 'ticket_min_hours') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_min_hours',
				value: $('[name=ticket_min_hours]').val()
			}
		});
	} else if(this.name == 'timesheet_hour_intervals') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'timesheet_hour_intervals',
				value: $('[name=timesheet_hour_intervals]').val()
			}
		});
	} else if(this.name == 'ticket_next_step_timesheet') {
        var options = [];
        $('[name=ticket_next_step_timesheet][value]:checked').each(function() {
            options.push(this.value);
        });
        if(options.length > 0) {
            $('[name=ticket_next_step_timesheet]').not('[value]').removeAttr('checked');
        } else {
            $('[name=ticket_next_step_timesheet]').not('[value]').prop('checked',true);
        }
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_next_step_timesheet',
				value: options.join(',')
			}
		});
	}
}
</script>
<!-- <h1>Time Tracking</h1> -->
<div class="form-group">
	<label class="col-sm-4 control-label">Minimum Hours per <?= TICKET_NOUN ?></label>
	<div class="col-sm-8">
		<input type="number" min="0" max="24" step="0.25" name="ticket_min_hours" value="<?= get_config($dbc, 'ticket_min_hours') ?>" class="form-control">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Time Sheet Tracking Intervals</label>
	<div class="col-sm-8">
		<input type="number" min="0" max="2" step="0.05" name="timesheet_hour_intervals" value="<?= get_config($dbc, 'timesheet_hour_intervals') ?>" class="form-control">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Next Steps Page</label>
	<div class="col-sm-8">
		<?php $ticket_next_step_timesheet = array_filter(explode(',',get_config($dbc, 'ticket_next_step_timesheet'))); ?>
        <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" <?= empty($ticket_next_step_timesheet) ? 'checked' : '' ?>> Disable</label>
        <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" value="all_tasks" <?= in_array('all_tasks',$ticket_next_step_timesheet) ? 'checked' : '' ?>> All Staff Tasks</label>
        <?php $task_group = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
        while($group = $task_group->fetch_assoc()) { ?>
            <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" value="task_<?= config_safe_str($group['category']) ?>" <?= in_array('task_'.config_safe_str($group['category']),$ticket_next_step_timesheet) ? 'checked' : '' ?>> <?= $group['category'] ?> Tasks</label>
        <?php } ?>
        <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" value="next_ticket" <?= in_array('next_ticket',$ticket_next_step_timesheet) ? 'checked' : '' ?>> Next <?= TICKET_NOUN ?></label>
        <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" value="break" <?= in_array('break',$ticket_next_step_timesheet) ? 'checked' : '' ?>> Break</label>
        <label class="form-checkbox"><input type="checkbox" name="ticket_next_step_timesheet" value="end_day" <?= in_array('end_day',$ticket_next_step_timesheet) ? 'checked' : '' ?>> <?= get_config($dbc, 'timesheet_end_tile') ?></label>
	</div>
</div>