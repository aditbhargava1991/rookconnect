<?php
/*
Dashboard
*/
include ('../include.php');
error_reporting(0);
checkAuthorised('calendar_rook');

if (isset($_POST['add_tab'])) {
	// General Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'calendar_default' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='calendar_default') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['calendar_default']."' WHERE `name`='calendar_default'");

	// Calendar Types
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'calendar_types' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='calendar_types') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['calendar_types'])."' WHERE `name`='calendar_types'");

    // Calendar Work Anniversaries calendar_work_anniversaries
    $calendar_work_anniversaries = isset($_POST['calendar_work_anniversaries']) ? 1 : 0;
    $count_cwa = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) `num_rows` FROM `general_configuration` WHERE `name`='calendar_work_anniversaries'"));
    if ( $count_cwa['num_rows'] > 0 ) {
        mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$calendar_work_anniversaries' WHERE `name`='calendar_work_anniversaries'");
    } else {
        mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('calendar_work_anniversaries', '$calendar_work_anniversaries')");
    }

    // Calendar Settings
    set_config($dbc, 'calendar_time_behind_cell', $_POST['calendar_time_behind_cell']);
    set_config($dbc, 'calendar_hide_left_time', $_POST['calendar_hide_left_time']);
    set_config($dbc, 'calendar_reset_active', $_POST['calendar_reset_active']);
    set_config($dbc, 'calendar_reset_active_mode', $_POST['calendar_reset_active_mode']);
    set_config($dbc, 'calendar_auto_refresh', $_POST['calendar_auto_refresh']);
    set_config($dbc, 'calendar_online_users', $_POST['calendar_online_users']);

	// My Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['my_start'])[0] == '00' ? '12:'.explode(':',$_POST['my_start'])[1] : $_POST['my_start'])."' WHERE `name`='my_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['my_end'])[0] == '00' ? '12:'.explode(':',$_POST['my_end'])[1] : $_POST['my_end'])."' WHERE `name`='my_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['my_weekly_start']."' WHERE `name`='my_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['my_weekly_days'])."' WHERE `name`='my_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['my_monthly_numdays']."' WHERE `name`='my_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['my_monthly_start']."' WHERE `name`='my_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['my_monthly_days'])."' WHERE `name`='my_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['my_increments']."' WHERE `name`='my_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['my_wait_list'])."' WHERE `name`='my_wait_list'");
	if (!empty($_POST['my_combine_shift_items'])) {
		$my_combine_shift_items = $_POST['my_combine_shift_items'];
	} else {
		$my_combine_shift_items = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_combine_shift_items' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_combine_shift_items') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_combine_shift_items."' WHERE `name`='my_combine_shift_items'");
	if (!empty($_POST['my_use_shift_tickets'])) {
		$my_use_shift_tickets = $_POST['my_use_shift_tickets'];
	} else {
		$my_use_shift_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_use_shift_tickets."' WHERE `name`='my_use_shift_tickets'");
	if (!empty($_POST['my_offline'])) {
		$my_offline = $_POST['my_offline'];
	} else {
		$my_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_offline."' WHERE `name`='my_offline'");
	if (!empty($_POST['my_use_unbooked'])) {
		$my_use_unbooked = $_POST['my_use_unbooked'];
	} else {
		$my_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_use_unbooked."' WHERE `name`='my_use_unbooked'");
	if (!empty($_POST['my_use_shifts'])) {
		$my_use_shifts = $_POST['my_use_shifts'];
	} else {
		$my_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_use_shifts."' WHERE `name`='my_use_shifts'");
	if (!empty($_POST['my_default_view'])) {
		$my_default_view = $_POST['my_default_view'];
	} else {
		$my_default_view = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_default_view' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_default_view') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_default_view."' WHERE `name`='my_default_view'");
	if (!empty($_POST['my_calendar_notes'])) {
		$my_calendar_notes = $_POST['my_calendar_notes'];
	} else {
		$my_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_calendar_notes."' WHERE `name`='my_calendar_notes'");
	if (!empty($_POST['my_reminders'])) {
		$my_reminders = $_POST['my_reminders'];
	} else {
		$my_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_reminders."' WHERE `name`='my_reminders'");
	if (!empty($_POST['my_ticket_summary'])) {
		$my_ticket_summary = $_POST['my_ticket_summary'];
	} else {
		$my_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_ticket_summary."' WHERE `name`='my_ticket_summary'");
	if (!empty($_POST['my_ticket_summary_deleted'])) {
		$my_ticket_summary_deleted = $_POST['my_ticket_summary_deleted'];
	} else {
		$my_ticket_summary_deleted = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_ticket_summary_deleted' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_ticket_summary_deleted') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_ticket_summary_deleted."' WHERE `name`='my_ticket_summary_deleted'");
	if (!empty($_POST['my_availability_indication'])) {
		$my_availability_indication = $_POST['my_availability_indication'];
	} else {
		$my_availability_indication = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'my_availability_indication' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_availability_indication') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$my_availability_indication."' WHERE `name`='my_availability_indication'");

	// Universal Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['uni_start'])[0] == '00' ? '12:'.explode(':',$_POST['uni_start'])[1] : $_POST['uni_start'])."' WHERE `name`='uni_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['uni_end'])[0] == '00' ? '12:'.explode(':',$_POST['uni_end'])[1] : $_POST['uni_end'])."' WHERE `name`='uni_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['uni_weekly_start']."' WHERE `name`='uni_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['uni_weekly_days'])."' WHERE `name`='uni_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['uni_monthly_numdays']."' WHERE `name`='uni_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['uni_monthly_start']."' WHERE `name`='uni_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['uni_monthly_days'])."' WHERE `name`='uni_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['uni_increments']."' WHERE `name`='uni_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['uni_wait_list'])."' WHERE `name`='uni_wait_list'");
	if (!empty($_POST['uni_combine_shift_items'])) {
		$uni_combine_shift_items = $_POST['uni_combine_shift_items'];
	} else {
		$uni_combine_shift_items = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_combine_shift_items' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_combine_shift_items') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_combine_shift_items."' WHERE `name`='uni_combine_shift_items'");
	if (!empty($_POST['uni_use_shift_tickets'])) {
		$uni_use_shift_tickets = $_POST['uni_use_shift_tickets'];
	} else {
		$uni_use_shift_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_use_shift_tickets."' WHERE `name`='uni_use_shift_tickets'");
	if (!empty($_POST['uni_offline'])) {
		$uni_offline = $_POST['uni_offline'];
	} else {
		$uni_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_offline."' WHERE `name`='uni_offline'");
	if (!empty($_POST['uni_use_unbooked'])) {
		$uni_use_unbooked = $_POST['uni_use_unbooked'];
	} else {
		$uni_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_use_unbooked."' WHERE `name`='uni_use_unbooked'");
	if (!empty($_POST['uni_use_shifts'])) {
		$uni_use_shifts = $_POST['uni_use_shifts'];
	} else {
		$uni_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_use_shifts."' WHERE `name`='uni_use_shifts'");
	if (!empty($_POST['uni_teams'])) {
		$uni_teams = $_POST['uni_teams'];
	} else {
		$uni_teams = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_teams' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_teams') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_teams."' WHERE `name`='uni_teams'");
	if (!empty($_POST['uni_default_view'])) {
		$uni_default_view = $_POST['uni_default_view'];
	} else {
		$uni_default_view = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_default_view' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_default_view') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_default_view."' WHERE `name`='uni_default_view'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_client_type' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_client_type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['uni_client_type'])."' WHERE `name`='uni_client_type'");
	if (!empty($_POST['uni_calendar_notes'])) {
		$uni_calendar_notes = $_POST['uni_calendar_notes'];
	} else {
		$uni_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_calendar_notes."' WHERE `name`='uni_calendar_notes'");
	if (!empty($_POST['uni_reminders'])) {
		$uni_reminders = $_POST['uni_reminders'];
	} else {
		$uni_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_reminders."' WHERE `name`='uni_reminders'");
	if (!empty($_POST['uni_ticket_summary'])) {
		$uni_ticket_summary = $_POST['uni_ticket_summary'];
	} else {
		$uni_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_ticket_summary."' WHERE `name`='uni_ticket_summary'");
	if (!empty($_POST['uni_ticket_summary_deleted'])) {
		$uni_ticket_summary_deleted = $_POST['uni_ticket_summary_deleted'];
	} else {
		$uni_ticket_summary_deleted = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_ticket_summary_deleted' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_ticket_summary_deleted') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_ticket_summary_deleted."' WHERE `name`='uni_ticket_summary_deleted'");
	if (!empty($_POST['uni_availability_indication'])) {
		$uni_availability_indication = $_POST['uni_availability_indication'];
	} else {
		$uni_availability_indication = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_availability_indication' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_availability_indication') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_availability_indication."' WHERE `name`='uni_availability_indication'");
	if (!empty($_POST['uni_staff_split_security'])) {
		$uni_staff_split_security = $_POST['uni_staff_split_security'];
	} else {
		$uni_staff_split_security = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_staff_split_security' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_staff_split_security') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_staff_split_security."' WHERE `name`='uni_staff_split_security'");
	if (!empty($_POST['uni_client_staff_freq'])) {
		$uni_client_staff_freq = $_POST['uni_client_staff_freq'];
	} else {
		$uni_client_staff_freq = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_client_staff_freq' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_client_staff_freq') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_client_staff_freq."' WHERE `name`='uni_client_staff_freq'");
	if (!empty($_POST['uni_client_draggable'])) {
		$uni_client_draggable = $_POST['uni_client_draggable'];
	} else {
		$uni_client_draggable = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_client_draggable' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_client_draggable') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_client_draggable."' WHERE `name`='uni_client_draggable'");
	if (!empty($_POST['uni_staff_summary'])) {
		$uni_staff_summary = $_POST['uni_staff_summary'];
	} else {
		$uni_staff_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_staff_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_staff_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_staff_summary."' WHERE `name`='uni_staff_summary'");
	if (!empty($_POST['uni_day_summary_tab'])) {
		$uni_day_summary_tab = $_POST['uni_day_summary_tab'];
	} else {
		$uni_day_summary_tab = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'uni_day_summary_tab' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_day_summary_tab') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$uni_day_summary_tab."' WHERE `name`='uni_day_summary_tab'");

	// Appointment Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['appt_start'])[0] == '00' ? '12:'.explode(':',$_POST['appt_start'])[1] : $_POST['appt_start'])."' WHERE `name`='appt_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['appt_end'])[0] == '00' ? '12:'.explode(':',$_POST['appt_end'])[1] : $_POST['appt_end'])."' WHERE `name`='appt_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['appt_weekly_start']."' WHERE `name`='appt_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['appt_weekly_days'])."' WHERE `name`='appt_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['appt_monthly_numdays']."' WHERE `name`='appt_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['appt_monthly_start']."' WHERE `name`='appt_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['appt_monthly_days'])."' WHERE `name`='appt_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['appt_increments']."' WHERE `name`='appt_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['wait_list']."' WHERE `name`='appt_wait_list'");
	if (!empty($_POST['appt_use_shift_tickets'])) {
		$appt_use_shift_tickets = $_POST['appt_use_shift_tickets'];
	} else {
		$appt_use_shift_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_use_shift_tickets."' WHERE `name`='appt_use_shift_tickets'");
	if (!empty($_POST['appt_offline'])) {
		$appt_offline = $_POST['appt_offline'];
	} else {
		$appt_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_offline."' WHERE `name`='appt_offline'");
	if (!empty($_POST['appt_use_unbooked'])) {
		$appt_use_unbooked = $_POST['appt_use_unbooked'];
	} else {
		$appt_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_use_unbooked."' WHERE `name`='appt_use_unbooked'");
	if (!empty($_POST['appt_use_shifts'])) {
		$appt_use_shifts = $_POST['appt_use_shifts'];
	} else {
		$appt_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_use_shifts."' WHERE `name`='appt_use_shifts'");
	if (!empty($_POST['teams'])) {
		$appt_teams = $_POST['teams'];
	} else {
		$appt_teams = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_teams' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_teams') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_teams."' WHERE `name`='appt_teams'");
	if (!empty($_POST['equip_assign'])) {
		$appt_equip_assign = $_POST['equip_assign'];
	} else {
		$appt_equip_assign = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_equip_assign' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_equip_assign') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_equip_assign."' WHERE `name`='appt_equip_assign'");
	if (!empty($_POST['appt_calendar_notes'])) {
		$appt_calendar_notes = $_POST['appt_calendar_notes'];
	} else {
		$appt_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_calendar_notes."' WHERE `name`='appt_calendar_notes'");
	if (!empty($_POST['appt_reminders'])) {
		$appt_reminders = $_POST['appt_reminders'];
	} else {
		$appt_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_reminders."' WHERE `name`='appt_reminders'");
	if (!empty($_POST['appt_ticket_summary'])) {
		$appt_ticket_summary = $_POST['appt_ticket_summary'];
	} else {
		$appt_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_ticket_summary."' WHERE `name`='appt_ticket_summary'");
	if (!empty($_POST['appt_availability_indication'])) {
		$appt_availability_indication = $_POST['appt_availability_indication'];
	} else {
		$appt_availability_indication = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'appt_availability_indication' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_availability_indication') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_availability_indication."' WHERE `name`='appt_availability_indication'");

	// Staff Schedule Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['staff_schedule_start'])[0] == '00' ? '12:'.explode(':',$_POST['staff_schedule_start'])[1] : $_POST['staff_schedule_start'])."' WHERE `name`='staff_schedule_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['staff_schedule_end'])[0] == '00' ? '12:'.explode(':',$_POST['staff_schedule_end'])[1] : $_POST['staff_schedule_end'])."' WHERE `name`='staff_schedule_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_weekly_start']."' WHERE `name`='staff_schedule_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['staff_schedule_weekly_days'])."' WHERE `name`='staff_schedule_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_monthly_numdays']."' WHERE `name`='staff_schedule_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_monthly_start']."' WHERE `name`='staff_schedule_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['staff_schedule_monthly_days'])."' WHERE `name`='staff_schedule_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_increments']."' WHERE `name`='staff_schedule_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_wait_list']."' WHERE `name`='staff_schedule_wait_list'");
	if (!empty($_POST['staff_schedule_use_shift_tickets'])) {
		$staff_schedule_use_shift_tickets = $_POST['staff_schedule_use_shift_tickets'];
	} else {
		$staff_schedule_use_shift_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_use_shift_tickets."' WHERE `name`='staff_schedule_use_shift_tickets'");
	if (!empty($_POST['staff_schedule_offline'])) {
		$staff_schedule_offline = $_POST['staff_schedule_offline'];
	} else {
		$staff_schedule_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_offline."' WHERE `name`='staff_schedule_offline'");
	if (!empty($_POST['staff_schedule_use_unbooked'])) {
		$staff_schedule_use_unbooked = $_POST['staff_schedule_use_unbooked'];
	} else {
		$staff_schedule_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_use_unbooked."' WHERE `name`='staff_schedule_use_unbooked'");
	if (!empty($_POST['staff_schedule_use_shifts'])) {
		$staff_schedule_use_shifts = $_POST['staff_schedule_use_shifts'];
	} else {
		$staff_schedule_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_use_shifts."' WHERE `name`='staff_schedule_use_shifts'");
	if (!empty($_POST['staff_schedule_teams'])) {
		$staff_schedule_teams = $_POST['staff_schedule_teams'];
	} else {
		$staff_schedule_teams = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_teams' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_teams') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_teams."' WHERE `name`='staff_schedule_teams'");
	if (!empty($_POST['staff_schedule_equip_assign'])) {
		$staff_schedule_equip_assign = $_POST['staff_schedule_equip_assign'];
	} else {
		$staff_schedule_equip_assign = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_equip_assign' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_equip_assign') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_equip_assign."' WHERE `name`='staff_schedule_equip_assign'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_client_type' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_client_type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['staff_schedule_client_type']."' WHERE `name`='staff_schedule_client_type'");
	if (!empty($_POST['staff_schedule_calendar_notes'])) {
		$staff_schedule_calendar_notes = $_POST['staff_schedule_calendar_notes'];
	} else {
		$staff_schedule_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_calendar_notes."' WHERE `name`='staff_schedule_calendar_notes'");
	if (!empty($_POST['staff_schedule_reminders'])) {
		$staff_schedule_reminders = $_POST['staff_schedule_reminders'];
	} else {
		$staff_schedule_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_reminders."' WHERE `name`='staff_schedule_reminders'");
	if (!empty($_POST['staff_schedule_ticket_summary'])) {
		$staff_schedule_ticket_summary = $_POST['staff_schedule_ticket_summary'];
	} else {
		$staff_schedule_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_ticket_summary."' WHERE `name`='staff_schedule_ticket_summary'");
	if (!empty($_POST['staff_schedule_availability_indication'])) {
		$staff_schedule_availability_indication = $_POST['staff_schedule_availability_indication'];
	} else {
		$staff_schedule_availability_indication = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_availability_indication' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_availability_indication') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_availability_indication."' WHERE `name`='staff_schedule_availability_indication'");
	if (!empty($_POST['staff_schedule_use_all_tickets'])) {
		$staff_schedule_use_all_tickets = $_POST['staff_schedule_use_all_tickets'];
	} else {
		$staff_schedule_use_all_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_use_all_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_all_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$staff_schedule_use_all_tickets."' WHERE `name`='staff_schedule_use_all_tickets'");

	// Dispatch Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['scheduling_start'])[0] == '00' ? '12:'.explode(':',$_POST['scheduling_start'])[1] : $_POST['scheduling_start'])."' WHERE `name`='scheduling_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['scheduling_end'])[0] == '00' ? '12:'.explode(':',$_POST['scheduling_end'])[1] : $_POST['scheduling_end'])."' WHERE `name`='scheduling_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_weekly_start']."' WHERE `name`='scheduling_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['scheduling_weekly_days'])."' WHERE `name`='scheduling_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_monthly_numdays']."' WHERE `name`='scheduling_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_monthly_start']."' WHERE `name`='scheduling_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['scheduling_monthly_days'])."' WHERE `name`='scheduling_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_increments']."' WHERE `name`='scheduling_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_wait_list']."' WHERE `name`='scheduling_wait_list'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_calendar_sort_auto' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_calendar_sort_auto') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['scheduling_calendar_sort_auto'])."' WHERE `name`='scheduling_calendar_sort_auto'");
    set_config($dbc, 'scheduling_calendar_est_time', $_POST['scheduling_calendar_est_time']);
    set_config($dbc, 'auto_size_calendar_blocks', $_POST['auto_size_calendar_blocks']);
    set_config($dbc, 'auto_show_time', $_POST['auto_show_time']);
	if (!empty($_POST['scheduling_use_shift_tickets'])) {
		$scheduling_use_shift_tickets = $_POST['scheduling_use_shift_tickets'];
	} else {
		$scheduling_use_shift_tickets = '';
	}
	set_config($dbc, 'equip_multi_assign_staff_disallow', $_POST['equip_multi_assign_staff_disallow']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_use_shift_tickets."' WHERE `name`='scheduling_use_shift_tickets'");
	if (!empty($_POST['scheduling_new_ticket_button'])) {
		$scheduling_new_ticket_button = $_POST['scheduling_new_ticket_button'];
	} else {
		$scheduling_new_ticket_button = '';
	}
	set_config($dbc, 'scheduling_restrict_ticket_types', implode(',',$_POST['scheduling_restrict_ticket_types']));
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_new_ticket_button' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_new_ticket_button') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_new_ticket_button."' WHERE `name`='scheduling_new_ticket_button'");
	if (!empty($_POST['scheduling_offline'])) {
		$scheduling_offline = $_POST['scheduling_offline'];
	} else {
		$scheduling_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_offline."' WHERE `name`='scheduling_offline'");
	if (!empty($_POST['scheduling_use_unbooked'])) {
		$scheduling_use_unbooked = $_POST['scheduling_use_unbooked'];
	} else {
		$scheduling_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_use_unbooked."' WHERE `name`='scheduling_use_unbooked'");
	if (!empty($_POST['scheduling_use_shifts'])) {
		$scheduling_use_shifts = $_POST['scheduling_use_shifts'];
	} else {
		$scheduling_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_use_shifts."' WHERE `name`='scheduling_use_shifts'");
	if (!empty($_POST['scheduling_teams'])) {
		$scheduling_teams = $_POST['scheduling_teams'];
	} else {
		$scheduling_teams = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_teams' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_teams') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_teams."' WHERE `name`='scheduling_teams'");
	if (!empty($_POST['scheduling_equip_assign'])) {
		$scheduling_equip_assign = $_POST['scheduling_equip_assign'];
	} else {
		$scheduling_equip_assign = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_filters' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_filters') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['scheduling_filters'])."' WHERE `name`='scheduling_filters'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_client_type' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_client_type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['scheduling_client_type']."' WHERE `name`='scheduling_client_type'");
	if (!empty($_POST['scheduling_calendar_notes'])) {
		$scheduling_calendar_notes = $_POST['scheduling_calendar_notes'];
	} else {
		$scheduling_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_calendar_notes."' WHERE `name`='scheduling_calendar_notes'");
	if (!empty($_POST['scheduling_ticket_summary'])) {
		$scheduling_ticket_summary = $_POST['scheduling_ticket_summary'];
	} else {
		$scheduling_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_ticket_summary."' WHERE `name`='scheduling_ticket_summary'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_item_filters' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_item_filters') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['scheduling_item_filters'])."' WHERE `name`='scheduling_item_filters'");
	if (!empty($_POST['scheduling_classification_loggedin'])) {
		$scheduling_classification_loggedin = $_POST['scheduling_classification_loggedin'];
	} else {
		$scheduling_classification_loggedin = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_classification_loggedin' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_classification_loggedin') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_classification_loggedin."' WHERE `name`='scheduling_classification_loggedin'");
	if (!empty($_POST['scheduling_multi_class_admin'])) {
		$scheduling_multi_class_admin = $_POST['scheduling_multi_class_admin'];
	} else {
		$scheduling_multi_class_admin = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_multi_class_admin' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_multi_class_admin') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_multi_class_admin."' WHERE `name`='scheduling_multi_class_admin'");
	if (!empty($_POST['scheduling_combine_warehouse'])) {
		$scheduling_combine_warehouse = $_POST['scheduling_combine_warehouse'];
	} else {
		$scheduling_combine_warehouse = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_combine_warehouse' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_combine_warehouse') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_combine_warehouse."' WHERE `name`='scheduling_combine_warehouse'");
	if (!empty($_POST['scheduling_combine_pickup'])) {
		$scheduling_combine_pickup = $_POST['scheduling_combine_pickup'];
	} else {
		$scheduling_combine_pickup = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_combine_pickup' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_combine_pickup') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_combine_pickup."' WHERE `name`='scheduling_combine_pickup'");
	if (!empty($_POST['scheduling_dont_count_warehouse'])) {
		$scheduling_dont_count_warehouse = $_POST['scheduling_dont_count_warehouse'];
	} else {
		$scheduling_dont_count_warehouse = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_dont_count_warehouse' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_dont_count_warehouse') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_dont_count_warehouse."' WHERE `name`='scheduling_dont_count_warehouse'");
	if (!empty($_POST['scheduling_combine_time'])) {
		$scheduling_combine_time = $_POST['scheduling_combine_time'];
	} else {
		$scheduling_combine_time = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_combine_time' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_combine_time') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_combine_time."' WHERE `name`='scheduling_combine_time'");
	if (!empty($_POST['scheduling_summary_view'])) {
		$scheduling_summary_view = $_POST['scheduling_summary_view'];
	} else {
		$scheduling_summary_view = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_summary_view' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_summary_view') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_summary_view."' WHERE `name`='scheduling_summary_view'");
	$scheduling_warning_num_tickets = filter_var($_POST['scheduling_warning_num_tickets'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_warning_num_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_warning_num_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_warning_num_tickets."' WHERE `name`='scheduling_warning_num_tickets'");
	if (!empty($_POST['scheduling_equip_classification'])) {
		$scheduling_equip_classification = $_POST['scheduling_equip_classification'];
	} else {
		$scheduling_equip_classification = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_equip_classification' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_equip_classification') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_equip_classification."' WHERE `name`='scheduling_equip_classification'");
	if (!empty($_POST['scheduling_equip_classification_ticket'])) {
		$scheduling_equip_classification_ticket = $_POST['scheduling_equip_classification_ticket'];
	} else {
		$scheduling_equip_classification_ticket = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_equip_classification_ticket' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_equip_classification_ticket') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_equip_classification_ticket."' WHERE `name`='scheduling_equip_classification_ticket'");
	if (!empty($_POST['scheduling_reset_active'])) {
		$scheduling_reset_active = $_POST['scheduling_reset_active'];
	} else {
		$scheduling_reset_active = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_reset_active' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_reset_active') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_reset_active."' WHERE `name`='scheduling_reset_active'");
	if (!empty($_POST['scheduling_service_followup'])) {
		$scheduling_service_followup = $_POST['scheduling_service_followup'];
	} else {
		$scheduling_service_followup = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_service_followup' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_service_followup') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_service_followup."' WHERE `name`='scheduling_service_followup'");
	if (!empty($_POST['scheduling_service_date'])) {
		$scheduling_service_date = $_POST['scheduling_service_date'];
	} else {
		$scheduling_service_date = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_service_date' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_service_date') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_service_date."' WHERE `name`='scheduling_service_date'");
	if (!empty($_POST['scheduling_passed_service'])) {
		$scheduling_passed_service = $_POST['scheduling_passed_service'];
	} else {
		$scheduling_passed_service = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_passed_service' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_passed_service') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_passed_service."' WHERE `name`='scheduling_passed_service'");
	if (!empty($_POST['scheduling_columns_group_regions'])) {
		$scheduling_columns_group_regions = $_POST['scheduling_columns_group_regions'];
	} else {
		$scheduling_columns_group_regions = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_columns_group_regions' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_columns_group_regions') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_columns_group_regions."' WHERE `name`='scheduling_columns_group_regions'");
	if (!empty($_POST['scheduling_staff_split_security'])) {
		$scheduling_staff_split_security = $_POST['scheduling_staff_split_security'];
	} else {
		$scheduling_staff_split_security = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_staff_split_security' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_staff_split_security') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_staff_split_security."' WHERE `name`='scheduling_staff_split_security'");
	if (!empty($_POST['scheduling_contractor_split_security'])) {
		$scheduling_contractor_split_security = $_POST['scheduling_contractor_split_security'];
	} else {
		$scheduling_contractor_split_security = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_contractor_split_security' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_contractor_split_security') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_contractor_split_security."' WHERE `name`='scheduling_contractor_split_security'");
	$scheduling_customer_roles = filter_var(implode(',', $_POST['scheduling_customer_roles']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_customer_roles' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_customer_roles') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_customer_roles."' WHERE `name`='scheduling_customer_roles'");
	if (!empty($_POST['scheduling_drag_multiple'])) {
		$scheduling_drag_multiple = $_POST['scheduling_drag_multiple'];
	} else {
		$scheduling_drag_multiple = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_drag_multiple' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_drag_multiple') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_drag_multiple."' WHERE `name`='scheduling_drag_multiple'");
	$scheduling_customer_roles = filter_var(implode(',', $_POST['scheduling_customer_roles']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_customer_roles' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_customer_roles') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_customer_roles."' WHERE `name`='scheduling_customer_roles'");
	if (!empty($_POST['scheduling_export_time_table'])) {
		$scheduling_export_time_table = $_POST['scheduling_export_time_table'];
	} else {
		$scheduling_export_time_table = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'scheduling_export_time_table' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_export_time_table') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$scheduling_export_time_table."' WHERE `name`='scheduling_export_time_table'");

    if(!empty($_FILES['scheduling_time_table_logo']['name'])) {
        $scheduling_time_table_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['scheduling_time_table_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$scheduling_time_table_logo)) {
            $scheduling_time_table_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['scheduling_time_table_logo']['tmp_name'], 'download/'.$scheduling_time_table_logo);
        set_config($dbc, 'scheduling_time_table_logo', $scheduling_time_table_logo);
    }
    $scheduling_time_table_logo_align = filter_var($_POST['scheduling_time_table_logo_align'], FILTER_SANITIZE_STRING);
    set_config($dbc, 'scheduling_time_table_logo_align', $scheduling_time_table_logo_align);

	// Sales Estimates Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['estimates_start'])[0] == '00' ? '12:'.explode(':',$_POST['estimates_start'])[1] : $_POST['estimates_start'])."' WHERE `name`='estimates_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['estimates_end'])[0] == '00' ? '12:'.explode(':',$_POST['estimates_end'])[1] : $_POST['estimates_end'])."' WHERE `name`='estimates_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['estimates_weekly_start']."' WHERE `name`='estimates_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['estimates_weekly_days'])."' WHERE `name`='estimates_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['estimates_monthly_numdays']."' WHERE `name`='estimates_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['estimates_monthly_start']."' WHERE `name`='estimates_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['estimates_monthly_days'])."' WHERE `name`='estimates_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['estimates_increments']."' WHERE `name`='estimates_increments'");
	if (!empty($_POST['estimates_calendar_notes'])) {
		$estimates_calendar_notes = $_POST['estimates_calendar_notes'];
	} else {
		$estimates_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$estimates_calendar_notes."' WHERE `name`='estimates_calendar_notes'");
	if (!empty($_POST['estimates_reminders'])) {
		$estimates_reminders = $_POST['estimates_reminders'];
	} else {
		$estimates_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimates_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$estimates_reminders."' WHERE `name`='estimates_reminders'");

	// Ticket Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['ticket_start'])[0] == '00' ? '12:'.explode(':',$_POST['ticket_start'])[1] : $_POST['ticket_start'])."' WHERE `name`='ticket_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['ticket_end'])[0] == '00' ? '12:'.explode(':',$_POST['ticket_end'])[1] : $_POST['ticket_end'])."' WHERE `name`='ticket_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['ticket_weekly_start']."' WHERE `name`='ticket_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['ticket_weekly_days'])."' WHERE `name`='ticket_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['ticket_monthly_numdays']."' WHERE `name`='ticket_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['ticket_monthly_start']."' WHERE `name`='ticket_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['ticket_monthly_days'])."' WHERE `name`='ticket_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['ticket_increments']."' WHERE `name`='ticket_increments'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_wait_list' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['ticket_wait_list']."' WHERE `name`='ticket_wait_list'");
	if (!empty($_POST['ticket_use_shift_tickets'])) {
		$ticket_use_shift_tickets = $_POST['ticket_use_shift_tickets'];
	} else {
		$ticket_use_shift_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_use_shift_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_use_shift_tickets."' WHERE `name`='ticket_use_shift_tickets'");
	if (!empty($_POST['ticket_use_unbooked'])) {
		$ticket_use_unbooked = $_POST['ticket_use_unbooked'];
	} else {
		$ticket_use_unbooked = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_use_unbooked' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_use_unbooked."' WHERE `name`='ticket_use_unbooked'");
	if (!empty($_POST['ticket_offline'])) {
		$ticket_offline = $_POST['ticket_offline'];
	} else {
		$ticket_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_offline."' WHERE `name`='ticket_offline'");
	if (!empty($_POST['ticket_use_shifts'])) {
		$ticket_use_shifts = $_POST['ticket_use_shifts'];
	} else {
		$ticket_use_shifts = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_use_shifts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_use_shifts."' WHERE `name`='ticket_use_shifts'");
	if (!empty($_POST['ticket_teams'])) {
		$ticket_teams = $_POST['ticket_teams'];
	} else {
		$ticket_teams = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_teams' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_teams') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_teams."' WHERE `name`='ticket_teams'");
	if (!empty($_POST['ticket_equip_assign'])) {
		$ticket_equip_assign = $_POST['ticket_equip_assign'];
	} else {
		$ticket_equip_assign = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_equip_assign' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_equip_assign') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_equip_assign."' WHERE `name`='ticket_equip_assign'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_client_type' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_client_type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['ticket_client_type'])."' WHERE `name`='ticket_client_type'");
	if (!empty($_POST['ticket_calendar_notes'])) {
		$ticket_calendar_notes = $_POST['ticket_calendar_notes'];
	} else {
		$ticket_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_calendar_notes."' WHERE `name`='ticket_calendar_notes'");
	if (!empty($_POST['ticket_reminders'])) {
		$ticket_reminders = $_POST['ticket_reminders'];
	} else {
		$ticket_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_reminders."' WHERE `name`='ticket_reminders'");
	if (!empty($_POST['ticket_ticket_summary'])) {
		$ticket_ticket_summary = $_POST['ticket_ticket_summary'];
	} else {
		$ticket_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_ticket_summary."' WHERE `name`='ticket_ticket_summary'");
	if (!empty($_POST['ticket_ticket_summary_deleted'])) {
		$ticket_ticket_summary_deleted = $_POST['ticket_ticket_summary_deleted'];
	} else {
		$ticket_ticket_summary_deleted = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_ticket_summary_deleted' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_ticket_summary_deleted') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_ticket_summary_deleted."' WHERE `name`='ticket_ticket_summary_deleted'");
	if (!empty($_POST['ticket_availability_indication'])) {
		$ticket_availability_indication = $_POST['ticket_availability_indication'];
	} else {
		$ticket_availability_indication = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_availability_indication' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_availability_indication') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_availability_indication."' WHERE `name`='ticket_availability_indication'");
	if (!empty($_POST['ticket_use_all_tickets'])) {
		$ticket_use_all_tickets = $_POST['ticket_use_all_tickets'];
	} else {
		$ticket_use_all_tickets = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_use_all_tickets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_all_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_use_all_tickets."' WHERE `name`='ticket_use_all_tickets'");
	set_config($dbc, 'ticket_sidebar_filters', implode(',',$_POST['ticket_sidebar_filters']));
	set_config($dbc, 'ticket_restrict_ticket_types', implode(',',$_POST['ticket_restrict_ticket_types']));
	set_config($dbc, 'ticket_use_equipment', $_POST['ticket_use_equipment']);
	if (!empty($_POST['ticket_staff_split_security'])) {
		$ticket_staff_split_security = $_POST['ticket_staff_split_security'];
	} else {
		$ticket_staff_split_security = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_staff_split_security' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_staff_split_security') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_staff_split_security."' WHERE `name`='ticket_staff_split_security'");
	if (!empty($_POST['ticket_client_staff_freq'])) {
		$ticket_client_staff_freq = $_POST['ticket_client_staff_freq'];
	} else {
		$ticket_client_staff_freq = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_client_staff_freq' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_client_staff_freq') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_client_staff_freq."' WHERE `name`='ticket_client_staff_freq'");
	if (!empty($_POST['ticket_client_draggable'])) {
		$ticket_client_draggable = $_POST['ticket_client_draggable'];
	} else {
		$ticket_client_draggable = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_client_draggable' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_client_draggable') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_client_draggable."' WHERE `name`='ticket_client_draggable'");
	if (!empty($_POST['ticket_staff_summary'])) {
		$ticket_staff_summary = $_POST['ticket_staff_summary'];
	} else {
		$ticket_staff_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_staff_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_staff_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_staff_summary."' WHERE `name`='ticket_staff_summary'");
	if (!empty($_POST['ticket_ticket_summary_tab'])) {
		$ticket_ticket_summary_tab = $_POST['ticket_ticket_summary_tab'];
	} else {
		$ticket_ticket_summary_tab = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_ticket_summary_tab' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_ticket_summary_tab') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_ticket_summary_tab."' WHERE `name`='ticket_ticket_summary_tab'");
	if (!empty($_POST['ticket_ticket_summary_tab_deleted'])) {
		$ticket_ticket_summary_tab_deleted = $_POST['ticket_ticket_summary_tab_deleted'];
	} else {
		$ticket_ticket_summary_tab_deleted = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_ticket_summary_tab_deleted' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_ticket_summary_tab_deleted') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_ticket_summary_tab_deleted."' WHERE `name`='ticket_ticket_summary_tab_deleted'");
	if (!empty($_POST['ticket_client_tab'])) {
		$ticket_client_tab = $_POST['ticket_client_tab'];
	} else {
		$ticket_client_tab = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'ticket_client_tab' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_client_tab') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_client_tab."' WHERE `name`='ticket_client_tab'");

	// Shift Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['shift_start'])[0] == '00' ? '12:'.explode(':',$_POST['shift_start'])[1] : $_POST['shift_start'])."' WHERE `name`='shift_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['shift_end'])[0] == '00' ? '12:'.explode(':',$_POST['shift_end'])[1] : $_POST['shift_end'])."' WHERE `name`='shift_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['shift_weekly_start']."' WHERE `name`='shift_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['shift_weekly_days'])."' WHERE `name`='shift_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['shift_monthly_numdays']."' WHERE `name`='shift_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['shift_monthly_start']."' WHERE `name`='shift_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['shift_monthly_days'])."' WHERE `name`='shift_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['shift_increments']."' WHERE `name`='shift_increments'");
	if (!empty($_POST['shift_calendar_notes'])) {
		$shift_calendar_notes = $_POST['shift_calendar_notes'];
	} else {
		$shift_calendar_notes = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_calendar_notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_calendar_notes."' WHERE `name`='shift_calendar_notes'");
	if (!empty($_POST['shift_reminders'])) {
		$shift_reminders = $_POST['shift_reminders'];
	} else {
		$shift_reminders = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_reminders' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_reminders') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_reminders."' WHERE `name`='shift_reminders'");
	if (!empty($_POST['shift_offline'])) {
		$shift_offline = $_POST['shift_offline'];
	} else {
		$shift_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_offline."' WHERE `name`='shift_offline'");
	if (!empty($_POST['shift_select_all_staff'])) {
		$shift_select_all_staff = $_POST['shift_select_all_staff'];
	} else {
		$shift_select_all_staff = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_select_all_staff' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_select_all_staff') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_select_all_staff."' WHERE `name`='shift_select_all_staff'");
	if (!empty($_POST['shift_selected_staff_icons'])) {
		$shift_selected_staff_icons = $_POST['shift_selected_staff_icons'];
	} else {
		$shift_selected_staff_icons = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_selected_staff_icons' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_selected_staff_icons') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_selected_staff_icons."' WHERE `name`='shift_selected_staff_icons'");
	if (!empty($_POST['shift_selected_client_icons'])) {
		$shift_selected_client_icons = $_POST['shift_selected_client_icons'];
	} else {
		$shift_selected_client_icons = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'shift_selected_client_icons' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_selected_client_icons') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$shift_selected_client_icons."' WHERE `name`='shift_selected_client_icons'");

	// Events Calendar Settings
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_day_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['event_start'])[0] == '00' ? '12:'.explode(':',$_POST['event_start'])[1] : $_POST['event_start'])."' WHERE `name`='event_day_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_day_end' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".(explode(':',$_POST['event_end'])[0] == '00' ? '12:'.explode(':',$_POST['event_end'])[1] : $_POST['event_end'])."' WHERE `name`='event_day_end'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_weekly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['event_weekly_start']."' WHERE `name`='event_weekly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_weekly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['event_weekly_days'])."' WHERE `name`='event_weekly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_monthly_numdays' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_monthly_numdays') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['event_monthly_numdays']."' WHERE `name`='event_monthly_numdays'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_monthly_start' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['event_monthly_start']."' WHERE `name`='event_monthly_start'");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_monthly_days' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode(',',$_POST['event_monthly_days'])."' WHERE `name`='event_monthly_days'");

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_increments' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['event_increments']."' WHERE `name`='event_increments'");
	if (!empty($_POST['event_calendar_notes'])) {
		$event_calendar_notes = $_POST['event_calendar_notes'];
	} else {
		$event_calendar_notes = '';
	}
	if (!empty($_POST['event_offline'])) {
		$event_offline = $_POST['event_offline'];
	} else {
		$event_offline = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_offline' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_offline') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$event_offline."' WHERE `name`='event_offline'");
	if (!empty($_POST['event_ticket_summary'])) {
		$event_ticket_summary = $_POST['event_ticket_summary'];
	} else {
		$event_ticket_summary = '';
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'event_ticket_summary' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_ticket_summary') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$event_ticket_summary."' WHERE `name`='event_ticket_summary'");

	// Staff Settings
	foreach($_POST['contactid'] as $row => $contactid) {
		$calendar_color = $_POST['calendar_color'][$row];
		$default_calendar = $_POST['default_calendar'][$row];
		$calendar_enabled = (in_array($contactid, $_POST['calendar_enabled']) ? 1 : 0);

		mysqli_query($dbc, "UPDATE `contacts` SET `calendar_color`='$calendar_color', `calendar_enabled`='$calendar_enabled' WHERE `contactid`='$contactid'");
		mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='$contactid') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `user_settings` SET `calendar_view`='$default_calendar' WHERE `contactid`='$contactid'");
	}

	// Holiday Settings
	$ids = trim(implode(',',$_POST['holidays_id']),',');
	//$delete_sql = mysqli_query($dbc, "DELETE FROM `field_config_holidays` WHERE holiday_id NOT IN ($ids)");
	foreach($_POST['holiday_id'] as $key => $id) {
		if($id > 0 || ($_POST['holiday_date'][$key] != '' && $_POST['holiday_date'][$key] != '0000-00-00')) {
			$name = filter_var($_POST['holiday_name'][$key],FILTER_SANITIZE_STRING);
			$date = $_POST['holiday_date'][$key];
			$paid = $_POST['holiday_paid'][$key];
			$archived = $_POST['holiday_archived'][$key];
			if($id > 0) {
				$holiday_sql = "UPDATE `holidays` SET `name`='$name', `date`='$date', `paid`='$paid', `deleted`='$archived' WHERE `holidays_id`='$id'";
			} else {
				$holiday_sql = "INSERT INTO `holidays` (`name`, `date`, `paid`) VALUES ('$name', '$date', '$paid')";
			}
			$holiday_result = mysqli_query($dbc, $holiday_sql);
		}
	}

    echo '<script type="text/javascript"> window.location.replace("field_config_calendar.php"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name$="wait_list"]').change(function() {
		if($(this).val() == 'ticket') {
			$(this).closest('.panel-body').find('.shift_tickets_block').show();
		} else {
			$(this).closest('.panel-body').find('.shift_tickets_block').hide();
		}
	});
	$('[name$="wait_list[]"]').change(function() {
		$(this).closest('.panel-body').find('.shift_tickets_block').hide();
		if($(this).closest('.form-group').find('input[value="ticket"]').is(':checked')) {
			$(this).closest('.panel-body').find('.shift_tickets_block').show();
		}
	});
});
function showDefaultView(chk) {
	if($(chk).is(':checked')) {
		$(chk).closest('.panel-body').find('.shifts_default_view').show();
	} else {
		$(chk).closest('.panel-body').find('.shifts_default_view').hide();
	}
}
function deleteLogo(logo) {
    if(confirm('Are you sure you want to delete this logo?')) {
        $.ajax({
            url: '../Calendar/calendar_ajax_all.php?fill=delete_logo',
            type: 'POST',
            data: { logo: logo },
            success: function(response) {
            	$(logo).closest('.form-group').find('.logo_url').html('');
            }
        });
    }
}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container calendar_config_div">
	<div class="row">
		<h1>Calendar</h1>
		<a href="calendars.php" class="btn brand-btn gap-top double-gap-bottom">Back to Dashboard</a>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
			$active_general = '';
			$active_appt = '';
			$active_tickets = '';
			$active_shifts = '';
			$active_teams = '';
			$active_equip_assign = '';
			$active_workorder = '';
			$active_unbooked = '';
			$active_security = '';

			if($_GET['type'] == 'general' || empty($_GET['type'])) {
				$active_general = 'active_tab';
			}
			if($_GET['type'] == 'appointments') {
				$active_appt = 'active_tab';
			}
			if($_GET['type'] == 'tickets') {
				$active_tickets = 'active_tab';
			}
			if($_GET['type'] == 'shifts') {
				$active_shifts = 'active_tab';
			}
			if($_GET['type'] == 'teams') {
				$active_teams = 'active_tab';
			}
			if($_GET['type'] == 'equip_assign') {
				$active_equip_assign = 'active_tab';
			}
			if($_GET['type'] == 'workorder') {
				$active_workorder = 'active_tab';
			}
			if($_GET['type'] == 'unbooked') {
				$active_unbooked = 'active_tab';
			}
			if($_GET['type'] == 'security') {
				$active_security = 'active_tab';
			}
		?>

			<div class="tab-container"><?php
				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your general Calendar settings.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=general'><button type='button' class='btn brand-btn mobile-block ".$active_general."' >General</button></a></div>";

				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Appointments.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=appointments'><button type='button' class='btn brand-btn mobile-block ".$active_appt."' >Appointments</button></a></div>";

				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for ".TICKET_TILE.".'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=tickets'><button type='button' class='btn brand-btn mobile-block ".$active_tickets."' >".TICKET_TILE."</button></a></div>";

				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Staff Shifts.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=shifts'><button type='button' class='btn brand-btn mobile-block ".$active_shifts."' >Shifts</button></a></div>";

				$teams_enabled = get_config($dbc, 'appt_teams').get_config($dbc, 'scheduling_teams').get_config($dbc,'staff_schedule_teams').get_config($dbc,'ticket_teams');
				if ($teams_enabled != '') {
					echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Team View.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=teams'><button type='button' class='btn brand-btn mobile-block ".$active_teams."' >Teams</button></a></div>";
				}

				$equip_assign_enabled = get_config($dbc, 'appt_equip_assign').get_config($dbc, 'scheduling_equip_assign').get_config($dbc, 'staff_schedule_equip_assign').get_config($dbc, 'ticket_equip_assign');
				if ($equip_assign_enabled != '') {
					echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Equipment.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=equip_assign'><button type='button' class='btn brand-btn mobile-block ".$active_equip_assign."' >Equipment</button></a></div>";
				}

				$appt_wait_list = get_config($dbc, 'appt_wait_list').get_config($dbc, 'scheduling_wait_list').get_config($dbc, 'staff_schedule_wait_list').get_config($dbc, 'ticket_wait_list');
				if ($appt_wait_list != '') {
					echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Work Order view.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=workorder'><button type='button' class='btn brand-btn mobile-block ".$active_workorder."' >Work Orders</button></a></div>";
				}

				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your Calendar settings for Unbooked Lists.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=unbooked'><button type='button' class='btn brand-btn mobile-block ".$active_unbooked."' >Unbooked List</button></a></div>";

				echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='These are your security settings for your Calendars.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></span><a href='field_config_calendar.php?type=security'><button type='button' class='btn brand-btn mobile-block ".$active_security."' >Security</button></a></div>";
				?>
			</div>

			<div class="clearfix"></div>

			<?php if($_GET['type'] == 'general' || empty($_GET['type'])) { ?>
			<h3>General</h3>
			<div class="panel-group" id="accordion2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_calendar" >
								General Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_calendar" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Calendar Types:</label>
								<div class="col-sm-8">
									<?php $calendar_types = explode(',',get_config($dbc, 'calendar_types')); ?>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('My Calendar',$calendar_types) ? 'checked' : '' ?> value="My Calendar"> My Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Universal Calendar',$calendar_types) ? 'checked' : '' ?> value="Universal Calendar"> Universal Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Appointment Calendar',$calendar_types) ? 'checked' : '' ?> value="Appointment Calendar"> Appointment Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Staff Schedule Calendar',$calendar_types) ? 'checked' : '' ?> value="Staff Schedule Calendar"> Staff Schedule Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Dispatch Calendar',$calendar_types) ? 'checked' : '' ?> value="Dispatch Calendar"> Dispatch Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Sales Estimates Calendar',$calendar_types) ? 'checked' : '' ?> value="Sales Estimates Calendar"> Sales <?= ESTIMATE_TILE ?> Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Ticket Calendar',$calendar_types) ? 'checked' : '' ?> value="Ticket Calendar"> <?= TICKET_NOUN.' Calendar' ?></label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Shift Calendar',$calendar_types) ? 'checked' : '' ?> value="Shift Calendar"> Shift Calendar</label>
									<label class="form-checkbox"><input type="checkbox" name="calendar_types[]" <?= in_array('Events Calendar',$calendar_types) ? 'checked' : '' ?> value="Events Calendar"> Events Calendar</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Software Default Calendar View:</label>
								<div class="col-sm-8">
									<?php $calendar_default = get_config($dbc, 'calendar_default'); ?>
									<select name="calendar_default" class="chosen-select-deselect"><option></option>
										<?php if(in_array('My Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'my_day' ? 'selected' : '' ?> value="my_day">My Calendar: Day</option>
											<option <?= $calendar_default == 'my_wk' ? 'selected' : '' ?> value="my_wk">My Calendar: Week</option>
											<option <?= $calendar_default == 'my_mon' ? 'selected' : '' ?> value="my_wk">My Calendar: Month</option>
										<?php } ?>
										<?php if(in_array('Universal Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'uni_day' ? 'selected' : '' ?> value="uni_day">Universal: Day</option>
											<option <?= $calendar_default == 'uni_wk' ? 'selected' : '' ?> value="uni_wk">Universal: Week</option>
											<option <?= $calendar_default == 'uni_mon' ? 'selected' : '' ?> value="uni_wk">Universal: Month</option>
										<?php } ?>
										<?php if(in_array('Appointment Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'appt_day' ? 'selected' : '' ?> value="appt_day">Appointment: Day</option>
											<option <?= $calendar_default == 'appt_wk' ? 'selected' : '' ?> value="appt_wk">Appointment: Week</option>
											<option <?= $calendar_default == 'appt_mon' ? 'selected' : '' ?> value="appt_wk">Appointment: Month</option>
										<?php } ?>
										<?php if(in_array('Staff Schedule Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'staff_day' ? 'selected' : '' ?> value="staff_day">Staff Schedule: Day</option>
											<option <?= $calendar_default == 'staff_wk' ? 'selected' : '' ?> value="staff_wk">Staff Schedule: Week</option>
											<option <?= $calendar_default == 'staff_mon' ? 'selected' : '' ?> value="staff_wk">Staff Schedule: Month</option>
										<?php } ?>
										<?php if(in_array('Dispatch Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'sched_day' ? 'selected' : '' ?> value="sched_day">Dispatch: Day</option>
											<option <?= $calendar_default == 'sched_wk' ? 'selected' : '' ?> value="sched_wk">Dispatch: Week</option>
											<option <?= $calendar_default == 'sched_mon' ? 'selected' : '' ?> value="sched_wk">Dispatch: Month</option>
										<?php } ?>
										<?php if(in_array('Ticket Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'ticket_day' ? 'selected' : '' ?> value="ticket_day"><?= TICKET_NOUN ?>: Day</option>
											<option <?= $calendar_default == 'ticket_wk' ? 'selected' : '' ?> value="ticket_wk"><?= TICKET_NOUN ?>: Week</option>
											<option <?= $calendar_default == 'ticket_mon' ? 'selected' : '' ?> value="ticket_mon"><?= TICKET_NOUN ?>: Month</option>
											<!-- <option <?= $calendar_default == 'ticket_30' ? 'selected' : '' ?> value="ticket_30"><?= TICKET_NOUN ?>: 30 Days</option> -->
										<?php } ?>
										<?php if(in_array('Shift Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'shift_day' ? 'selected' : '' ?> value="shift_day">Shift: Day</option>
											<option <?= $calendar_default == 'shift_wk' ? 'selected' : '' ?> value="shift_wk">Shift: Week</option>
											<option <?= $calendar_default == 'shift_mon' ? 'selected' : '' ?> value="shift_mon">Shift: Month</option>
										<?php } ?>
										<?php if(in_array('Events Calendar',$calendar_types)) { ?>
											<option <?= $calendar_default == 'event_day' ? 'selected' : '' ?> value="event_day">Events: Day</option>
											<option <?= $calendar_default == 'event_wk' ? 'selected' : '' ?> value="event_wk">Events: Week</option>
											<option <?= $calendar_default == 'event_mon' ? 'selected' : '' ?> value="event_mon">Events: Month</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Staff Work Anniversaries:</label>
								<div class="col-sm-8"><?php
                                    $calendar_work_anniversaries = get_config($dbc, 'calendar_work_anniversaries'); ?>
                                    <label class="form-checkbox"><input type="checkbox" name="calendar_work_anniversaries" <?= $calendar_work_anniversaries==1 ? 'checked' : ''; ?> /></label>
                                </div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-4 control-label">Display Time Behind Cell In Grey Italics:</label>
								<div class="col-sm-8"><?php
                                    $calendar_time_behind_cell = get_config($dbc, 'calendar_time_behind_cell'); ?>
                                    <label class="form-checkbox"><input type="checkbox" name="calendar_time_behind_cell" <?= $calendar_time_behind_cell==1 ? 'checked' : ''; ?> value="1" /></label>
                                </div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-4 control-label">Hide Left Side Time Column:</label>
								<div class="col-sm-8"><?php
                                    $calendar_hide_left_time = get_config($dbc, 'calendar_hide_left_time'); ?>
                                    <label class="form-checkbox"><input type="checkbox" name="calendar_hide_left_time" <?= $calendar_hide_left_time==1 ? 'checked' : ''; ?> value="1" /></label>
                                </div>
                            </div>
                            <div class="form-group">
                            	<label class="col-sm-4 control-label">Reset Active Blocks:</label>
                            	<div class="col-sm-8"><?php
                            		$calendar_reset_active = get_config($dbc, 'calendar_reset_active'); ?>
                            		<label class="form-checkbox"><input type="radio" name="calendar_reset_active" <?= empty($calendar_reset_active) ? 'checked' : '' ?> value="" onchange="if($(this).is(':checked')) { $('.reset_mode').hide(); } else { $('.reset_mode').show(); }"> No Reset</label>
                            		<label class="form-checkbox"><input type="radio" name="calendar_reset_active" <?= $calendar_reset_active==1 ? 'checked' : '' ?> value="1" onchange="if($(this).is(':checked')) { $('.reset_mode').show(); } else { $('.reset_mode').hide(); }"> On Calendar Reload</label>
                            		<label class="form-checkbox"><input type="radio" name="calendar_reset_active" <?= $calendar_reset_active==2 ? 'checked' : '' ?> value="2" onchange="if($(this).is(':checked')) { $('.reset_mode').show(); } else { $('.reset_mode').hide(); }"> Once Per Day</label>
                            	</div>
                            </div>
                            <div class="form-group reset_mode" <?= $calendar_reset_active != 1 && $calendar_reset_active != 2 ? 'style="display:none;"' : '' ?>>
                            	<label class="col-sm-4 control-label">Active Blocks Reset To:</label>
                            	<div class="col-sm-8"><?php
                            		$calendar_reset_active_mode = get_config($dbc, 'calendar_reset_active_mode'); ?>
                            		<label class="form-checkbox"><input type="radio" name="calendar_reset_active_mode" <?= $calendar_reset_active_mode=='session_user' ? 'checked' : '' ?> value="session_user">Logged In User Only</label>
                            		<?php 
							        $equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
							        $equipment_categories = array_filter(explode(',', $equipment_category));
							        if(empty($equipment_categories) || count($equipment_categories) > 1) {
							            $equipment_category = 'Equipment';
							        } ?>
                            		<label class="form-checkbox"><input type="radio" name="calendar_reset_active_mode" <?= $calendar_reset_active_mode=='session_user active_equip' ? 'checked' : '' ?> value="session_user active_equip">Logged In User/Assigned <?= $equipment_category ?></label>
                            	</div>
                            </div>
                            <div class="form-group">
                            	<label class="col-sm-4 control-label">Auto Refresh Calendar After Inactivity Time:</label>
                            	<div class="col-sm-8"><?php
                            		$calendar_auto_refresh = get_config($dbc, 'calendar_auto_refresh'); ?>
                            		<input type="text" name="calendar_auto_refresh" class="timepicker form-control" value="<?= $calendar_auto_refresh ?>">
                            	</div>
                            </div>
                            <div class="form-group">
                            	<label class="col-sm-4 control-label">Display Online Users:</label>
								<div class="col-sm-8"><?php
                                    $calendar_online_users = get_config($dbc, 'calendar_online_users'); ?>
                                    <label class="form-checkbox"><input type="checkbox" name="calendar_online_users" <?= $calendar_online_users==1 ? 'checked' : ''; ?> value="1" /></label>
                            	</div>
                            </div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('My Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_my" >
								My Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_my" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $my_increments = get_config($dbc, 'my_increments'); ?>
									<select name="my_increments" class="chosen-select-deselect"><option></option>
										<option <?= $my_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $my_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $my_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $my_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $my_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $my_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $my_day_start = get_config($dbc, 'my_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="my_start" value="<?= $my_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $my_day_end = get_config($dbc, 'my_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="my_end" value="<?= $my_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $my_weekly_start = get_config($dbc, 'my_weekly_start'); ?>
									<select name="my_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $my_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $my_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $my_weekly_days = explode(',',get_config($dbc, 'my_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Sunday',$my_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Monday',$my_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Tuesday',$my_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Wednesday',$my_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Thursday',$my_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Friday',$my_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_weekly_days[]" <?= in_array('Saturday',$my_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $my_monthly_numdays = get_config($dbc, 'my_monthly_numdays'); ?>
									<select name="my_monthly_numdays" class="chosen-select-deselect">
										<option <?= $my_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $my_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $my_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $my_monthly_start = get_config($dbc, 'my_monthly_start'); ?>
									<select name="my_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $my_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $my_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $my_monthly_days = explode(',',get_config($dbc, 'my_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Sunday',$my_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Monday',$my_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Tuesday',$my_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Wednesday',$my_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Thursday',$my_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Friday',$my_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="my_monthly_days[]" <?= in_array('Saturday',$my_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $my_wait_list = explode(',',get_config($dbc, 'my_wait_list')); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_wait_list[]" <?= in_array('ticket', $my_wait_list) ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="checkbox" name="my_wait_list[]" <?= in_array('appt', $my_wait_list) ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Blocks Month View - Combine Shifts with Items:</label>
								<div class="col-sm-8">
									<?php $my_combine_shift_items = get_config($dbc, 'my_combine_shift_items'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_combine_shift_items" <?= $my_combine_shift_items == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= (in_array('ticket', $my_wait_list) ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">My Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $my_use_shift_tickets = get_config($dbc, 'my_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_use_shift_tickets" <?= $my_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $my_use_unbooked = get_config($dbc, 'my_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_use_unbooked" <?= $my_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $my_offline = get_config($dbc, 'my_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_offline" <?= $my_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $my_use_shifts = get_config($dbc, 'my_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_use_shifts" <?= $my_use_shifts != '' ? 'checked' : '' ?> value="1" onchange="showDefaultView(this);"></label>
								</div>
							</div>
							<div class="form-group shifts_default_view" <?= $my_use_shifts != '' ? '' : 'style="display:none;"' ?>>
								<label class="col-sm-4 control-label">My Calendar Default View:</label>
								<div class="col-sm-8">
									<?php $my_default_view = get_config($dbc, 'my_default_view'); ?>
									<label class="form-checkbox"><input type="radio" name="my_default_view" value="" <?= empty($my_default_view) ? 'checked' : '' ?>> Default</label>
									<label class="form-checkbox"><input type="radio" name="my_default_view" value="shifts" <?= $my_default_view == 'shifts' ? 'checked' : '' ?>> Shifts</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $my_calendar_notes = get_config($dbc, 'my_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_calendar_notes" <?= $my_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $my_reminders = get_config($dbc, 'my_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_reminders" <?= $my_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $my_ticket_summary = get_config($dbc, 'my_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_ticket_summary" <?= $my_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar <?= TICKET_NOUN ?> Summary - Display Deleted:</label>
								<div class="col-sm-8">
									<?php $my_ticket_summary_deleted = get_config($dbc, 'my_ticket_summary_deleted'); ?>
									<label class="form-checkbox"><input type="checkbox" name="my_ticket_summary_deleted" <?= $my_ticket_summary_deleted != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">My Calendar No Shift Indicates:</label>
								<div class="col-sm-8">
									<?php $my_availability_indication = get_config($dbc, 'my_availability_indication'); ?>
									<label class="form-checkbox"><input type="radio" name="my_availability_indication" <?= empty($my_availability_indication) ? 'checked' : '' ?> value=""> All Day Availability</label>
									<label class="form-checkbox"><input type="radio" name="my_availability_indication" <?= $my_availability_indication == 1 ? 'checked' : '' ?> value="1"> No Availability</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Universal Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_uni" >
								Universal Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_uni" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $uni_increments = get_config($dbc, 'uni_increments'); ?>
									<select name="uni_increments" class="chosen-select-deselect"><option></option>
										<option <?= $uni_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $uni_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $uni_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $uni_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $uni_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $uni_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $uni_day_start = get_config($dbc, 'uni_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="uni_start" value="<?= $uni_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $uni_day_end = get_config($dbc, 'uni_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="uni_end" value="<?= $uni_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $uni_weekly_start = get_config($dbc, 'uni_weekly_start'); ?>
									<select name="uni_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $uni_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $uni_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $uni_weekly_days = explode(',',get_config($dbc, 'uni_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Sunday',$uni_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Monday',$uni_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Tuesday',$uni_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Wednesday',$uni_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Thursday',$uni_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Friday',$uni_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_weekly_days[]" <?= in_array('Saturday',$uni_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $uni_monthly_numdays = get_config($dbc, 'uni_monthly_numdays'); ?>
									<select name="uni_monthly_numdays" class="chosen-select-deselect">
										<option <?= $uni_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $uni_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $uni_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $uni_monthly_start = get_config($dbc, 'uni_monthly_start'); ?>
									<select name="uni_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $uni_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $uni_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $uni_monthly_days = explode(',',get_config($dbc, 'uni_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Sunday',$uni_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Monday',$uni_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Tuesday',$uni_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Wednesday',$uni_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Thursday',$uni_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Friday',$uni_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="uni_monthly_days[]" <?= in_array('Saturday',$uni_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $uni_wait_list = explode(',',get_config($dbc, 'uni_wait_list')); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_wait_list[]" <?= in_array('ticket', $uni_wait_list) ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="checkbox" name="uni_wait_list[]" <?= in_array('appt', $uni_wait_list) ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Blocks Month View - Combine Shifts with Items:</label>
								<div class="col-sm-8">
									<?php $uni_combine_shift_items = get_config($dbc, 'uni_combine_shift_items'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_combine_shift_items" <?= $uni_combine_shift_items == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= (in_array('ticket', $uni_wait_list) ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Universal Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $uni_use_shift_tickets = get_config($dbc, 'uni_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_use_shift_tickets" <?= $uni_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $uni_use_unbooked = get_config($dbc, 'uni_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_use_unbooked" <?= $uni_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $uni_offline = get_config($dbc, 'uni_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_offline" <?= $uni_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $uni_use_shifts = get_config($dbc, 'uni_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_use_shifts" <?= $uni_use_shifts != '' ? 'checked' : '' ?> value="1" onchange="showDefaultView(this);"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Teams:</label>
								<div class="col-sm-8">
									<?php $uni_teams = get_config($dbc, 'uni_teams'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_teams" <?= $uni_teams != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shifts_default_view" <?= $uni_use_shifts != '' ? '' : 'style="display:none;"' ?>>
								<label class="col-sm-4 control-label">Universal Calendar Default View:</label>
								<div class="col-sm-8">
									<?php $uni_default_view = get_config($dbc, 'uni_default_view'); ?>
									<label class="form-checkbox"><input type="radio" name="uni_default_view" value="" <?= empty($uni_default_view) ? 'checked' : '' ?>> Default</label>
									<label class="form-checkbox"><input type="radio" name="uni_default_view" value="shifts" <?= $uni_default_view == 'shifts' ? 'checked' : '' ?>> Shifts</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Client Type:</label>
								<div class="col-sm-8">
			                        <select name="uni_client_type[]" multiple data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
			                            <option value="">NO CLIENT</option>
			                            <?php $uni_client_type = get_config($dbc, 'uni_client_type');
			                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
			                            $result = mysqli_query($dbc, $query);
			                            while ($row = mysqli_fetch_array($result)) {
			                                echo '<option value="'.$row['category'].'"'.(strpos(','.$uni_client_type.',', ','.$row['category'].',') !== FALSE ? ' selected' : '').'>'.$row['category'].'</option>';
			                            }
			                            ?>
			                        </select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $uni_calendar_notes = get_config($dbc, 'uni_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_calendar_notes" <?= $uni_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $uni_reminders = get_config($dbc, 'uni_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_reminders" <?= $uni_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $uni_ticket_summary = get_config($dbc, 'uni_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_ticket_summary" <?= $uni_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar <?= TICKET_NOUN ?> Summary - Display Deleted:</label>
								<div class="col-sm-8">
									<?php $uni_ticket_summary_deleted = get_config($dbc, 'uni_ticket_summary_deleted'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_ticket_summary_deleted" <?= $uni_ticket_summary_deleted != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Universal Calendar No Shift Indicates:</label>
								<div class="col-sm-8">
									<?php $uni_availability_indication = get_config($dbc, 'uni_availability_indication'); ?>
									<label class="form-checkbox"><input type="radio" name="uni_availability_indication" <?= empty($uni_availability_indication) ? 'checked' : '' ?> value=""> All Day Availability</label>
									<label class="form-checkbox"><input type="radio" name="uni_availability_indication" <?= $uni_availability_indication == 1 ? 'checked' : '' ?> value="1"> No Availability</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Split Staff By Security Level:</label>
								<div class="col-sm-8">
									<?php $uni_staff_split_security = get_config($dbc, 'uni_staff_split_security'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_staff_split_security" <?= $uni_staff_split_security != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Active Client Frequency Per Staff:</label>
								<div class="col-sm-8">
									<?php $uni_client_staff_freq = get_config($dbc, 'uni_client_staff_freq'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_client_staff_freq" <?= $uni_client_staff_freq != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Clients Draggable To Book:</label>
								<div class="col-sm-8">
									<?php $uni_client_draggable = get_config($dbc, 'uni_client_draggable'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_client_draggable" <?= $uni_client_draggable != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable Staff Summary Tab:</label>
								<div class="col-sm-8">
									<?php $uni_staff_summary = get_config($dbc, 'uni_staff_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_staff_summary" <?= $uni_staff_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable Day Summary Tab:</label>
								<div class="col-sm-8">
									<?php $uni_day_summary_tab = get_config($dbc, 'uni_day_summary_tab'); ?>
									<label class="form-checkbox"><input type="checkbox" name="uni_day_summary_tab" <?= $uni_day_summary_tab != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Appointment Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_appt" >
								Appointment Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_appt" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $appt_increments = get_config($dbc, 'appt_increments'); ?>
									<select name="appt_increments" class="chosen-select-deselect"><option></option>
										<option <?= $appt_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $appt_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $appt_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $appt_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $appt_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $appt_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $appt_day_start = get_config($dbc, 'appt_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="appt_start" value="<?= $appt_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $appt_day_end = get_config($dbc, 'appt_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="appt_end" value="<?= $appt_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $appt_weekly_start = get_config($dbc, 'appt_weekly_start'); ?>
									<select name="appt_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $appt_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $appt_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $appt_weekly_days = explode(',',get_config($dbc, 'appt_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Sunday',$appt_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Monday',$appt_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Tuesday',$appt_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Wednesday',$appt_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Thursday',$appt_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Friday',$appt_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_weekly_days[]" <?= in_array('Saturday',$appt_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $appt_monthly_numdays = get_config($dbc, 'appt_monthly_numdays'); ?>
									<select name="appt_monthly_numdays" class="chosen-select-deselect">
										<option <?= $appt_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $appt_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $appt_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $appt_monthly_start = get_config($dbc, 'appt_monthly_start'); ?>
									<select name="appt_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $appt_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $appt_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $appt_monthly_days = explode(',',get_config($dbc, 'appt_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Sunday',$appt_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Monday',$appt_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Tuesday',$appt_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Wednesday',$appt_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Thursday',$appt_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Friday',$appt_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="appt_monthly_days[]" <?= in_array('Saturday',$appt_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $appt_wait_list = get_config($dbc, 'appt_wait_list'); ?>
									<label class="form-checkbox"><input type="radio" name="wait_list" <?= $appt_wait_list == 'ticket' ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="wait_list" <?= $appt_wait_list == 'workorder' ? 'checked' : '' ?> value="workorder"> Work Orders</label>
									<label class="form-checkbox"><input type="radio" name="wait_list" <?= $appt_wait_list == 'appt' ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($appt_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Appointment Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $appt_use_shift_tickets = get_config($dbc, 'appt_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_use_shift_tickets" <?= $appt_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $appt_use_unbooked = get_config($dbc, 'appt_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_use_unbooked" <?= $appt_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $appt_offline = get_config($dbc, 'appt_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_offline" <?= $appt_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $appt_use_shifts = get_config($dbc, 'appt_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_use_shifts" <?= $appt_use_shifts != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Teams:</label>
								<div class="col-sm-8">
									<?php $appt_teams = get_config($dbc, 'appt_teams'); ?>
									<label class="form-checkbox"><input type="checkbox" name="teams" <?= $appt_teams != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Equipment Assignment:</label>
								<div class="col-sm-8">
									<?php $appt_equip_assign = get_config($dbc, 'appt_equip_assign'); ?>
									<label class="form-checkbox"><input type="checkbox" name="equip_assign" <?= $appt_equip_assign != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $appt_calendar_notes = get_config($dbc, 'appt_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_calendar_notes" <?= $appt_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $appt_reminders = get_config($dbc, 'appt_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_reminders" <?= $appt_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $appt_ticket_summary = get_config($dbc, 'appt_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="appt_ticket_summary" <?= $appt_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Appointment Calendar No Shift Indicates:</label>
								<div class="col-sm-8">
									<?php $appt_availability_indication = get_config($dbc, 'appt_availability_indication'); ?>
									<label class="form-checkbox"><input type="radio" name="appt_availability_indication" <?= empty($appt_availability_indication) ? 'checked' : '' ?> value=""> All Day Availability</label>
									<label class="form-checkbox"><input type="radio" name="appt_availability_indication" <?= $appt_availability_indication == 1 ? 'checked' : '' ?> value="1"> No Availability</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Staff Schedule Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff_schedule" >
								Staff Schedule Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_staff_schedule" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_increments = get_config($dbc, 'staff_schedule_increments'); ?>
									<select name="staff_schedule_increments" class="chosen-select-deselect"><option></option>
										<option <?= $staff_schedule_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $staff_schedule_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $staff_schedule_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $staff_schedule_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $staff_schedule_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $staff_schedule_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_day_start = get_config($dbc, 'staff_schedule_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="staff_schedule_start" value="<?= $staff_schedule_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_day_end = get_config($dbc, 'staff_schedule_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="staff_schedule_end" value="<?= $staff_schedule_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_weekly_start = get_config($dbc, 'staff_schedule_weekly_start'); ?>
									<select name="staff_schedule_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $staff_schedule_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $staff_schedule_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_weekly_days = explode(',',get_config($dbc, 'staff_schedule_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Sunday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Monday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Tuesday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Wednesday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Thursday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Friday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_weekly_days[]" <?= in_array('Saturday',$staff_schedule_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $staff_schedule_monthly_numdays = get_config($dbc, 'staff_schedule_monthly_numdays'); ?>
									<select name="staff_schedule_monthly_numdays" class="chosen-select-deselect">
										<option <?= $staff_schedule_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $staff_schedule_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $staff_schedule_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_monthly_start = get_config($dbc, 'staff_schedule_monthly_start'); ?>
									<select name="staff_schedule_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $staff_schedule_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $staff_schedule_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_monthly_days = explode(',',get_config($dbc, 'staff_schedule_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Sunday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Monday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Tuesday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Wednesday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Thursday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Friday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_monthly_days[]" <?= in_array('Saturday',$staff_schedule_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_wait_list = get_config($dbc, 'staff_schedule_wait_list'); ?>
									<label class="form-checkbox"><input type="radio" name="staff_schedule_wait_list" <?= $staff_schedule_wait_list == 'ticket' ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="staff_schedule_wait_list" <?= $staff_schedule_wait_list == 'workorder' ? 'checked' : '' ?> value="workorder"> Work Orders</label>
									<label class="form-checkbox"><input type="radio" name="staff_schedule_wait_list" <?= $staff_schedule_wait_list == 'appt' ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($staff_schedule_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_use_shift_tickets = get_config($dbc, 'staff_schedule_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_use_shift_tickets" <?= $staff_schedule_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($staff_schedule_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Staff Schedule Calendar All <?= TICKET_TILE ?> Button:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_use_all_tickets = get_config($dbc, 'staff_schedule_use_all_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_use_all_tickets" <?= $staff_schedule_use_all_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_use_unbooked = get_config($dbc, 'staff_schedule_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_use_unbooked" <?= $staff_schedule_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_offline = get_config($dbc, 'staff_schedule_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_offline" <?= $staff_schedule_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_use_shifts = get_config($dbc, 'staff_schedule_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_use_shifts" <?= $staff_schedule_use_shifts != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Teams:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_teams = get_config($dbc, 'staff_schedule_teams'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_teams" <?= $staff_schedule_teams != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Equipment Assignment:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_equip_assign = get_config($dbc, 'staff_schedule_equip_assign'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_equip_assign" <?= $staff_schedule_equip_assign != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Client Type:</label>
								<div class="col-sm-8">
			                        <select name="staff_schedule_client_type" data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
			                            <option></option>
			                            <?php $staff_schedule_client_type = get_config($dbc, 'staff_schedule_client_type');
			                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
			                            $result = mysqli_query($dbc, $query);
			                            while ($row = mysqli_fetch_array($result)) {
			                                echo '<option value="'.$row['category'].'"'.($row['category'] == $staff_schedule_client_type ? ' selected' : '').'>'.$row['category'].'</option>';
			                            }
			                            ?>
			                        </select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_calendar_notes = get_config($dbc, 'staff_schedule_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_calendar_notes" <?= $staff_schedule_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_reminders = get_config($dbc, 'staff_schedule_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_reminders" <?= $staff_schedule_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_ticket_summary = get_config($dbc, 'staff_schedule_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="staff_schedule_ticket_summary" <?= $staff_schedule_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Schedule Calendar No Shift Indicates:</label>
								<div class="col-sm-8">
									<?php $staff_schedule_availability_indication = get_config($dbc, 'staff_schedule_availability_indication'); ?>
									<label class="form-checkbox"><input type="radio" name="staff_schedule_availability_indication" <?= empty($staff_schedule_availability_indication) ? 'checked' : '' ?> value=""> All Day Availability</label>
									<label class="form-checkbox"><input type="radio" name="staff_schedule_availability_indication" <?= $staff_schedule_availability_indication == 1 ? 'checked' : '' ?> value="1"> No Availability</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Dispatch Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dispatch" >
								Dispatch Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_dispatch" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $scheduling_increments = get_config($dbc, 'scheduling_increments'); ?>
									<select name="scheduling_increments" class="chosen-select-deselect"><option></option>
										<option <?= $scheduling_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $scheduling_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $scheduling_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $scheduling_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $scheduling_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $scheduling_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $scheduling_day_start = get_config($dbc, 'scheduling_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="scheduling_start" value="<?= $scheduling_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $scheduling_day_end = get_config($dbc, 'scheduling_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="scheduling_end" value="<?= $scheduling_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $scheduling_weekly_start = get_config($dbc, 'scheduling_weekly_start'); ?>
									<select name="scheduling_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $scheduling_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $scheduling_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $scheduling_weekly_days = explode(',',get_config($dbc, 'scheduling_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Sunday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Monday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Tuesday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Wednesday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Thursday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Friday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_weekly_days[]" <?= in_array('Saturday',$scheduling_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $scheduling_monthly_numdays = get_config($dbc, 'scheduling_monthly_numdays'); ?>
									<select name="scheduling_monthly_numdays" class="chosen-select-deselect">
										<option <?= $scheduling_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $scheduling_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $scheduling_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $scheduling_monthly_start = get_config($dbc, 'scheduling_monthly_start'); ?>
									<select name="scheduling_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $scheduling_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $scheduling_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $scheduling_monthly_days = explode(',',get_config($dbc, 'scheduling_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Sunday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Monday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Tuesday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Wednesday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Thursday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Friday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_monthly_days[]" <?= in_array('Saturday',$scheduling_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $scheduling_wait_list = get_config($dbc, 'scheduling_wait_list'); ?>
									<label class="form-checkbox"><input type="radio" name="scheduling_wait_list" <?= $scheduling_wait_list == 'ticket' ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="scheduling_wait_list" <?= $scheduling_wait_list == 'ticket_multi' ? 'checked' : '' ?> value="ticket_multi"> Multi-Book <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="scheduling_wait_list" <?= $scheduling_wait_list == 'workorder' ? 'checked' : '' ?> value="workorder"> Work Orders</label>
									<label class="form-checkbox"><input type="radio" name="scheduling_wait_list" <?= $scheduling_wait_list == 'appt' ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Expand Rows to show Details:</label>
								<div class="col-sm-8"><?php
                                    $auto_size_calendar_blocks = get_config($dbc, 'auto_size_calendar_blocks'); ?>
                                    <label class="form-checkbox"><input type="radio" name="auto_size_calendar_blocks" value="true" <?= $auto_size_calendar_blocks=='true' ? 'checked' : ''; ?> />Enable</label>
                                    <label class="form-checkbox"><input type="radio" name="auto_size_calendar_blocks" value="" <?= $auto_size_calendar_blocks=='true' ? '' : 'checked'; ?> />Disable</label>
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Jump to Current Time:</label>
								<div class="col-sm-8"><?php
                                    $auto_show_time = get_config($dbc, 'auto_show_time'); ?>
                                    <label class="form-checkbox"><input type="radio" name="auto_show_time" value="true" <?= $auto_show_time=='true' ? 'checked' : ''; ?> />Enable</label>
                                    <label class="form-checkbox"><input type="radio" name="auto_show_time" value="" <?= $auto_show_time=='true' ? '' : 'checked'; ?> />Disable</label>
                                </div>
                            </div>
							<div class="form-group shift_tickets_block" <?= ($scheduling_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Dispatch Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $scheduling_use_shift_tickets = get_config($dbc, 'scheduling_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_use_shift_tickets" <?= $scheduling_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($scheduling_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label">Appointment Calendar New <?= TICKET_NOUN ?> Button:</label>
								<div class="col-sm-8">
									<?php $scheduling_new_ticket_button = get_config($dbc, 'scheduling_new_ticket_button'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_new_ticket_button" <?= $scheduling_new_ticket_button != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will restrict this Calendar to only be able to view these <?= TICKET_NOUN ?> Types. If none are checked, the software will assume there are no restrictions.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Restrict <?= TICKET_NOUN ?> Types:</label>
								<div class="col-sm-8">
									<?php $scheduling_restrict_ticket_types = ','.get_config($dbc, 'scheduling_restrict_ticket_types').','; ?>
                                    <?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
                                        $ticket_type_value = config_safe_str($ticket_type);
                                        echo '<label class="form-checkbox"><input type="checkbox" name="scheduling_restrict_ticket_types[]" '.(strpos($scheduling_restrict_ticket_types,','.$ticket_type_value.',') !== FALSE ? 'checked' : '').' value="'.$ticket_type_value.'"> '.$ticket_type.'</label>';
                                    } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $scheduling_use_unbooked = get_config($dbc, 'scheduling_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_use_unbooked" <?= $scheduling_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $scheduling_offline = get_config($dbc, 'scheduling_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_offline" <?= $scheduling_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $scheduling_use_shifts = get_config($dbc, 'scheduling_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_use_shifts" <?= $scheduling_use_shifts != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Teams:</label>
								<div class="col-sm-8">
									<?php $scheduling_teams = get_config($dbc, 'scheduling_teams'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_teams" <?= $scheduling_teams != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Multiple Equipment per Staff:</label>
								<div class="col-sm-8">
									<?php $equip_multi_assign_staff_disallow = get_config($dbc, 'equip_multi_assign_staff_disallow'); ?>
									<label class="form-checkbox any-width"><input type="radio" name="equip_multi_assign_staff_disallow" <?= $equip_multi_assign_staff_disallow == '1' ? 'checked' : '' ?> value="1">Do Not Allow Staff to be Assigned to Multiple</label>
									<label class="form-checkbox any-width"><input type="radio" name="equip_multi_assign_staff_disallow" <?= $equip_multi_assign_staff_disallow == '1' ? '' : 'checked' ?> value="0">Allow Staff to be Assigned to Multiple</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Filters:</label>
								<div class="col-sm-8">
									<?php $scheduling_filters = get_config($dbc, 'scheduling_filters'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_filters[]" <?= strpos(",$scheduling_filters,",",Ticket Type,") !== FALSE ? 'checked' : '' ?> value="Ticket Type"> <?= TICKET_NOUN ?> Type</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_filters[]" <?= strpos(",$scheduling_filters,",",Region,") !== FALSE ? 'checked' : '' ?> value="Region"> Region</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_filters[]" <?= strpos(",$scheduling_filters,",",Location,") !== FALSE ? 'checked' : '' ?> value="Location"> Location</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_filters[]" <?= strpos(",$scheduling_filters,",",Classification,") !== FALSE ? 'checked' : '' ?> value="Classification"> Classification</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Client Type:</label>
								<div class="col-sm-8">
			                        <select name="scheduling_client_type" data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
			                            <option></option>
			                            <?php $scheduling_client_type = get_config($dbc, 'scheduling_client_type');
			                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
			                            $result = mysqli_query($dbc, $query);
			                            while ($row = mysqli_fetch_array($result)) {
			                                echo '<option value="'.$row['category'].'"'.($row['category'] == $scheduling_client_type ? ' selected' : '').'>'.$row['category'].'</option>';
			                            }
			                            ?>
			                        </select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $scheduling_calendar_notes = get_config($dbc, 'scheduling_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_calendar_notes" <?= $scheduling_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch <?= TICKET_NOUN ?> Sorting:</label>
								<div class="col-sm-8">
									<?php $scheduling_calendar_sort_auto = explode(',',get_config($dbc, 'scheduling_calendar_sort_auto')); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_calendar_sort_auto[]" <?= in_array('map_sort', $scheduling_calendar_sort_auto) ? 'checked' : '' ?> value="map_sort"> Allow Auto-Sort using Map</label>
									<label class="form-checkbox any-width"><input type="checkbox" name="scheduling_calendar_sort_auto[]" <?= in_array('hide_unoptimized', $scheduling_calendar_sort_auto) ? 'checked' : '' ?> value="hide_unoptimized">Do Not Show Unoptimized <?= TICKET_TILE ?></label>
									<label class="form-checkbox any-width"><input type="checkbox" name="scheduling_calendar_sort_auto[]" <?= in_array('limit_single_optimize', $scheduling_calendar_sort_auto) ? 'checked' : '' ?> value="limit_single_optimize">Hide Sort Button After Use</label>
                                    <?php foreach(array_filter(explode(',',get_config($dbc, '%_classification', true, ','))) as $class_name) { ?>
                                        <label class="form-checkbox any-width"><input type="checkbox" name="scheduling_calendar_sort_auto[]" <?= in_array('class_'.config_safe_str($class_name), $scheduling_calendar_sort_auto) ? 'checked' : '' ?> value="class_<?= config_safe_str($class_name) ?>">Use Sort Button for Classification: <?= $class_name ?></label>
                                    <?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch <?= TICKET_NOUN ?> Estimate Time:</label>
								<div class="col-sm-8">
									<label class="form-checkbox"><input type="radio" name="scheduling_calendar_est_time" <?= get_config($dbc, "scheduling_calendar_est_time") == 'auto_sort' ? 'checked' : '' ?> value="auto_sort"> Auto Add Time to <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="scheduling_calendar_est_time" <?= get_config($dbc, "scheduling_calendar_est_time") == '' ? 'checked' : '' ?> value=""> No Change of Time</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Dispatch Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $scheduling_ticket_summary = get_config($dbc, 'scheduling_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_ticket_summary" <?= $scheduling_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Equipment/Team/Staff Filters:</label>
								<div class="col-sm-8">
									<?php $scheduling_item_filters = get_config($dbc, 'scheduling_item_filters'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_item_filters[]" <?= strpos(",$scheduling_item_filters,",",Region,") !== FALSE ? 'checked' : '' ?> value="Region"> Region</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_item_filters[]" <?= strpos(",$scheduling_item_filters,",",Location,") !== FALSE ? 'checked' : '' ?> value="Location"> Location</label>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_item_filters[]" <?= strpos(",$scheduling_item_filters,",",Classification,") !== FALSE ? 'checked' : '' ?> value="Classification"> Classification</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Active Classification Logged In Users:</label>
								<div class="col-sm-8">
									<?php $scheduling_classification_loggedin = get_config($dbc, 'scheduling_classification_loggedin'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_classification_loggedin" <?= $scheduling_classification_loggedin != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Multiple Classification Filters Admin Only:</label>
								<div class="col-sm-8">
									<?php $scheduling_multi_class_admin = get_config($dbc, 'scheduling_multi_class_admin'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_multi_class_admin" <?= $scheduling_multi_class_admin == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Combine Time Conflicts:</label>
								<div class="col-sm-8">
									<?php $scheduling_combine_time = get_config($dbc, 'scheduling_combine_time'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_combine_time" <?= $scheduling_combine_time == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Combine Warehouse Stops:</label>
								<div class="col-sm-8">
									<?php $scheduling_combine_warehouse = get_config($dbc, 'scheduling_combine_warehouse'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_combine_warehouse" <?= $scheduling_combine_warehouse == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Combine Pick Up Stops:</label>
								<div class="col-sm-8">
									<?php $scheduling_combine_pickup = get_config($dbc, 'scheduling_combine_pickup'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_combine_pickup" <?= $scheduling_combine_pickup == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Don't Count Warehouse As A Delivery Stop:</label>
								<div class="col-sm-8">
									<?php $scheduling_dont_count_warehouse = get_config($dbc, 'scheduling_dont_count_warehouse'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_dont_count_warehouse" <?= $scheduling_dont_count_warehouse == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will enable the Summary tab, which is a monthly view of all assigned Equipment in a Summary view with no functionality.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Enable Summary Tab:</label>
								<div class="col-sm-8">
									<?php $scheduling_summary_view = get_config($dbc, 'scheduling_summary_view'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_summary_view" <?= $scheduling_summary_view == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will disable a Warning message if there are more than this # of <?= TICKET_TILE?>. Leave blank for no Warnings.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Display Warning After # of <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $scheduling_warning_num_tickets = get_config($dbc, 'scheduling_warning_num_tickets'); ?>
									<input type="number" name="scheduling_warning_num_tickets" value="<?= $scheduling_warning_num_tickets ?>" class="form-control"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Equipment Display Classification:</label>
								<div class="col-sm-8">
									<?php $scheduling_equip_classification = get_config($dbc, 'scheduling_equip_classification'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_equip_classification" <?= $scheduling_equip_classification == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Equipment Display Classification - Based on <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $scheduling_equip_classification_ticket = get_config($dbc, 'scheduling_equip_classification_ticket'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_equip_classification_ticket" <?= $scheduling_equip_classification_ticket == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Toggle All Assigned Equipment When Changing Dates:</label>
								<div class="col-sm-8">
									<?php $scheduling_reset_active = get_config($dbc, 'scheduling_reset_active'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_reset_active" <?= $scheduling_reset_active == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Service Follow Up Date On Warning:</label>
								<div class="col-sm-8">
									<?php $scheduling_service_followup = get_config($dbc, 'scheduling_service_followup'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_service_followup" <?= $scheduling_service_followup == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Next Service Date On Warning:</label>
								<div class="col-sm-8">
									<?php $scheduling_service_date = get_config($dbc, 'scheduling_service_date'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_service_date" <?= $scheduling_service_date == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Passed Service Date On Warning:</label>
								<div class="col-sm-8">
									<?php $scheduling_passed_service = get_config($dbc, 'scheduling_passed_service'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_passed_service" <?= $scheduling_passed_service == 1 ? 'checked' : '' ?> value="1"></label>
				                </div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Group Columns by Region:</label>
								<div class="col-sm-8">
									<?php $scheduling_columns_group_regions = get_config($dbc, 'scheduling_columns_group_regions'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_columns_group_regions" <?= $scheduling_columns_group_regions == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Allow Dragging Multiple:</label>
								<div class="col-sm-8">
									<?php $scheduling_drag_multiple = get_config($dbc, 'scheduling_drag_multiple'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_drag_multiple" <?= $scheduling_drag_multiple == 1 ? 'checked' : '' ?> value="1"></label>
				                </div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will display the Calendar to the selected Security Levels with a limited view to only things related to them.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Customer View Security Levels:</label>
								<div class="col-sm-8">
									<select name="scheduling_customer_roles[]" multiple class="chosen-select-deselect">
										<?php $scheduling_customer_roles = array_filter(explode(',',get_config($dbc, 'scheduling_customer_roles')));
										$on_security = get_security_levels($dbc);
										foreach($on_security as $security_label => $security_value) {
											echo '<option value="'.$security_value.'" '.(in_array($security_value,$scheduling_customer_roles) ? 'selected' : '').'>'.$security_label.'</option>';
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Split Staff By Security Level:</label>
								<div class="col-sm-8">
									<?php $scheduling_staff_split_security = get_config($dbc, 'scheduling_staff_split_security'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_staff_split_security" <?= $scheduling_staff_split_security != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Split Contractors By Security Level:</label>
								<div class="col-sm-8">
									<?php $scheduling_contractor_split_security = get_config($dbc, 'scheduling_contractor_split_security'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_contractor_split_security" <?= $scheduling_contractor_split_security != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Export Time Table Button:</label>
								<div class="col-sm-8">
									<?php $scheduling_export_time_table = get_config($dbc, 'scheduling_export_time_table'); ?>
									<label class="form-checkbox"><input type="checkbox" name="scheduling_export_time_table" <?= $scheduling_export_time_table != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Time Table PDF Logo:</label>
								<div class="col-sm-8">
			                        <div class="logo_url">
			                            <?php $scheduling_time_table_logo = get_config($dbc, 'scheduling_time_table_logo');
			                            if(!empty($scheduling_time_table_logo) && file_exists('download/'.$scheduling_time_table_logo)) { ?>
			                                <a href="download/<?= $scheduling_time_table_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('scheduling_time_table_logo'); return false;">Delete</a>
			                            <?php } ?>
			                        </div>
			                        <input name="scheduling_time_table_logo" type="file" data-filename-placement="inside" class="form-control" />
								</div>
							</div>
			                <div class="form-group">
			                    <label class="col-sm-4 control-label">Time Table PDF Logo Align:</label>
			                    <div class="col-sm-8">
			                    	<?php $scheduling_time_table_logo_align = get_config($dbc, 'scheduling_time_table_logo_align'); ?>
			                        <select name="scheduling_time_table_logo_align" class="chosen-select-deselect form-control">
			                            <option></option>
			                            <option <?= $scheduling_time_table_logo_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
			                            <option <?= $scheduling_time_table_logo_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
			                            <option <?= $scheduling_time_table_logo_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
			                        </select>
			                    </div>
			                </div>
						</div>
					</div>
				</div>

                <div class="panel panel-default" <?= (!in_array('Sales Estimates Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_estimates_calendar" >
								Sales <?= ESTIMATE_TILE ?> Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_estimates_calendar" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $estimates_increments = get_config($dbc, 'estimates_increments'); ?>
									<select name="estimates_increments" class="chosen-select-deselect"><option></option>
										<option <?= $estimates_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $estimates_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $estimates_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $estimates_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $estimates_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $estimates_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $estimates_day_start = get_config($dbc, 'estimates_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="estimates_start" value="<?= $estimates_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $estimates_day_end = get_config($dbc, 'estimates_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="estimates_end" value="<?= $estimates_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $estimates_weekly_start = get_config($dbc, 'estimates_weekly_start'); ?>
									<select name="estimates_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $estimates_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $estimates_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $estimates_weekly_days = explode(',',get_config($dbc, 'estimates_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Sunday',$estimates_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Monday',$estimates_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Tuesday',$estimates_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Wednesday',$estimates_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Thursday',$estimates_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Friday',$estimates_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_weekly_days[]" <?= in_array('Saturday',$estimates_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $estimates_monthly_numdays = get_config($dbc, 'estimates_monthly_numdays'); ?>
									<select name="estimates_monthly_numdays" class="chosen-select-deselect">
										<option <?= $estimates_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $estimates_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $estimates_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $estimates_monthly_start = get_config($dbc, 'estimates_monthly_start'); ?>
									<select name="estimates_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $estimates_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $estimates_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $estimates_monthly_days = explode(',',get_config($dbc, 'estimates_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Sunday',$estimates_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Monday',$estimates_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Tuesday',$estimates_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Wednesday',$estimates_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Thursday',$estimates_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Friday',$estimates_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="estimates_monthly_days[]" <?= in_array('Saturday',$estimates_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $estimates_calendar_notes = get_config($dbc, 'estimates_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="estimates_calendar_notes" <?= $estimates_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sales <?= ESTIMATE_TILE ?> Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $estimates_reminders = get_config($dbc, 'estimates_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="estimates_reminders" <?= $estimates_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Ticket Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket_calendar" >
								<?= TICKET_NOUN ?> Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_calendar" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $ticket_increments = get_config($dbc, 'ticket_increments'); ?>
									<select name="ticket_increments" class="chosen-select-deselect"><option></option>
										<option <?= $ticket_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $ticket_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $ticket_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $ticket_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $ticket_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $ticket_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $ticket_day_start = get_config($dbc, 'ticket_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="ticket_start" value="<?= $ticket_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $ticket_day_end = get_config($dbc, 'ticket_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="ticket_end" value="<?= $ticket_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $ticket_weekly_start = get_config($dbc, 'ticket_weekly_start'); ?>
									<select name="ticket_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $ticket_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $ticket_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $ticket_weekly_days = explode(',',get_config($dbc, 'ticket_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Sunday',$ticket_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Monday',$ticket_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Tuesday',$ticket_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Wednesday',$ticket_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Thursday',$ticket_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Friday',$ticket_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_weekly_days[]" <?= in_array('Saturday',$ticket_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $ticket_monthly_numdays = get_config($dbc, 'ticket_monthly_numdays'); ?>
									<select name="ticket_monthly_numdays" class="chosen-select-deselect">
										<option <?= $ticket_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $ticket_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $ticket_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $ticket_monthly_start = get_config($dbc, 'ticket_monthly_start'); ?>
									<select name="ticket_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $ticket_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $ticket_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $ticket_monthly_days = explode(',',get_config($dbc, 'ticket_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Sunday',$ticket_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Monday',$ticket_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Tuesday',$ticket_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Wednesday',$ticket_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Thursday',$ticket_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Friday',$ticket_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_monthly_days[]" <?= in_array('Saturday',$ticket_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Blocks:</label>
								<div class="col-sm-8">
									<?php $ticket_wait_list = get_config($dbc, 'ticket_wait_list'); ?>
									<label class="form-checkbox"><input type="radio" name="ticket_wait_list" <?= $ticket_wait_list == 'ticket' ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?></label>
									<label class="form-checkbox"><input type="radio" name="ticket_wait_list" <?= $ticket_wait_list == 'workorder' ? 'checked' : '' ?> value="workorder"> Work Orders</label>
									<label class="form-checkbox"><input type="radio" name="ticket_wait_list" <?= $ticket_wait_list == 'appt' ? 'checked' : '' ?> value="appt"> Appointments</label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($ticket_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Use Shift <?= TICKET_TILE ?>:</label>
								<div class="col-sm-8">
									<?php $ticket_use_shift_tickets = get_config($dbc, 'ticket_use_shift_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_use_shift_tickets" <?= $ticket_use_shift_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group shift_tickets_block" <?= ($ticket_wait_list == 'ticket' ? '' : 'style="display:none;"') ?>>
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar All <?= TICKET_TILE ?> Button:</label>
								<div class="col-sm-8">
									<?php $ticket_use_all_tickets = get_config($dbc, 'ticket_use_all_tickets'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_use_all_tickets" <?= $ticket_use_all_tickets != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Sidebar Filters:</label>
								<div class="col-sm-8">
									<?php $ticket_sidebar_filters = ','.get_config($dbc, 'ticket_sidebar_filters').','; ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_sidebar_filters[]" <?= strpos($ticket_sidebar_filters,',Region,') !== FALSE ? 'checked' : '' ?> value="Region"> Region</label>
									<label class="form-checkbox"><input type="checkbox" name="ticket_sidebar_filters[]" <?= strpos($ticket_sidebar_filters,',Ticket Type,') !== FALSE ? 'checked' : '' ?> value="Ticket Type"> <?= TICKET_NOUN ?> Type</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will restrict this Calendar to only be able to view these <?= TICKET_NOUN ?> Types. If none are checked, the software will assume there are no restrictions.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Restrict <?= TICKET_NOUN ?> Types:</label>
								<div class="col-sm-8">
									<?php $ticket_restrict_ticket_types = ','.get_config($dbc, 'ticket_restrict_ticket_types').','; ?>
                                    <?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
                                        $ticket_type_value = config_safe_str($ticket_type);
                                        echo '<label class="form-checkbox"><input type="checkbox" name="ticket_restrict_ticket_types[]" '.(strpos($ticket_restrict_ticket_types,','.$ticket_type_value.',') !== FALSE ? 'checked' : '').' value="'.$ticket_type_value.'"> '.$ticket_type.'</label>';
                                    } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?>  Calendar Include Equipment:</label>
								<div class="col-sm-8">
									<?php $ticket_use_equipment = get_config($dbc, 'ticket_use_equipment'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_use_equipment" <?= $ticket_use_equipment != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?>  Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $ticket_offline = get_config($dbc, 'ticket_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_offline" <?= $ticket_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?>  Calendar Use Unbooked List:</label>
								<div class="col-sm-8">
									<?php $ticket_use_unbooked = get_config($dbc, 'ticket_use_unbooked'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_use_unbooked" <?= $ticket_use_unbooked != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?>  Calendar Use Shifts:</label>
								<div class="col-sm-8">
									<?php $ticket_use_shifts = get_config($dbc, 'ticket_use_shifts'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_use_shifts" <?= $ticket_use_shifts != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Use Teams:</label>
								<div class="col-sm-8">
									<?php $ticket_teams = get_config($dbc, 'ticket_teams'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_teams" <?= $ticket_teams != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Use Equipment Assignment:</label>
								<div class="col-sm-8">
									<?php $ticket_equip_assign = get_config($dbc, 'ticket_equip_assign'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_equip_assign" <?= $ticket_equip_assign != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Client Type:</label>
								<div class="col-sm-8">
			                        <select name="ticket_client_type[]" multiple data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
			                            <option value="">NO CLIENT</option>
			                            <?php $ticket_client_type = get_config($dbc, 'ticket_client_type');
			                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
			                            $result = mysqli_query($dbc, $query);
			                            while ($row = mysqli_fetch_array($result)) {
			                                echo '<option value="'.$row['category'].'"'.(strpos(','.$ticket_client_type.',', ','.$row['category'].',') !== FALSE ? ' selected' : '').'>'.$row['category'].'</option>';
			                            }
			                            ?>
			                        </select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $ticket_calendar_notes = get_config($dbc, 'ticket_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_calendar_notes" <?= $ticket_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $ticket_reminders = get_config($dbc, 'ticket_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_reminders" <?= $ticket_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $ticket_ticket_summary = get_config($dbc, 'ticket_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_ticket_summary" <?= $ticket_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Calendar <?= TICKET_NOUN ?> Summary - Display Deleted:</label>
								<div class="col-sm-8">
									<?php $ticket_ticket_summary_deleted = get_config($dbc, 'ticket_ticket_summary_deleted'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_ticket_summary_deleted" <?= $ticket_ticket_summary_deleted != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Ticket Calendar No Shift Indicates:</label>
								<div class="col-sm-8">
									<?php $ticket_availability_indication = get_config($dbc, 'ticket_availability_indication'); ?>
									<label class="form-checkbox"><input type="radio" name="ticket_availability_indication" <?= empty($ticket_availability_indication) ? 'checked' : '' ?> value=""> All Day Availability</label>
									<label class="form-checkbox"><input type="radio" name="ticket_availability_indication" <?= $ticket_availability_indication == 1 ? 'checked' : '' ?> value="1"> No Availability</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Split Staff By Security Level:</label>
								<div class="col-sm-8">
									<?php $ticket_staff_split_security = get_config($dbc, 'ticket_staff_split_security'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_staff_split_security" <?= $ticket_staff_split_security != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Active Client Frequency Per Staff:</label>
								<div class="col-sm-8">
									<?php $ticket_client_staff_freq = get_config($dbc, 'ticket_client_staff_freq'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_client_staff_freq" <?= $ticket_client_staff_freq != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Clients Draggable To Book:</label>
								<div class="col-sm-8">
									<?php $ticket_client_draggable = get_config($dbc, 'ticket_client_draggable'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_client_draggable" <?= $ticket_client_draggable != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable Staff Summary Tab:</label>
								<div class="col-sm-8">
									<?php $ticket_staff_summary = get_config($dbc, 'ticket_staff_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_staff_summary" <?= $ticket_staff_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable <?= TICKET_NOUN ?> Summary Tab:</label>
								<div class="col-sm-8">
									<?php $ticket_ticket_summary_tab = get_config($dbc, 'ticket_ticket_summary_tab'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_ticket_summary_tab" <?= $ticket_ticket_summary_tab != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable <?= TICKET_NOUN ?> Summary Tab - Display Deleted:</label>
								<div class="col-sm-8">
									<?php $ticket_ticket_summary_tab_deleted = get_config($dbc, 'ticket_ticket_summary_tab_deleted'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_ticket_summary_tab_deleted" <?= $ticket_ticket_summary_tab_deleted != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable Client Tab:</label>
								<div class="col-sm-8">
									<?php $ticket_client_tab = get_config($dbc, 'ticket_client_tab'); ?>
									<label class="form-checkbox"><input type="checkbox" name="ticket_client_tab" <?= $ticket_client_tab != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Shift Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_shift" >
								Shift Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_shift" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $shift_increments = get_config($dbc, 'shift_increments'); ?>
									<select name="shift_increments" class="chosen-select-deselect"><option></option>
										<option <?= $shift_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $shift_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $shift_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $shift_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $shift_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $shift_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $shift_day_start = get_config($dbc, 'shift_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="shift_start" value="<?= $shift_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $shift_day_end = get_config($dbc, 'shift_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="shift_end" value="<?= $shift_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $shift_weekly_start = get_config($dbc, 'shift_weekly_start'); ?>
									<select name="shift_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $shift_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $shift_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $shift_weekly_days = explode(',',get_config($dbc, 'shift_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Sunday',$shift_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Monday',$shift_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Tuesday',$shift_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Wednesday',$shift_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Thursday',$shift_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Friday',$shift_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_weekly_days[]" <?= in_array('Saturday',$shift_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $shift_monthly_numdays = get_config($dbc, 'shift_monthly_numdays'); ?>
									<select name="shift_monthly_numdays" class="chosen-select-deselect">
										<option <?= $shift_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $shift_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $shift_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $shift_monthly_start = get_config($dbc, 'shift_monthly_start'); ?>
									<select name="shift_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $shift_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $shift_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $shift_monthly_days = explode(',',get_config($dbc, 'shift_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Sunday',$shift_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Monday',$shift_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Tuesday',$shift_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Wednesday',$shift_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Thursday',$shift_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Friday',$shift_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="shift_monthly_days[]" <?= in_array('Saturday',$shift_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Use Notes:</label>
								<div class="col-sm-8">
									<?php $shift_calendar_notes = get_config($dbc, 'shift_calendar_notes'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_calendar_notes" <?= $shift_calendar_notes != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Use Reminders:</label>
								<div class="col-sm-8">
									<?php $shift_reminders = get_config($dbc, 'shift_reminders'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_reminders" <?= $shift_reminders != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $shift_offline = get_config($dbc, 'shift_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_offline" <?= $shift_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Shift Calendar Select All Staff Checkbox:</label>
								<div class="col-sm-8">
									<?php $shift_select_all_staff = get_config($dbc, 'shift_select_all_staff'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_select_all_staff" <?= $shift_select_all_staff == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Selected Staff Icons:</label>
								<div class="col-sm-8">
									<?php $shift_selected_staff_icons = get_config($dbc, 'shift_selected_staff_icons'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_selected_staff_icons" <?= $shift_selected_staff_icons == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Display Selected Client Icons:</label>
								<div class="col-sm-8">
									<?php $shift_selected_client_icons = get_config($dbc, 'shift_selected_client_icons'); ?>
									<label class="form-checkbox"><input type="checkbox" name="shift_selected_client_icons" <?= $shift_selected_client_icons == 1 ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" <?= (!in_array('Events Calendar', $calendar_types) ? 'style="display:none;"' : '') ?>>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_event" >
								Events Calendar Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_event" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Time Increments:</label>
								<div class="col-sm-8">
									<?php $event_increments = get_config($dbc, 'event_increments'); ?>
									<select name="event_increments" class="chosen-select-deselect"><option></option>
										<option <?= $event_increments == '5' ? 'selected' : '' ?> value="5">5 minutes</option>
										<option <?= $event_increments == '10' ? 'selected' : '' ?> value="10">10 minutes</option>
										<option <?= $event_increments == '15' ? 'selected' : '' ?> value="15">15 minutes</option>
										<option <?= $event_increments == '20' ? 'selected' : '' ?> value="20">20 minutes</option>
										<option <?= $event_increments == '30' ? 'selected' : '' ?> value="30">30 minutes</option>
										<option <?= $event_increments == '60' ? 'selected' : '' ?> value="60">60 minutes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Day Start:</label>
								<div class="col-sm-8">
									<?php $event_day_start = get_config($dbc, 'event_day_start'); ?>
									<input type="text" class="form-control datetimepicker" name="event_start" value="<?= $event_day_start ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Day End:</label>
								<div class="col-sm-8">
									<?php $event_day_end = get_config($dbc, 'event_day_end'); ?>
									<input type="text" class="form-control datetimepicker" name="event_end" value="<?= $event_day_end ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Weekly View Week Start:</label>
								<div class="col-sm-8">
									<?php $event_weekly_start = get_config($dbc, 'event_weekly_start'); ?>
									<select name="event_weekly_start" class="chosen-select-deselect"><option></option>
										<option <?= $event_weekly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $event_weekly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Weekly View Days Included:</label>
								<div class="col-sm-8">
									<?php $event_weekly_days = explode(',',get_config($dbc, 'event_weekly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Sunday',$event_weekly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Monday',$event_weekly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Tuesday',$event_weekly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Wednesday',$event_weekly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Thursday',$event_weekly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Friday',$event_weekly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_weekly_days[]" <?= in_array('Saturday',$event_weekly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Monthly View # Of Days</label>
								<div class="col-sm-8">
									<?php $event_monthly_numdays = get_config($dbc, 'event_monthly_numdays'); ?>
									<select name="event_monthly_numdays" class="chosen-select-deselect">
										<option <?= $event_monthly_numdays == '' ? 'selected' : '' ?> value="">Month</option>
										<option <?= $event_monthly_numdays == 'week' ? 'selected' : '' ?> value="week">Month &amp; 1 Week</option>
										<option <?= $event_monthly_numdays == 'month' ? 'selected' : '' ?> value="month">Month &amp; Next Month</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Monthly View Week Start:</label>
								<div class="col-sm-8">
									<?php $event_monthly_start = get_config($dbc, 'event_monthly_start'); ?>
									<select name="event_monthly_start" class="chosen-select-deselect"><option></option>
										<option <?= $event_monthly_start == 'Sunday' ? 'selected' : '' ?> value="Sunday">Sunday</option>
										<option <?= $event_monthly_start == 'Monday' ? 'selected' : '' ?> value="Monday">Monday</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Monthly View Days Included:</label>
								<div class="col-sm-8">
									<?php $event_monthly_days = explode(',',get_config($dbc, 'event_monthly_days')); ?>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Sunday',$event_monthly_days) ? 'checked' : '' ?> value="Sunday"> Sunday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Monday',$event_monthly_days) ? 'checked' : '' ?> value="Monday"> Monday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Tuesday',$event_monthly_days) ? 'checked' : '' ?> value="Tuesday"> Tuesday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Wednesday',$event_monthly_days) ? 'checked' : '' ?> value="Wednesday"> Wednesday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Thursday',$event_monthly_days) ? 'checked' : '' ?> value="Thursday"> Thursday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Friday',$event_monthly_days) ? 'checked' : '' ?> value="Friday"> Friday</label>
									<label class="form-checkbox"><input type="checkbox" name="event_monthly_days[]" <?= in_array('Saturday',$event_monthly_days) ? 'checked' : '' ?> value="Saturday"> Saturday</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar Use Offline Editing Mode:</label>
								<div class="col-sm-8">
									<?php $event_offline = get_config($dbc, 'event_offline'); ?>
									<label class="form-checkbox"><input type="checkbox" name="event_offline" <?= $event_offline != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Events Calendar <?= TICKET_NOUN ?> Summary:</label>
								<div class="col-sm-8">
									<?php $event_ticket_summary = get_config($dbc, 'event_ticket_summary'); ?>
									<label class="form-checkbox"><input type="checkbox" name="event_ticket_summary" <?= $event_ticket_summary != '' ? 'checked' : '' ?> value="1"></label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >
								Staff Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_staff" class="panel-collapse collapse">
						<div class="panel-body">

							<?php
								echo "<table class='table table-bordered'>
									<tr class='hidden-xs hidden-sm'>
										<th>Staff Name</th>
										<th>Calendar Colour</th>
										<th>Default Calendar</th>
										<th>Show On Appointment Calendar</th>
									</tr>";
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status = 1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY),MYSQLI_ASSOC));
								$i=1;
								foreach($query as $staff_row) {
									$row = mysqli_fetch_array(mysqli_query($dbc,"SELECT `contacts`.`contactid`, `first_name`, `last_name`, IF(IFNULL(`calendar_color`,'')='','#C8C8C8',`calendar_color`) color, IFNULL(`calendar_enabled`,1) enabled, IFNULL(`calendar_view`,'default') view FROM `contacts` LEFT JOIN `user_settings` ON `contacts`.`contactid`=`user_settings`.`contactid` WHERE `contacts`.`contactid`='$staff_row'"));
									echo '<tr>
									<td data-title="Staff Name">';
									echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']);
									echo '<input type="hidden" name="contactid[]" value="'.$row['contactid'].'">
									</td>
									<td data-title="Calendar Colour">';
									echo '<input class="form-control" type="color" name="calendar_color[]" value="'.$row['color'].'">';
									echo '</td>
									<td data-title="Default Calendar View">
									<select name="default_calendar[]" class="chosen-select-deselect"><option></option>
										<option '.($row['view'] == 'default' ? 'selected' : '').' value="default">Software Default</option>
										<option '.($row['view'] == 'my_day' ? 'selected' : '').' value="uni_day">My Calendar: Day</option>
										<option '.($row['view'] == 'my_wk' ? 'selected' : '').' value="uni_wk">My Calendar: Week</option>
										<option '.($row['view'] == 'my_mon' ? 'selected' : '').' value="uni_wk">My Calendar: Month</option>
										<option '.($row['view'] == 'uni_day' ? 'selected' : '').' value="uni_day">Universal: Day</option>
										<option '.($row['view'] == 'uni_wk' ? 'selected' : '').' value="uni_wk">Universal: Week</option>
										<option '.($row['view'] == 'uni_mon' ? 'selected' : '').' value="uni_wk">Universal: Month</option>
										<option '.($row['view'] == 'appt_day' ? 'selected' : '').' value="appt_day">Appointment: Day</option>
										<option '.($row['view'] == 'appt_wk' ? 'selected' : '').' value="appt_wk">Appointment: Week</option>
										<option '.($row['view'] == 'appt_mon' ? 'selected' : '').' value="appt_wk">Appointment: Month</option>
										<option '.($row['view'] == 'staff_day' ? 'selected' : '').' value="staff_day">Staff Schedule: Day</option>
										<option '.($row['view'] == 'staff_wk' ? 'selected' : '').' value="staff_wk">Staff Schedule: Week</option>
										<option '.($row['view'] == 'staff_mon' ? 'selected' : '').' value="staff_wk">Staff Schedule: Month</option>
										<option '.($row['view'] == 'sched_day' ? 'selected' : '').' value="sched_day">Dispatch: Day</option>
										<option '.($row['view'] == 'sched_wk' ? 'selected' : '').' value="sched_wk">Dispatch: Week</option>
										<option '.($row['view'] == 'sched_mon' ? 'selected' : '').' value="sched_wk">Dispatch: Month</option>
										<option '.($row['view'] == 'ticket_day' ? 'selected' : '').' value="ticket_day">'.TICKET_NOUN.': Day</option>
										<option '.($row['view'] == 'ticket_wk' ? 'selected' : '').' value="ticket_wk">'.TICKET_NOUN.': Week</option>
										<option '.($row['view'] == 'ticket_mon' ? 'selected' : '').' value="ticket_mon">'.TICKET_NOUN.': Month</option>
										<option '.($row['view'] == 'shift_day' ? 'selected' : '').' value="shift_mon">Shift: Day</option>
										<option '.($row['view'] == 'shift_wk' ? 'selected' : '').' value="shift_mon">Shift: Week</option>
										<option '.($row['view'] == 'shift_mon' ? 'selected' : '').' value="shift_mon">Shift: Month</option>
										<option '.($row['view'] == 'event_day' ? 'selected' : '').' value="event_mon">Event: Day</option>
										<option '.($row['view'] == 'event_wk' ? 'selected' : '').' value="event_mon">Event: Week</option>
										<option '.($row['view'] == 'event_mon' ? 'selected' : '').' value="event_mon">Event: Month</option>';
									echo '</select></td>
									<td data-title="Show On Appointment Calendar">';
									echo '<label class="form-checkbox"><input type="checkbox" name="calendar_enabled[]" value="'.$row['contactid'].'" '.($row['enabled'] ? 'checked' : '').'> Show</label>';
									echo '</td>';
									echo '</tr>';
									$i++;
								}
								echo '</table>';
							?>

						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_holidays" >
								Holiday Settings<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_holidays" class="panel-collapse collapse">
						<div class="panel-body">
							<script>
							$(document).ready(function() {
								$('#collapse_holidays .toggle-switch').click(function() {
									$(this).find('img').toggle();
									$(this).find('input').val($(this).find('input').val() == 1 ? 0 : 1);
								});
							});
							$(document).on('change.select2', 'select[name="defined_holidays"]', function() { use_defined_holiday(this); });
							function use_defined_holiday(select) {
								if(select.value == 'CUSTOM') {
									$(select).closest('div').find('.select2').hide();
									$(select).closest('div').find('input').show().focus();
								} else {
									var row = $(select).closest('.form-group');
									var choice = $(select).find('option:selected');
									$.ajax({
										url: '../ajax_dates.php?action=next_occurrence',
										method: 'POST',
										data: { day: choice.data('day'), month: choice.data('month'), week: choice.data('week'), weekday: choice.data('weekday'), name: choice.val() },
										success: function(response) {
											row.find('[name="holiday_date[]"]').val(response);
										}
									});
									row.find('[name="holiday_name[]"]').val(choice.val());
									if(choice.data('paid') != row.find('[name="holiday_paid[]"]').val()) {
										row.find('.toggle-switch img').toggle();
										row.find('.toggle-switch input').val(choice.data('paid'));
									}
								}
							}
							</script>
							<div class="form-group hide-titles-mob text-center">
								<label class="col-sm-5">Holiday Name</label>
								<label class="col-sm-3">Statutory Date</label>
								<label class="col-sm-2">Paid</label>
							</div>
							<?php $defined_holidays = [];
							include('defined_holidays.php');
							$holiday_list = mysqli_query($dbc, "SELECT * FROM (SELECT `holidays_id`, `name`, `date`, `paid` FROM `holidays` WHERE `deleted`=0 UNION SELECT 'NEW', '', '', 1) holidays ORDER BY `date`='' DESC, `date` DESC");
							while($holiday = mysqli_fetch_array($holiday_list)) { ?>
								<div class="form-group">
									<input type="hidden" name="holiday_id[]" value="<?= $holiday['holidays_id'] ?>">
									<div class="col-sm-5 col-xs-12">
										<label class="show-on-mob">Holiday Name:</label>
										<?php if($holiday['name'] == '' && $holiday['date'] == '') { ?>
											<select class="chosen-select-deselect" name="defined_holidays"><option></option>
												<option value="CUSTOM">Custom Holiday</option>
												<?php foreach($defined_holidays as $defined) { ?>
													<option data-day="<?= $defined['day'] ?>" data-week="<?= $defined['week'] ?>" data-weekday="<?=$defined['weekday'] ?>" data-month="<?= $defined['month'] ?>" data-paid="<?= $defined['paid'] ?>" value="<?= $defined['name'] ?>"><?= $defined['label'] ?></option>
												<?php } ?>
											</select>
											<input type="text" name="holiday_name[]" value="<?= $holiday['name'] ?>" class="form-control" style="display:none;">
										<?php } else { ?>
											<input type="text" name="holiday_name[]" value="<?= $holiday['name'] ?>" class="form-control">
										<?php } ?>
									</div>
									<div class="col-sm-3 col-xs-12">
										<label class="show-on-mob">Statutory Date:</label>
										<input type="text" name="holiday_date[]" value="<?= $holiday['date'] ?>" class="datepicker form-control">
									</div>
									<div class="col-sm-2 col-xs-12">
										<label class="show-on-mob">Paid:</label>
										<div class="toggle-switch form-group"><input type="hidden" name="holiday_paid[]" value="<?= $holiday['paid'] ?>">
											<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $holiday['paid'] > 0 ? 'display: none;' : '' ?>">
											<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $holiday['paid'] > 0 ? '' : 'display: none;' ?>"> Holiday is Paid
										</div>
									</div>
									<div class="col-sm-1 col-xs-12">
										<input type="hidden" name="holiday_archived[]" value="<?= $holiday['deleted'] ?>">
										<button class="btn brand-btn" onclick="$(this).closest('div').find('[name^=holiday_archived]').val(1); $(this).closest('.form-group').hide(); return false;">Archive</button>
									</div>
								</div>
							<?php } ?>

							<!--<div class="form-group">
								<div class="col-sm-4" style="text-align:center;">Holiday</div>
								<div class="col-sm-3" style="text-align:center;">Day of Month</div>
								<div class="col-sm-3" style="text-align:center;">Month</div>
								<div class="col-sm-1" style="text-align:center;">Paid</div>
								<div class="col-sm-1" style="text-align:center;"></div>
							</div>
							<?php $result = mysqli_query($dbc, "SELECT * FROM (SELECT `holiday_id`, `name`, `holiday_day`, `holiday_month`, `paid` FROM field_config_holidays ORDER BY `holiday_month`, `holiday_day`) holidays UNION SELECT '', '', '', '', false");
							while($row = mysqli_fetch_array($result)):
								$day_of_month = explode(',',$row['holiday_day']);
								$day_num = $day_of_month[0];
								$day_week = $day_of_month[1];
								$defined = array_search(['name'=>decryptIt($row['name']),'day'=>$row['holiday_day'],'month'=>$row['holiday_month'],'paid'=>$row['paid']],$defined_holidays);
								if($defined !== false) {
									unset($defined_holidays[$defined]);
								} ?>
								<div class="form-group">
									<input type="hidden" name="holiday_id[]" value="<?php echo $row['holiday_id']; ?>">
									<div class="col-sm-4"><input type="text" name="holiday_name[]" value="<?php echo decryptIt($row['name']); ?>" class="form-control"></div>
									<div class="col-sm-1"><input type="number" name="holiday_day[]" value="<?php echo $day_num; ?>" class="form-control"></div>
									<div class="col-sm-2"><select name="holiday_week[]" class="form-control chosen-select-deselect"><option></option>
										<?php $date = date('d', strtotime('Last Sunday'));
										for($i = $date; $i < $date + 7; $i++) {
											$dateObj = DateTime::createFromFormat('d', $i);
											echo "<option ".($dateObj->format('l') == $day_week ? 'selected ' : '')."value='".$dateObj->format('l')."'>".$dateObj->format('l')."</option>";
										} ?>
										</select></div>
									<div class="col-sm-3"><select name="holiday_month[]" class="form-control chosen-select-deselect"><option></option>
										<?php for($i = 1; $i <= 12; $i++) {
											$dateObj = DateTime::createFromFormat('!m', $i);
											echo "<option ".($i == $row['holiday_month'] ? 'selected ' : '')."value='$i'>".$dateObj->format('F')."</option>";
										} ?>
										</select></div>
									<div class="col-sm-1"><input type="checkbox" <?php echo ($row['paid'] == '1' ? 'checked' : ''); ?> name="holiday_paid[]" value="1" class="form-control"></div>
									<div class="col-sm-1"><button class="btn brand-btn" onclick="remove_holiday(this); return false;">Remove</button></div>
								</div>
							<?php endwhile;
							foreach($defined_holidays as $holiday):
								$day_of_month = explode(',',$holiday['day']);
								$day_num = $day_of_month[0];
								$day_week = $day_of_month[1]; ?>
								<div class="form-group">
									<input type="hidden" disabled name="holiday_id[]" value="">
									<div class="col-sm-4"><input type="text" disabled name="holiday_name[]" value="<?php echo $holiday['name']; ?>" class="form-control"></div>
									<div class="col-sm-1"><input type="number" disabled name="holiday_day[]" value="<?php echo $day_num; ?>" class="form-control"></div>
									<div class="col-sm-2"><select disabled name="holiday_week[]" class="form-control"><option></option>
										<?php $date = date('d', strtotime('Last Sunday'));
										for($i = $date; $i < $date + 7; $i++) {
											$dateObj = DateTime::createFromFormat('d', $i);
											echo "<option ".($dateObj->format('l') == $day_week ? 'selected ' : '')."value='".$dateObj->format('l')."'>".$dateObj->format('l')."</option>";
										} ?>
										</select></div>
									<div class="col-sm-3"><select disabled name="holiday_month[]" class="form-control"><option></option>
										<?php for($i = 1; $i <= 12; $i++) {
											$dateObj = DateTime::createFromFormat('!m', $i);
											echo "<option ".($i == $holiday['month'] ? 'selected ' : '')."value='$i'>".$dateObj->format('F')."</option>";
										} ?>
										</select></div>
									<div class="col-sm-1"><input disabled type="checkbox" checked name="holiday_paid[]" value="<?php echo $holiday['paid']; ?>" class="form-control"></div>
									<div class="col-sm-1"><button class="btn brand-btn" value="Enable" onclick="switch_enable(this); return false;">Enable</button></div>
								</div>
							<?php endforeach; ?>
							<script>
							function add_holiday(button) {
								clone = $('#collapse_holidays div.form-group:contains("Remove"):last').clone();
								clone.find('input').val('').attr('checked',false);
								clone.find('option:selected').removeAttr('selected');
								clone.find('.chosen-select-deselect').removeClass("chzn-done").css("display", "block").next().remove();
								clone.find('.chosen-select-deselect').chosen({allow_single_deselect:true});
								$(button).before(clone);
							}
							function remove_holiday(button) {
								$(button).closest('.form-group').remove();
							}
							function switch_enable(button) {
								if(button.value == 'Enable') {
									$(button).closest('.form-group').find(':disabled').prop('disabled', false);
									button.value = 'Disable';
								} else {
									$(button).closest('.form-group').find(':enabled').prop('disabled', true);
									button.value = 'Enable';
								}
							}
							</script>
							<button class="btn brand-btn pull-right" onclick="add_holiday(this); return false;">Add Holiday</button>-->
						</div>
					</div>
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-6">
					<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
				</div>
			</div>

			<?php } ?>

			<?php if($_GET['type'] == 'appointments') {
				include('field_config_calendar_booking.php');
			} ?>

			<?php if($_GET['type'] == 'tickets') {
				include('field_config_tickets.php');
			} ?>

			<?php if($_GET['type'] == 'shifts') {
				include('field_config_shifts.php');
			} ?>

			<?php if($_GET['type'] == 'teams') {
				include('field_config_teams.php');
			} ?>

			<?php if($_GET['type'] == 'equip_assign') {
				include('field_config_equip_assign.php');
			} ?>

			<?php if($_GET['type'] == 'workorder') {
				include('field_config_workorder.php');
			} ?>

			<?php if($_GET['type'] == 'unbooked') {
				include('field_config_unbooked.php');
			} ?>

			<?php if($_GET['type'] == 'security') {
				include('field_config_security.php');
			} ?>

		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>