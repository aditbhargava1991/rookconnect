<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
include(substr(dirname(__FILE__), 0, -8).'/Profile/daysheet_functions.php');
ob_clean();

$daysheet_styling = '';
$daysheet_ticket_fields = explode(',',get_config($dbc, 'daysheet_ticket_fields'));

$row_open = '<div style="background-color: #ddd; margin: 0.5em;"><div style="color: black; text-decoration: none; display: block; padding: 0.5em;">';
$row_close = '</div></div>';

$current_hour = date('G');
$current_day = date('l');

$default_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_email_alerts` WHERE `software_default` = 1"));

$staff_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE (`office_email` != '' OR `email_address` != '') AND `user_name` != '' AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0"),MYSQLI_ASSOC);
foreach($staff_list as $staff) {
	$staff_id = $staff['contactid'];
	if($staff_id > 0) {
		$noti_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_email_alerts` WHERE `contactid` = '$staff_id'"));
		if(empty($noti_config)) {
			$noti_config = $default_config;
		}
		if($noti_config['enabled'] == 1 && (($noti_config['frequency'] == 'daily' && $noti_config['alert_hour'] == $current_hour) || ($noti_config['frequency'] == 'weekly' && $noti_config['alert_hour'] == $current_hour && strpos(','.$noti_config['alert_days'].',', ','.$current_day.',') !== FALSE) || $noti_config['frequency'] == 'hourly')) {
			$search_user = $staff_id;
			include(substr(dirname(__FILE__), 0, -8).'/Notification/get_notifications.php');
			$enabled_alerts = [];
			foreach(explode(',', $noti_config['alerts']) as $alert) {
				$alert = explode('#*#', $alert);
				foreach(array_filter(explode(',', $alert[1])) as $alert_field) {
					$enabled_alerts[$alert[0]][] = $alert_field;
				}
			}

			$reminder_query = ' AND 1=0';
			$daysheet_reminder_query = ' AND 1=0';
			$journal_notification_query = ' AND 1=0';

			if(!empty($enabled_alerts['reminder'])) {
				$enabled_alerts['daysheet_reminder'][] = 'reminder';
				$reminder_query = " AND `src_table` IN ('".implode("','", $enabled_alerts['reminder'])."')";
			}
			if(!empty($enabled_alerts['daysheet_reminder'])) {
				$enabled_alerts['journal_notification'][] = 'daysheet_reminders';
				$daysheet_reminder_query = " AND `type` IN ('".implode("','", $enabled_alerts['daysheet_reminder'])."')";
			}
			if(!empty($enabled_alerts['journal_notification'])) {
				$journal_notification_query = " AND `src_table` IN ('".implode("','", $enabled_alerts['journal_notification'])."')";
			}

			$noti_list = mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `contactid` = '$staff_id' AND `deleted` = 0 AND `email_sent` = 0".$journal_notification_query." ORDER BY `id` DESC");
			$row_limit = 25;
			$email_alert = 1;

			include(substr(dirname(__FILE__), 0, -8).'/Profile/daysheet_notifications_inc.php');

			if($row_i > 0) {
				$noti_html = preg_replace("/<img[^>]+\>/i", "", $noti_html);
				$noti_html = preg_replace('/on[A-Za-z]*?=".*?"/', "", $noti_html);
				$noti_html = str_replace('<a ', '<a style="color: black; text-decoration: none;" ', $noti_html);

				$subject = "You have Reminder Alerts!";
				$body = "You have Reminder Alerts. The following are your latest Reminder Alerts. Please click <a href='".WEBSITE_URL."/Daysheet/daysheet.php?side_content=notifications'><b>HERE</b></a> to see all of your notifications.<br>".$noti_html;

				$email = get_email($dbc, $staff_id);
				try {
					send_email('', $email, '', '', $subject, $body, '');
				} catch (Exception $e) {

				}
				mysqli_query($dbc, "UPDATE `journal_notifications` SET `email_sent` = 1 WHERE `contactid` = '$staff_id'");
			}
		}
	}
}