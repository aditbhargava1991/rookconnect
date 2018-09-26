<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
// error_reporting(E_ALL);

$holiday_update_noti = get_config($dbc, 'holiday_update_noti');
if($holiday_update_noti == 1) {
	$holiday_update_date = get_config($dbc, 'holiday_update_date');
	$holiday_update_stopdate = get_config($dbc, 'holiday_update_stopdate');
	$last_sent_date = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `holiday_update_reminders` ORDER BY `date` DESC"))['date'];
	$today_date = date('Y-m-d');
	$today_day_of_week = date('l');
	$update_day_of_week = date('l', strtotime(date('Y').'-'.$holiday_update_date));
	if(strtotime($today_date) >= strtotime(date('Y').'-'.$holiday_update_date) && $today_day_of_week == $update_day_of_week && strtotime($today_date) > strtotime($last_sent_date) && strtotime($today_date) > strtotime($holiday_update_stopdate)) {
		$holiday_update_staff = get_config($dbc, 'holiday_update_staff');

		$subject = "Notification that Statutory Holidays need updating";
		$body = '<p>This is a notification that Statutory Holidays need updating. You will be sent notifications weekly until turned off from the software. The next notification will be sent on '.date('Y-m-d', strtotime($today_date.' + 1 week')).'. To update holidays and turn off these notifications, click <a href="'.WEBSITE_URL.'/Timesheet/holidays.php">here</a>.</p>';

		mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `body`, `src_table`) VALUES ('$holiday_update_staff', '$today_date', 'Statutory Holiday Update', '$subject', '".htmlentities($body)."', 'holidays_update')");
		mysqli_query($dbc, "INSERT INTO `holiday_update_reminders` (`date`, `sent`) VALUES ('$today_date', 1)");
		$email = get_email($dbc, $holiday_update_staff);
		try {
			send_email('', $email, '', '', $subject, $body, '');
		} catch(Exception $e) {
			$log = "Unable to send e-mail to ".get_contact($dbc, $holiday_update_staff).": ".$e->getMessage()."\n";
			mysqli_query($dbc, "UPDATE `holiday_update_reminders` SET `log` = CONCAT(`log`, '$log') WHERE `date` = '$today_date'");
		}
	}
}
