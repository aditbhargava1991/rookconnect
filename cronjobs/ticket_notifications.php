<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
error_reporting(0);

$today_date = date('Y-m-d');
$noti_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_notifications` WHERE `send_date` = '$today_date' AND `deleted` = 0 AND `status` = 'Pending'"),MYSQLI_ASSOC);

foreach ($noti_list as $noti) {
	$ticketnotificationid = $noti['ticketnotificationid'];
	$staffid = $noti['staffid'];
	$contactid = $noti['contactid'];
	$sender_name = $noti['sender_name'];
	$sender_email = $noti['sender_email'];
	$subject = $noti['subject'];
	$body = html_entity_decode($noti['email_body']);
	$log = '';

	$recipients = array_merge(explode(',', $staffid), explode(',', $contactid));
	foreach ($recipients as $address) {
		$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
		try {
			send_email([$sender_email=>$sender_name], $address, '', '', $subject, $body, '');
		} catch(Exception $e) { $log .= "Unable to send e-mail to ".get_contact($dbc, $address).": ".$e->getMessage()."\n"; }
	}
	$log .= "Notification Sent.";
	mysqli_query($dbc, "UPDATE `ticket_notifications` SET `log` = '".$noti['log']."\n$log', `status` = 'Sent', `send_date` = '$today_date' WHERE `ticketnotificationid` = '$ticketnotificationid'");
}
