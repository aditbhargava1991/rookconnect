<!-- Daysheet Field Config -->
<script type="text/javascript">
$(document).ready(function() {
    $('.daysheet_block input').on('click', function() {
        var field_name = this.name;
		if(field_name == 'planner_end_day') {
			$.post('../ajax_all.php?action=general_config', { name: 'planner_end_day', value: this.checked ? this.value : $(this).data('off')}, function(response) {console.log(response)});
		} else {
			var settings_contactid = $('[name="settings_contactid"]').val();
			var daysheet_styling = $('[name="daysheet_styling"]:checked').val();
			var ticket_slider = $('[name="daysheet_ticket_slider"]:checked').val();
			var field_list = '';
			$('[name="daysheet_fields_config[]"]:checked').each(function() {
				field_list += this.value + ',';
			});
			var daysheet_ticket_fields = '';
			$('[name="daysheet_ticket_fields[]"]:checked').each(function() {
				daysheet_ticket_fields += this.value + ',';
			});
			var day_list = '';
			$('[name="daysheet_weekly_config[]"]:checked').each(function() {
				day_list += this.value + ',';
			});
			var button_list = '';
			$('[name="daysheet_button_config[]"]:checked').each(function() {
				button_list += this.value + ',';
			});
			var daysheet_rightside_views = '';
			$('[name="daysheet_rightside_views[]"]:checked').each(function() {
				daysheet_rightside_views += this.value + ',';
			});
            var daysheet_ticket_default_mode = $('[name="daysheet_ticket_default_mode"]:checked').val();
            var quick_action_icons = [];
            $('[name="quick_action_icons[]"]:checked').each(function() {
                quick_action_icons.push(this.value);
            })
            quick_action_icons = quick_action_icons.join(',');
			$.ajax({
				url: '../Profile/profile_ajax.php?fill=daysheet_config',
				method: 'POST',
				data: { field_name: field_name, daysheet_styling: daysheet_styling, ticket_slider: ticket_slider, field_list: field_list, daysheet_ticket_fields: daysheet_ticket_fields, day_list: day_list, button_list: button_list, daysheet_rightside_views: daysheet_rightside_views, daysheet_ticket_default_mode: daysheet_ticket_default_mode, quick_action_icons: quick_action_icons, settings_contactid: settings_contactid },
				response: 'html',
				success: function(response) {
					// console.log(response);
				}
			});
		}
    });
	$('[name=daysheet_ticket_combine_contact_type]').change(function() {console.log(this.value,this.name);
		$.post('../ajax_all.php?action=general_config',{name:this.name,value:this.value},function(response){console.log(response);});
	});
});
</script>
<?php
    $settings_type = 'user';
	$daysheet_ticket_fields = explode(',',get_config($dbc, 'daysheet_ticket_fields'));
	$daysheet_ticket_combine_contact_type = get_config($dbc, 'daysheet_ticket_combine_contact_type');
    if(!empty($_GET['settings_type'])) {
        $settings_type = $_GET['settings_type'];
    }
    if($settings_type == 'software') {
        $daysheet_styling = get_config($dbc, 'daysheet_styling');
        $daysheet_ticket_slider = get_config($dbc, 'daysheet_ticket_slider');
        $daysheet_fields_config = explode(',', get_config($dbc, 'daysheet_fields_config'));
        $daysheet_weekly_config = explode(',', get_config($dbc, 'daysheet_weekly_config'));
        $daysheet_button_config = explode(',', get_config($dbc, 'daysheet_button_config'));
        $daysheet_rightside_views = explode(',', get_config($dbc, 'daysheet_rightside_views'));
        $daysheet_ticket_default_mode = get_config($dbc, 'daysheet_ticket_default_mode');
    } else {
        $daysheet_styling = get_user_settings()['daysheet_styling'];
        if(empty($daysheet_styling)) {
            $daysheet_styling = get_config($dbc, 'daysheet_styling');
        }
        $daysheet_fields_config = explode(',', get_user_settings()['daysheet_fields_config']);
        if(empty(get_user_settings()['daysheet_fields_config'])) {
            $daysheet_fields_config = explode(',', get_config($dbc, 'daysheet_fields_config'));
        }
        $daysheet_weekly_config = explode(',', get_user_settings()['daysheet_weekly_config']);
        if(empty(get_user_settings()['daysheet_weekly_config'])) {
            $daysheet_weekly_config = explode(',', get_config($dbc, 'daysheet_weekly_config'));
        }
        $daysheet_button_config = explode(',', get_user_settings()['daysheet_button_config']);
        if(empty(get_user_settings()['daysheet_button_config'])) {
            $daysheet_button_config = explode(',', get_config($dbc, 'daysheet_button_config'));
        }
        $daysheet_rightside_views = explode(',', get_user_settings()['daysheet_rightside_views']);
        if(empty(get_user_settings()['daysheet_rightside_views'])) {
            $daysheet_rightside_views = explode(',', get_config($dbc, 'daysheet_rightside_views'));
        }
    }
    if(empty($daysheet_styling)) {
        $daysheet_styling = 'card';
    }
    if(empty($daysheet_rightside_views) || (count($daysheet_rightside_views) == 1 && empty($daysheet_rightside_views[0]))) {
        $daysheet_rightside_views = ['Journal','Weekly Overview','Monthly Overview'];
    }
