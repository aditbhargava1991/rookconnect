<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
error_reporting(0);

$today_date = date('Y-m-d');
$noti_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `incident_report_reminders` WHERE `second_reminder_date` = '$today_date' AND `deleted` = 0 AND `done` = 0"),MYSQLI_ASSOC);

foreach ($noti_list as $noti) {
	$reminderid = $noti['reminderid'];
	$staffid = $noti['staffid'];
	$subject = $noti['subject'];
	$body = html_entity_decode($noti['body']);
	$sender_name = $noti['sender_name'];
	$sender_email = $noti['sender_email'];
	$second_reminder_email = $noti['second_reminder_email'];

	$log = '';
	$address = get_email($dbc, filter_var($staffid,FILTER_SANITIZE_STRING));
	try {
		send_email([$sender_email=>$sender_name], $address, '', '', $subject, $body, '');
	} catch(Exception $e) { $log .= "Unable to send e-mail to ".get_contact($dbc, $staffid).": ".$e->getMessage()."\n"; }

	$body .= '<br><br>This is the second reminder sent for '.get_contact($dbc, $staffid).' with you attached as the second reminder email.';
	if(!empty($second_reminder_email)) {
		foreach (explode(',', $second_reminder_email) as $address) {
			try {
				send_email([$sender_email=>$sender_name], $address, '', '', $subject, $body, '');
				$log = '';
			} catch(Exception $e) { $log .= "Unable to send e-mail to ".$address.": ".$e->getMessage()."\n"; }
		}
	}
	mysqli_query($dbc, "UPDATE `incident_report_reminders` SET `done` = 1, `log` = CONCAT(`log`,'$log') WHERE `reminderid` = '$reminderid'");
}
