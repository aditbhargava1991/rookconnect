<?php include('../include.php');

include('calendar_defaults.php');
include_once('calendar_functions_inc.php');
include_once('calendar_settings_inc.php');
include_once('calendar_js_inc.php');
// error_reporting(E_ALL);
$detect = new Mobile_Detect;
$is_mobile = ( $detect->isMobile() ) ? true : false;
if($is_mobile) {
	header("Location: ".WEBSITE_URL."/Calendar/calendars_mobile.php");
}

// Reset active blocks
if($is_customer && $_GET['type'] == 'schedule') {
	$new_today_date = empty($_GET['date']) ? date('Y-m-d') : $_GET['date'];
	$result = mysqli_query($dbc, "SELECT `equipmentid` FROM `equipment_assignment` WHERE `deleted` = 0 AND DATE(`start_date`) <= '$new_today_date' AND DATE(`end_date`) >= '$new_today_date' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$new_today_date,%'");
	$appt_calendar_equipment = [];
	while($row = mysqli_fetch_assoc($result)) {
		$appt_calendar_equipment[] = $row['equipmentid'];
	}
	$appt_calendar_equipment = implode(',',array_unique($appt_calendar_equipment));
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$appt_calendar_equipment' WHERE `contactid`='".$_SESSION['contactid']."'");
} else if(strpos(','.strtolower(STAFF_CATS).',', ",'".strtolower(get_contact($dbc,$_SESSION['contactid'],'category'))."',") !== FALSE) {
	$calendar_reset_active = get_config($dbc, 'calendar_reset_active');
	$calendar_blocks_last_reloaded = get_user_settings()['calendar_blocks_last_reloaded'];
	if($_GET['type'] == 'schedule' && $_GET['retrieve_assigned'] == 1) {
		$new_today_date = empty($_GET['date']) ? date('Y-m-d') : $_GET['date'];
		$result = mysqli_query($dbc, "SELECT `equipmentid` FROM `equipment_assignment` WHERE `deleted` = 0 AND DATE(`start_date`) <= '$new_today_date' AND DATE(`end_date`) >= '$new_today_date' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$new_today_date,%'");
		$appt_calendar_equipment = [];
		while($row = mysqli_fetch_assoc($result)) {
			$appt_calendar_equipment[] = $row['equipmentid'];
		}
		$appt_calendar_equipment = implode(',',array_unique($appt_calendar_equipment));
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$appt_calendar_equipment' WHERE `contactid`='".$_SESSION['contactid']."'");
	} else if($_GET['mode'] == 'summary') {
		if($_GET['type'] == 'schedule') {
			$appt_calendar_equipment = '';
			mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$appt_calendar_equipment' WHERE `contactid`='".$_SESSION['contactid']."'");
		}
	} else if($_GET['mode'] == 'staff_summary') {
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_staff`='', `appt_calendar_teams`='' WHERE `contactid`='".$_SESSION['contactid']."'");
	} else {
		if((!isset($_GET['date']) && $calendar_reset_active == 1) || ($calendar_reset_active == 2 && strtotime(date('Y-m-d', strtotime($calendar_blocks_last_reloaded))) < strtotime(date('Y-m-d')))) {
			$calendar_reset_active_mode = get_config($dbc, 'calendar_reset_active_mode');
			if($calendar_reset_active_mode == 'session_user') {
				mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_regions`='', `appt_calendar_locations`='', `appt_calendar_classifications`='', `appt_calendar_staff`='".$_SESSION['contactid']."', `appt_calendar_contacts`='', `appt_calendar_equipment`='', `appt_calendar_teams`='', `appt_calendar_clients`='' WHERE `contactid`='".$_SESSION['contactid']."'");
			} else if($calendar_reset_active_mode == 'session_user active_equip') {
				$today_date = date('Y-m-d');
				$equipment_ids = $dbc->query("SELECT `equipmentid`, `region`, `classification`, `location` FROM `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment_staff`.`equipment_assignmentid`=`equipment_assignment`.`equipment_assignmentid` WHERE `equipment_assignment_staff`.`deleted`=0 AND `equipment_assignment`.`deleted`=0 AND `equipment_assignment_staff`.`contactid`='".$_SESSION['contactid']."' AND DATE(`equipment_assignment`.`start_date`) <= '$today_date' AND DATE(`equipment_assignment`.`end_date`) >= '$today_date'");
				$appt_calendar_equipment = [];
				$appt_calendar_regions = [];
				$appt_calendar_locations = [];
				$appt_calendar_classifications = [];
				while($row = $equipment_ids->fetch_assoc()) {
					$appt_calendar_equipment[] = $row['equipmentid'];
					$appt_calendar_regions[] = $row['region'];
					$appt_calendar_locations[] = $row['location'];
					$appt_calendar_classifications[] = $row['classification'];
				}
				$appt_calendar_equipment = implode(',',array_unique($appt_calendar_equipment));
				$appt_calendar_regions = implode(',',array_unique($appt_calendar_regions));
				$appt_calendar_locations = implode(',',array_unique($appt_calendar_locations));
				$appt_calendar_classifications = implode(',',array_unique($appt_calendar_classifications));
				mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_regions`='$appt_calendar_regions', `appt_calendar_locations`='$appt_calendar_locations', `appt_calendar_classifications`='$appt_calendar_classifications', `appt_calendar_staff`='".$_SESSION['contactid']."', `appt_calendar_contacts`='', `appt_calendar_equipment`='$appt_calendar_equipment', `appt_calendar_teams`='', `appt_calendar_clients`='' WHERE `contactid`='".$_SESSION['contactid']."'");
			}
			set_user_settings($dbc, 'calendar_blocks_last_reloaded', date('Y-m-d H:i:s'));
		}
	}
}

// Auto refresh calendar after set period of time
$calendar_auto_refresh = get_config($dbc, 'calendar_auto_refresh');
if(!empty($calendar_auto_refresh)) {
	$calendar_auto_refresh = date_parse($calendar_auto_refresh);
	$calendar_auto_refresh = ($calendar_auto_refresh['hour'] * 3600) + ($calendar_auto_refresh['minute'] * 60);
}

// Calendar Main Screen ?>
<script>
function resize_rows() {
    <?php if(get_config($dbc, 'auto_size_calendar_blocks') == 'true') { ?>
        var min_size = 0;
        $('.used-block').each(function() {
            var blocks = $(this).data('blocks');
            if(blocks == 0) {
                blocks = 1;
            }
            if(min_size < $(this).find('span').innerHeight() / blocks + 5) {
                min_size = $(this).find('span').innerHeight() / blocks + 5;
            }
        });
        var max_size = $('.calendar_view').height() / 2;
        if(min_size > max_size) {
        	min_size = max_size;
        }
        $('.calendar_view td').innerHeight(min_size).find('div.used_block').each(function() {
            $(this).innerHeight($(this).data('blocks') * min_size);
        });
    <?php } ?>
    <?php if(get_config($dbc, 'auto_show_time') == 'true') { ?>
        var top = $('td.today-active a[data-currenttime]').filter(function() { return Date.parse($(this).data('currentdate')+' '+$(this).data('currenttime')) < Date.now(); }).last();
        if(top.offset() != undefined) {
            $('.calendar_view').scrollTop(top.offset().top);
        }
    <?php } ?>
}
var unbooked_reload = '';
$(document).on("overlayIFrameSliderLoad", function() {
	clearInterval(unbooked_reload);
	if(window.parent.$('#unbooked_list').is(':visible') && typeof window.parent.retrieveUnbookedData != 'undefined') {
		unbooked_reload = setInterval(function() {
			if(!(window.parent.$('.iframe_overlay').is(':visible'))) {
				clearInterval(unbooked_reload);
				window.parent.retrieveUnbookedData();
			}
		},1000);
	}
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('calendar_rook');
?>
<div id="calendar_div" class="container calendar">
	<div id="active_class_users" class="block-button" style="display: none;"></div>
	<div id="ticket_assigned_staff" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
	<div id="dialog-universal" title="Select a Type" style="display: none;">
		What would you like to add?
	</div>
	<div id="dialog-confirm" title="Edit Recurring Shift" style="display: none;">
		Would you like to update only this Shift, all recurring Shifts, or following recurring Shifts?
	</div>
	<div id="dialog-staff-add" title="Add Staff or Replace Staff" style="display: none;">
		Would you like to add this Staff or replace all Staff with the new Staff?
	</div>
	<div id="dialog-quick-add-shift" title="Quick Add Shift" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Staff:</label>
			<div class="col-sm-8">
				<select name="quick_add_staff" class="chosen-select-deselect form-control">
					<option></option>
					<?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC);
					foreach($contact_list as $contact) {
						echo '<option value="'.$contact['contactid'].'">'.$contact['full_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
		<?php $shift_client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
		if(!empty($shift_client_type)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?= $shift_client_type ?>:</label>
				<div class="col-sm-8">
					<select name="quick_add_client" class="chosen-select-deselect form-control">
						<option></option>
						<?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC);
						foreach($contact_list as $contact) {
							echo '<option value="'.$contact['contactid'].'">'.$contact['full_name'].'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Time:<br><em>(eg. 8am - 4pm)</em></label>
			<div class="col-sm-8">
				<input type="text" name="quick_add_time" class="form-control" value="">
			</div>
		</div>
	</div>
	<div id="dialog-scheduled-time" title="Change Scheduled Time" style="display: none;">
		<input type="hidden" name="change_ticket_table" value="">
		<input type="hidden" name="change_ticket_id" value="">
		<div class="form-group">
			<label class="col-sm-4 control-label">Scheduled Date:</label>
			<div class="col-sm-8">
				<input type="text" name="change_to_do_date" value="" class="form-control datepicker">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Scheduled Start Time:</label>
			<div class="col-sm-8">
				<input type="text" name="change_to_do_start_time" value="" class="form-control datetimepicker">
			</div>
			<div class="clearfix"></div>
		</div>
		<!--<div class="form-group">
			<label class="col-sm-4 control-label">Scheduled End Time:</label>
			<div class="col-sm-8">
				<input type="text" name="change_to_do_end_time" value="" class="form-control datetimepicker">
			</div>
			<div class="clearfix"></div>
		</div>-->
	</div>
	<div id="dialog_create_recurrence_cal" title="Recurrence Details" style="display: none;">
		<script type="text/javascript">
		$(document).on('change', 'select[name="recurrence_repeat_type"],select[name="recurrence_repeat_monthly_type"]', function() {
			var repeat_type = $('[name="recurrence_repeat_type"]').val();
			var month_type = $('[name="recurrence_repeat_monthly_type"]').val();
			if(repeat_type == 'week') {
				$('.recurrence_monthly_settings').hide();
				$('.recurrence_repeat_days').show();
			} else if(repeat_type == 'month') {
				$('.recurrence_monthly_settings').show();
				if(month_type != 'day') {
					$('.recurrence_repeat_days').show();
				} else {
					$('.recurrence_repeat_days').hide();
				}
			} else {
				$('.recurrence_monthly_settings').hide();
				$('.recurrence_repeat_days').hide();
			}
		});
		</script><span class="ui-helper-hidden-accessible"><input type="text"/></span>
		<div class="form-group">
			<label class="col-sm-4 control-label">Start Date:</label>
			<div class="col-sm-8">
				<input type="text" name="recurrence_start_date" class="form-control datepicker" value="<?= date('Y-m-d') ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">End Date:</label>
			<div class="col-sm-8">
				<input type="text" name="recurrence_end_date" class="form-control datepicker" value="">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Repeats:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_type" class="form-control chosen-select-deselect">
					<option value="day">Daily</option>
					<option value="week" selected>Weekly</option>
					<option value="month">Monthly</option>
				</select>
			</div>
		</div>
		<div class="form-group recurrence_monthly_settings" style="display:none;">
			<label class="col-sm-4 control-label">Repeat Type:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_monthly_type" class="form-control  chosen-select-deselect">
					<option value="day" selected>By Day</option>
					<option value="first">First Week of Month</option>
					<option value="second">Second Week of Month</option>
					<option value="third">Third Week of Month</option>
					<option value="fourth">Fourth Week of Month</option>
					<option value="last">Last Week of Month</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Repeat Interval:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_interval" class="form-control chosen-select-deselect">
	                <?php for ($repeat_i = 1; $repeat_i <= 30; $repeat_i++) {
	                    echo '<option value="'.$repeat_i.'">'.$repeat_i.'</option>';
	                } ?>
				</select>
			</div>
		</div>
		<div class="form-group recurrence_repeat_days">
			<label class="col-sm-4 control-label">Repeat Days:</label>
			<div class="col-sm-8">
	            <?php $days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	            foreach ($days_of_week as $day_of_week_label) {
	                echo '<label style="padding-right: 0.5em; "><input type="checkbox" name="recurrence_repeat_days[]" value="'.$day_of_week_label.'">'.$day_of_week_label.'</label>';
	            } ?>
			</div>
		</div>
	</div>
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="calendar_iframe" src=""></iframe>
		</div>
	</div>
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">
		<div class="main-screen" style="background-color: #fff; border-width: 0; margin-top: -20px;">
            <div id="camera_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
            <div id="signature_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
            <div id="star_rating_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
            <div id="customer_notes_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
			<div class="subtab-menu pull-left">
				<a href="" onclick="toggleMenu(this); return false;"><span class="block-item"><?= $calendar_label ?><img src="<?= WEBSITE_URL ?>/img/icons/dropdown-arrow.png" style="height: 1em; margin: 0.25em 0;" class="counterclockwise pull-right"></span></a>
				<?php if($_GET['type'] != 'my' && in_array('My Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'My Calendar')) { ?><a href="?type=my&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">My Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'uni' && in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) { ?><a href="?type=uni&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Universal Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'appt' && in_array('Appointment Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Appointment Calendar')) { ?><a href="?type=appt&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Appointment Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'schedule' && in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) { ?><a href="?type=schedule&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Dispatch  Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'staff' && in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) { ?><a href="?type=staff&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Staff Schedule Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'estimates' && in_array('Sales Estimates Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Sales Estimates Calendar')) { ?><a href="?type=estimates&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Sales Estimates Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'ticket' && in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) { ?><a href="?type=ticket&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item"><?= TICKET_NOUN ?> Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'shift' && in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) { ?><a href="?type=shift&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Shift Calendar</span></a><?php } ?>
				<?php if($_GET['type'] != 'event' && in_array('Events Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Events Calendar')) { ?><a href="?type=event&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item">Events Calendar</span></a><?php } ?>
			</div>
			<?php if(($_GET['type'] == 'uni' || $_GET['type'] == 'my') && $use_shifts != '') { ?>
				<a href="?type=<?= $_GET['type'] ?>&mode=staff&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'staff' ? 'active' : '' ?>" style="float: left;">Schedule</span></a>
				<a href="?type=<?= $_GET['type'] ?>&mode=shift&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'shift' ? 'active' : '' ?>" style="float: left;">Shifts</span></a>
				<?php if($staff_summary == 1) { ?>
					<a href="?type=uni&mode=staff_summary&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'staff_summary' ? 'active' : '' ?>" style="float: left;">Staff Summary</span></a>
				<?php } ?>
				<?php if($day_summary_tab == 1) { ?>
					<a href="?type=uni&mode=day_summary&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'day_summary' ? 'active' : '' ?>" style="float: left;">Day Summary</span></a>
				<?php } ?>
			<?php } else if($_GET['type'] == 'staff' && $_GET['view'] != 'monthly' && in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) { ?>
				<a href="?type=staff&mode=staff&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] != 'client' && $_GET['mode'] != 'tickets' ? 'active' : '' ?>" style="float: left;">Staff</span></a>
				<a href="?type=staff&mode=client&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'client' ? 'active' : '' ?>" style="float: left;"><?= (!empty(get_config($dbc, 'staff_schedule_client_type')) ? get_config($dbc, 'staff_schedule_client_type') : 'Clients') ?></span></a>
				<!-- <a href="?type=staff&mode=tickets&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'tickets' ? 'active' : '' ?>" style="float: left;"><?= TICKET_TILE ?></span></a> -->
			<?php } else if($_GET['type'] == 'schedule' && in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) { ?>
				<a href="?type=schedule&mode=schedule&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'schedule' ? 'active' : '' ?>" style="float: left;">Schedule</span></a>
				<?php if($allowed_dispatch_staff > 0 && !$is_customer) { ?><a href="?type=schedule&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>&mode=staff"><span class="block-item <?= $_GET['mode'] == 'staff' ? 'active' : '' ?>" style="float: left;">Staff</span></a><?php } ?>
				<?php if($allowed_dispatch_staff > 0 && !empty($contractor_category) && !$is_customer) { ?><a href="?type=schedule&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>&mode=contractors"><span class="block-item <?= $_GET['mode'] == 'contractors' ? 'active' : '' ?>" style="float: left;">Contractors</span></a><?php } ?>
				<?php if($scheduling_summary_view == 1 && !$is_customer) { ?><a href="?type=schedule&view=<?= $_GET['view'] ?>&mode=summary"><span class="block-item <?= $_GET['mode'] == 'summary' ? 'active' : '' ?>" style="float: left;">Summary</span></a><?php } ?>
			<?php } else if($_GET['type'] == 'shift' && $_GET['view'] != 'monthly' && in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) {
				$shift_client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
				if(!empty($shift_client_type)) { ?>
					<a href="?type=shift&mode=client&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'client' ? 'active' : '' ?>" style="float: left;"><?= $shift_client_type ?></span></a>
					<a href="?type=shift&mode=staff&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] != 'client' && $_GET['mode'] != 'tickets' ? 'active' : '' ?>" style="float: left;">Staff</span></a>
				<?php }
			} else if($_GET['type'] == 'ticket' && in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar') && ($staff_summary == 1 || $ticket_summary_tab == 1 || $client_tab == 1)) { ?>
				<a href="?type=ticket&mode=&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= empty($_GET['mode']) || $_GET['mode'] == 'staff' ? 'active' : '' ?>" style="float: left;">Staff</span></a>
				<?php if($client_tab == 1) { ?>
					<a href="?type=ticket&mode=client&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'client' ? 'active' : '' ?>" style="float: left;">Client</span></a>
				<?php } ?>
				<?php if($staff_summary == 1) { ?>
					<a href="?type=ticket&mode=staff_summary&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'staff_summary' ? 'active' : '' ?>" style="float: left;">Staff Summary</span></a>
				<?php } ?>
				<?php if($ticket_summary_tab == 1) { ?>
					<a href="?type=ticket&mode=ticket_summary&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><span class="block-item <?= $_GET['mode'] == 'ticket_summary' ? 'active' : '' ?>" style="float: left;"><?= TICKET_NOUN ?> Summary</span></a>
				<?php }
			} ?>
			<?php if(config_visible_function($dbc, 'calendar_rook')) { ?>
				<div class="pull-right" style="height: 2.75em; padding: 0.25em; width: 2.75em;"><a href="field_config_calendar.php" class="mobile-block"><img title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me" style="height: 100%;"></a></div>
			<?php } ?>
            <?php if ( get_config($dbc, 'calendar_work_anniversaries')==1 ) { ?>
                    <div class="pull-right">
                        <img src="../img/calendar.png" alt="Staff Work Anniversaries" class="no-toggle" title="Staff Work Anniversaries" style="cursor:pointer; height:36px; padding:5px 5px 0 5px;" onclick="overlayIFrameSlider('work_anniversaries.php', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;" />
                    </div>
            <?php } ?>
			<?php if($_GET['type'] != 'schedule' && count($contact_regions) > 0): ?>
				<div class="subtab-menu pull-right" style="width: auto;">
					<a href="" onclick="toggleMenu(this); return false;"><span class="block-item"><?= $_GET['region'] ?><img src="<?= WEBSITE_URL ?>/img/icons/dropdown-arrow.png" style="height: 1em; margin: 0.25em 0 0.25em 1em;" class="counterclockwise pull-right"></span></a>
					<?php if($_GET['region'] != 'Display All') { ?><a href="?type=<?= $_GET['type'] ?>&mode=<?= $_GET['mode'] ?>&view=<?= $_GET['view'] ?>&region=Display All"><span class="block-item">Display All</span></a><?php } ?>
					<?php foreach($allowed_regions as $region_name) { ?>
						<?php if($_GET['region'] != $region_name) { ?><a href="?type=<?= $_GET['type'] ?>&mode=<?= $_GET['mode'] ?>&view=<?= $_GET['view'] ?>&region=<?= $region_name ?>"><span class="block-item"><?= $region_name ?></span></a><?php } ?>
					<?php } ?>
				</div>
			<?php endif; ?>
			<?php include('calendar_menu_inc.php'); ?>
			<div class="clearfix" style="margin-bottom: -3px;"></div>
			<?php 
			if($_GET['view'] == 'conflicts') {
				include('../Calendar/shift_conflicts.php');
			} else {
				switch($_GET['type']) {
					case 'uni':
						if($_GET['mode'] == 'staff_summary') {
							if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) {
								include('universal_staff_summary.php');
							} else {
								echo 'This Calendar is either not enabled or you do not have access.';
							}
						} else if($_GET['mode'] == 'day_summary') {
							if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) {
								include('universal_day_summary.php');
							} else {
								echo 'This Calendar is either not enabled or you do not have access.';
							}
						} else {
							switch($_GET['view']) {
								case 'weekly':
									if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) {
										include('universal_weekly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'daily':
									if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) {
										include('universal_daily.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'monthly':
									if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) {
										include('universal_monthly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								default:
							}
						}
						break;
					case 'appt':
						switch($_GET['view']) {
							case 'weekly':
								if(in_array('Appointment Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Appointment Calendar')) {
									include('appointment_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'daily':
								if(in_array('Appointment Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Appointment Calendar')) {
									include('appointment_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
								if(in_array('Appointment Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Appointment Calendar')) {
									include('appointment_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							default:
						}
						break;
					case 'staff':
						switch($_GET['view']) {
							case 'weekly':
								if(in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) {
									include('staff_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'daily':
								if(in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) {
									include('staff_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
							default:
								if(in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) {
									include('staff_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
						}
						break;
					case 'schedule':
						if($_GET['mode'] == 'summary') {
							if($_GET['mode'] == 'summary') {
								include('schedule_summary.php');
							}
						} else {
							switch($_GET['view']) {
								case 'weekly':
									if(in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) {
										include('schedule_weekly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'daily':
									if(in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) {
										include('schedule_daily.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'monthly':
								default:
									if(in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) {
										include('schedule_monthly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
							}
						}
						break;
					case 'estimates':
						switch($_GET['view']) {
							case 'weekly':
								if(in_array('Sales Estimates Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Sales Estimates Calendar')) {
									include('estimates_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'daily':
								if(in_array('Sales Estimates Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Sales Estimates Calendar')) {
									include('estimates_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
							default:
								if(in_array('Sales Estimates Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Sales Estimates Calendar')) {
									include('estimates_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
						}
						break;
					case 'shift':
						switch($_GET['view']) {
							case 'daily':
								if(in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) {
									include('shift_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'weekly':
								if(in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) {
									include('shift_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
							default:
								if(in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) {
									include('shift_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
						}
						break;
					case 'event':
						switch($_GET['view']) {
							case 'daily':
								if(in_array('Events Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Events Calendar')) {
									include('events_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'weekly':
								if(in_array('Events Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Events Calendar')) {
									include('events_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
							default:
								if(in_array('Events Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Events Calendar')) {
									include('events_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
						}
						break;
					case 'ticket':
						if($_GET['mode'] == 'staff_summary') {
							if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) {
								include('tickets_staff_summary.php');
							} else {
								echo 'This Calendar is either not enabled or you do not have access.';
							}
						} else if($_GET['mode'] == 'ticket_summary') {
							if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) {
								include('tickets_ticket_summary.php');
							} else {
								echo 'This Calendar is either not enabled or you do not have access.';
							}
						} else {
							switch($_GET['view']) {
								case 'monthly':
									if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) {
										include('tickets_monthly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'weekly':
									if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) {
										include('tickets_weekly.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
								case 'daily':
								default:
									if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) {
										include('tickets_daily.php');
									} else {
										echo 'This Calendar is either not enabled or you do not have access.';
									}
									break;
							}
						}
						break;
					case 'my':
					default:
						switch($_GET['view']) {
							case 'daily':
								if(in_array('My Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'My Calendar')) {
									include('universal_daily.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'weekly':
								if(in_array('My Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'My Calendar')) {
									include('universal_weekly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
							case 'monthly':
							default:
								if(in_array('My Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'My Calendar')) {
									include('universal_monthly.php');
								} else {
									echo 'This Calendar is either not enabled or you do not have access.';
								}
								break;
						}
						break;
				}
			} ?>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>