?>
<div class="col-sm-12 main-screen-details daysheet_block">
    <h1><?= $settings_type == 'software' ? 'Software Default Settings' : 'User Settings' ?></h1>
    <input type="hidden" name="settings_contactid" value="<?= $settings_type == 'software' ? 'software' : $contactid ?>">
    <div class="form-group block-group">
        <h4>Choose Layout</h4>
        <label class="col-sm-4 control-label">Layout:</label>
        <div class="col-sm-8 block-group">
            <label class="form-checkbox"><input type="radio" <?= $daysheet_styling == 'list' ? 'checked' : '' ?> name="daysheet_styling" value="list">List</label>
            <label class="form-checkbox"><input type="radio" <?= empty($daysheet_styling) || $daysheet_styling == 'card' ? 'checked' : '' ?> name="daysheet_styling" value="card">Cards</label>
        </div>
        <h4>Choose Fields to Display</h4>
        <label class="col-sm-4 control-label">Fields:</label>
        <div class="col-sm-8 block-group">
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Reminders', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Reminders">Reminders</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Tickets', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Tickets"><?= TICKET_TILE ?></label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Tasks', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Tasks">Tasks</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Checklists', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Checklists">Checklists</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Communication', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Communication">Communications</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Support', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Support">Support Requests</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Shifts', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Shifts">Shifts</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Tags', $daysheet_fields_config) ? 'checked' : '' ?> name="daysheet_fields_config[]" value="Tags">Tags</label>
        </div>
        <div class="clearfix"></div>
        <?php if($settings_type == 'software') { ?>
            <h4>Choose <?= TICKET_NOUN ?> Slider View Layout</h4>
            <label class="col-sm-4 control-label">Quick Action Icons:</label>
            <div class="col-sm-8 block-group">
                <?php $quick_action_icons = explode(',',get_config($dbc, 'daysheet_quick_action_icons')); ?>
                <label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reply',$quick_action_icons) ? 'checked' : '' ?> value="reply"> <img class="inline-img" src="../img/icons/ROOK-reply-icon.png"> Notes</label>
                <label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('email',$quick_action_icons) ? 'checked' : '' ?> value="email"> <img class="inline-img" src="../img/icons/ROOK-email-icon.png"> Email</label>
                <label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
                <label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Default Slider Window View:</label>
            <div class="col-sm-8 block-group">
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_slider" value="full" <?= $daysheet_ticket_slider != 'accordion' ? 'checked="checked"' : '' ?>> Full View</label>
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_slider" value="accordion" <?= $daysheet_ticket_slider == 'accordion' ? 'checked="checked"' : '' ?>> Accordion View</label>
            </div>
            <div class="clearfix"></div>
            <h4>Choose <?= TICKET_NOUN ?> Default Mode</h4>
            <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Default Mode:</label>
            <div class="col-sm-8 block-group">
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_default_mode" value="default" <?= $daysheet_ticket_default_mode != 'action' ? 'checked="checked"' : '' ?>> Default Mode</label>
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_default_mode" value="action" <?= $daysheet_ticket_default_mode == 'action' ? 'checked="checked"' : '' ?>> Action Mode</label>
            </div>
            <div class="clearfix"></div>
            <h4>Choose Fields to Display for <?= TICKET_TILE ?></h4>
            <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Fields:</label>
            <div class="col-sm-8 block-group">
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Business', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Business">Business</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Project', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Project"><?= PROJECT_NOUN ?></label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Warehouse Indicator', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Warehouse Indicator">Warehouse Indicator</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Staff', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Staff"> Staff</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Customer', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Customer">Customer</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Service Template', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Service Template">Service Template</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Delivery Type', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Delivery Type">Delivery Type</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Address', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Address">Address</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Map Link', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Map Link">Google Map Link</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Start Time', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Start Time">Start Time</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('ETA', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="ETA">ETA Window</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Availability', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Availability">Availability Window</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Total Budget Time', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Total Budget Time">Total Budget Time</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Service Time Estimate', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Service Time Estimate">Service Time Estimate</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Time Estimate', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Time Estimate">Time Estimate</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Attachment Indicator', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Attachment Indicator">Attachment Indicator Icon</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Comment Indicator', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Comment Indicator">Comment Indicator Icon</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Delivery Notes', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Delivery Notes">Delivery Notes</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Combine Warehouse Stops', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Combine Warehouse Stops">Combine Warehouse Stops</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Combine Pick Up Stops', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Combine Pick Up Stops">Combine Pick Up Stops</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Combined Details with Confirm', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Combined Details with Confirm">Include Detail Checkbox with Confirmation when Combined</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Details with Confirm', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Details with Confirm">Include Detail Checkbox with Confirmation</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Sort Completed to End', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Sort Completed to End">Display Completed <?= TICKET_TILE ?> at End</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Site Address', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Site Address"> Site Address</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Site Notes', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Site Notes"> Site Notes</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Key Number', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Key Number"> Key Number</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Door Code Number', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Door Code Number"> Door Code Number</label>
                <label class="form-checkbox"><input type="checkbox" <?= in_array('Alarm Code Number', $daysheet_ticket_fields) ? 'checked' : '' ?> name="daysheet_ticket_fields[]" value="Alarm Code Number"> Alarm Code Number</label>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label"><?= CONTACTS_NOUN ?> for Combined <?= TICKET_TILE ?>:</label>
            <div class="col-sm-8 block-group">
				<select class="chosen-select-deselect" data-placeholder="Select <?= CONTACTS_TILE ?> Category" name="daysheet_ticket_combine_contact_type"><option />
					<?php foreach(explode(',',get_config($dbc,'all_contact_tabs')) as $contact_type) { ?>
						<option <?= $contact_type == $daysheet_ticket_combine_contact_type ? 'selected' : '' ?> value="<?= $contact_type ?>"><?= $contact_type ?></option>
					<?php } ?>
				</select>
            </div>
            <div class="clearfix"></div>
        <?php } ?>
        <h4>Choose Buttons to Display</h4>
        <label class="col-sm-4 control-label">Buttons:</label>
        <div class="col-sm-8 block-group">
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Notes', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Notes">My Notes</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Scrum Notes', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="Scrum Notes">Scrum Notes</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Projects', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Projects">My Projects</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Tickets', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Tickets">My <?= TICKET_TILE ?></label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Checklists', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Checklists">My Checklists</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Support', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Support">My Support Requests</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Tasks', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Tasks">My Tasks</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Communications', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Communications">My Communications</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Sales', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Sales">My <?= SALES_TILE ?></label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Shifts', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Shifts">My Shifts</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Time Sheets', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Time Sheets">My Time Sheet</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('My Tags', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="My Tags">My Tags</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Attached Contact Forms', $daysheet_button_config) ? 'checked' : '' ?> name="daysheet_button_config[]" value="Attached Contact Forms">Attached Contact Forms (based on Match)</label>
			<?php if($settings_type == 'software') { ?>
				<label class="form-checkbox"><input type="checkbox" <?= in_array('My Notifications', $daysheet_button_config) ? 'checked' : '' ?> data-off="hide" name="daysheet_button_config[]" value="My Notifications">My Notifications</label>
				<label class="form-checkbox"><input type="checkbox" <?= get_config($dbc, 'planner_end_day') == 'show' ? 'checked' : '' ?> data-off="hide" name="planner_end_day" value="show">End Day</label>
			<?php } ?>
        </div>
        <div class="clearfix"></div>
        <h4>Choose Days to Display in Weekly Overview</h4>
        <label class="col-sm-4 control-label">Days of Week:</label>
        <div class="col-sm-8 block-group">
            <label class="form-checkbox"><input type="checkbox" <?= in_array('1', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="1">Monday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('2', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="2">Tuesday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('3', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="3">Wednesday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('4', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="4">Thursday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('5', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="5">Friday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('6', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="6">Saturday</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('7', $daysheet_weekly_config) ? 'checked' : '' ?> name="daysheet_weekly_config[]" value="7">Sunday</label>
        </div>
        <div class="clearfix"></div>
        <h4>Choose Right Side Views</h4>
        <label class="col-sm-4 control-label">Right Side Views:</label>
        <div class="col-sm-8 block-group">
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Journal', $daysheet_rightside_views) ? 'checked' : '' ?> name="daysheet_rightside_views[]" value="Journal">Journal</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Weekly Overview', $daysheet_rightside_views) ? 'checked' : '' ?> name="daysheet_rightside_views[]" value="Weekly Overview">Weekly Overview</label>
            <label class="form-checkbox"><input type="checkbox" <?= in_array('Monthly Overview', $daysheet_rightside_views) ? 'checked' : '' ?> name="daysheet_rightside_views[]" value="Monthly Overview">Monthly Overview</label>
        </div>
    </div>
</div>