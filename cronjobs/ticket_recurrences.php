<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();

$sync_upto = !empty(get_config($dbc, 'ticket_recurrence_sync_upto')) ? get_config($dbc, 'ticket_recurrence_sync_upto') : '2 years';
$today_date = date('Y-m-d', strtotime(date('Y-m-d').' + '.$sync_upto));
$ticket_recurrences = mysqli_query($dbc, "SELECT * FROM `ticket_recurrences` WHERE `deleted` = 0 AND IFNULL(NULLIF(NULLIF(`end_date`,'1969-12-31'),'0000-00-00'),'') = '' AND `last_added_date` < '$today_date'");

while($row = mysqli_fetch_assoc($ticket_recurrences)) {
	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `main_ticketid` = '".$row['ticketid']."' AND `deleted` = 0 AND `is_recurrence` = 1"));
	if(!empty($ticket)) {
		create_recurring_tickets($dbc, $ticket['main_ticketid'], date('Y-m-d', strtotime($row['last_added_date'].' + 1 day')), '', $row['repeat_type'], $row['repeat_interval'], array_filter(explode(',', $row['repeat_days'])), $row['repeat_monthly']);
		sync_recurring_tickets($dbc, $ticket['ticketid']);
	}
}