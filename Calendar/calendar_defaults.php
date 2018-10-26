<?php
	//Default Calendar Type
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'calendar_default','my_wk' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='calendar_default') num WHERE num.rows=0");

	//Calendar Types
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'calendar_types','My Calendar,Universal Calendar,Appointment Calendar,Staff Schedule Calendar,Dispatch Calendar,Sales Estimates Calendar,Ticket Calendar,Shift Calendar,Events Calendar' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='calendar_types') num WHERE num.rows=0");

	//Unbooked List Filters
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'unbooked_ticket_filters','project,customer,staff' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='unbooked_ticket_filters') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'unbooked_appt_filters','patient,injurytype,appttype' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='unbooked_appt_filters') num WHERE num.rows=0");

	//My Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_wait_list','ticket,appt' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_use_unbooked','' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'my_use_shift_tickets','' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='my_use_shift_tickets') num WHERE num.rows=0");

	//Universal Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_wait_list','ticket,appt' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_use_unbooked','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'uni_use_shift_tickets','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='uni_use_shift_tickets') num WHERE num.rows=0");

	//Appointment Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_wait_list','appt' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_use_unbooked','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'appt_use_shift_tickets','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='appt_use_shift_tickets') num WHERE num.rows=0");

	//Staff Schedule Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_wait_list','appt' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_use_unbooked','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'staff_schedule_use_shift_tickets','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_use_shift_tickets') num WHERE num.rows=0");

	//Dispatch Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_wait_list','workorder' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shceduling_use_unbooked','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shceduling_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shceduling_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shceduling_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_equip_assign','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_equip_assign') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_use_shift_tickets','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_new_ticket_button','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_new_ticket_button') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_filters','Region,Location,Classification' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_filters') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'scheduling_item_filters','Region' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='scheduling_item_filters') num WHERE num.rows=0");

	//Sales Estimates Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'estimates_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimates_monthly_days') num WHERE num.rows=0");

	//Ticket Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_wait_list','ticket' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_wait_list') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_use_unbooked','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_unbooked') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_use_shifts','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_shifts') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_monthly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_use_shift_tickets','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_use_shift_tickets') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'ticket_sidebar_filters','Region' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='ticket_sidebar_filters') num WHERE num.rows=0");

	//Shift Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'shift_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='shift_monthly_days') num WHERE num.rows=0");

	//Events Calendar
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_day_start','06:00 am' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_day_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_day_end','08:00 pm' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_day_end') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_increments','15' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_increments') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_calendar_notes','1' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_calendar_notes') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_weekly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_weekly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_weekly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_weekly_days') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_monthly_start','Sunday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_monthly_start') num WHERE num.rows=0");
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'event_monthly_days','Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='event_monthly_days') num WHERE num.rows=0");
?>