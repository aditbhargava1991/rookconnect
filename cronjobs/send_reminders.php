<?php $guest_access = true;
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'function.php');
include(substr(dirname(__FILE__), 0, -8).'email.php');
ob_clean();

if(isset($_GET['reminderid'])) {
	$query = "`reminderid`={$_GET['reminderid']}";
}
else {
	$query = "`reminder_date`=DATE(NOW()) AND `reminder_time` < NOW()";
}

// Send Reminders from Reminders table
$sql = "SELECT * FROM `reminders` WHERE $query AND (`reminder_type` NOT LIKE 'PROJECT%' AND `reminder_type` NOT LIKE 'CLIENTPROJECT%' AND `reminder_type` NOT IN ('QUICK','STAFF')) AND `deleted`=0 AND `sent`=0";
$results = mysqli_query($dbc, $sql);

while($row = mysqli_fetch_array($results)) {
	$email = $row['sender'];
	$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."' OR `office_email` = '".encryptIt($email)."'");
	if($email != '' && $email_name_id = mysqli_fetch_array($email_name_result)) {
		$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
	} else {
		$from = [$email => $email];
	}
	$verify = true;
	if($row['verify'] != '') {
		$verification = explode('#*#', $row['verify']);
		$table = $verification[0];
		$field = $verification[1];
		$rowfieldid = $verification[2];
		$rowid = $verification[3];
		$target = $verification[4];
		$comparison = $verification[5];
		$value = mysqli_fetch_array(mysqli_query($dbc, "SELECT `$field` FROM `$table` WHERE `$rowfieldid`='$rowid'"),MYSQLI_NUM);
		if(($comparsion == '' && $value[0] == $target) || ($comparison == 'NOT' && $value[0] != $target) || ($comparison == 'GREATER' && $value[0] > $target) || ($comparison == 'LESS' && $value[0] < $target)) {
			$sql = "UPDATE `reminders` SET `sent`=1 WHERE `reminderid`='".$row['reminderid']."'";
			$results = mysqli_query($dbc, $sql);
			$verify = false;
			echo $row['subject']." verified as complete, no email needed. (".$row['reminder_type']." Reminder: #".$row['reminderid'].")\n";
		}
	}

	if($verify) {
		$contacts = explode(',',$row['contactid']);
		$addresses = [];
		foreach($contacts as $contactid) {
			$address[] = get_email($dbc, $contactid);
		}
		$recipients = explode(',',$row['recipients']);
		foreach($recipients as $i => $recipient) {
			if(substr($recipient,0,6) == 'LEVEL:') {
				unset($recipients[$i]);
				$recipient = substr($recipient,6);
				foreach(sort_contacts_query($dbc->query("SELECT `contactid`,`email_address` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND CONCAT(',',`role`,',') LIKE '%,$recipient,%' AND '$recipient' != ''")) as $contact) {
					if(!empty(get_email($dbc, $contact['contactid']))) {
						$recipients[] = get_email($dbc, $contact['contactid']);
					}
				}
			}
		}
		$addresses = array_merge($addresses, $recipients);
		foreach($addresses as $email) {
			if($email != '') {
				$time = date('Y-m-d h:i:s');
				$title = $row['subject'];

				try {
					send_email($from, $email, '', '', $row['subject'], html_entity_decode($row['body']), '');
					echo $row['subject']." sent to $email at $time. (".$row['reminder_type']." Reminder: #".$row['reminderid'].")\n";
					$sql = "UPDATE `reminders` SET `sent`=1 WHERE `reminderid`='".$row['reminderid']."'";
					$results = mysqli_query($dbc, $sql);
				} catch (Exception $e) {
					echo "Unable to send email: $title to $email\n";
				}
			}
		}
	}
}

//Send Expense Reminders, if configured
$reminder_days = get_config($dbc, 'expense_reminder_days');
if($reminder_days > 0) {
	$date = new DateTime(date('Y-m-t'));
	$date->sub(new DateInterval('P'.$reminder_days.'D'));
	if($date->format('Y-m-d') == date('Y-m-d')) {
		$previous_reminder = get_config($dbc, 'last_expense_reminder');
		$last_expense_reminder = date('Y-m');
		if($last_expense_reminder != $previous_reminder) {
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='last_expense_reminder'"));
			if($get_config['configid'] > 0) {
				$result_update_config = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$last_expense_reminder' WHERE name='last_expense_reminder'");
			} else {
				$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('last_expense_reminder', '$last_expense_reminder')");
			}
			$recipients = mysqli_query($dbc, "SELECT `contactid`, `email_address`, `role` FROM `contacts` WHERE `user_name` != '' AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0");
			while($row = mysqli_fetch_array($recipients)) {
				if(tile_visible($dbc, 'expense', $row['role'])) {
					$recipient = get_email($dbc, $row['contactid']);
					if(!empty($recipient)) {
						$sender = get_config($dbc, 'expense_reminder_sender');
						$subject = get_config($dbc, 'expense_reminder_subject');
						$body = get_config($dbc, 'expense_reminder_body');
						try {
							send_email($sender, $recipient, '', '', $subject, html_entity_decode($body), '');
							echo $subject." sent to $recipient. (Expense Reminder)\n";
						} catch (Exception $e) {
							echo "Unable to send Expense Reminder email.\n";
						}
					}
				}
			}
		}
	}
}

//Send Invoice Reminders, if configured, from Projects
$reminder_projects = mysqli_query($dbc, "SELECT * FROM `project` WHERE `invoice_freq` != '' AND `deleted`=0 AND `status` NOT IN ('Archive','Pending')");
while($project = mysqli_fetch_assoc($reminder_projects)) {
	$send_day = '';
	$start = $project['invoice_start_date'];
	switch($project['invoice_freq']) {
		case 'weekly':
			$start_day_of_week = date('l',strtotime($start));
			$today_day_of_week = date('l');
			if($start_day_of_week == $today_day_of_week) {
				$send_day = date('Y-m-d');
			}
			break;
		case 'bi-weekly':
			$start_date = new DateTime($start);
			$send_day = new DateTime();
			$days_to_period = $send_day->diff($start_date)->days % 14;
			$send_day->modify('-'.$days_to_period.' days');
			$send_day->add(new DateInterval('P13D'));
			$send_day = $send_day->format('Y-m-d');
			break;
		case 'semi-month':
			$start_day_of_month = date('j',strtotime($start));
			$today_day_of_month = date('j');
			$length_of_month = date('t');
			$half_of_month = floor($length_of_month / 2);
			if($today_day_of_month >= $start_day_of_month && $today_day_of_month < ($start_day_of_month + $half_of_month)) {
				$send_day = 'Y-m-'.$start_day_of_month;
			} else if(($start_day_of_month + $half_of_month) > $length_of_month && $today_day_of_month < $start_day_of_month - $half_of_month) {
				$month = date('n') - 1;
				$year = date('Y');
				if($month < 1) {
					$year--;
					$month = 12;
				}
				$send_day = $year.'-'.$month.'-'.$start_day_of_month;
			} else {
				$start_day_of_month += $half_of_month;
				$month = date('n');
				$year = date('Y');
				if($start_day_of_month > $length_of_month) {
					$start_day_of_month -= $length_of_month;
				}
				$send_day = $year.'-'.$month.'-'.$start_day_of_month;
			}
			break;
		case 'annual':
			$send_day = new DateTime($start);
			$todays_date = new DateTime();
			if($send_day->format('j') < $todays_date->format('j')) {
				$send_day = new DateTime(date('Y-'.$send_day->format('m-d')));
			} else {
				$send_day = new DateTime(date('Y-'.$send_day->format('m-d')));
				$send_day->sub(new DateInterval('P1M'));
			}
			$send_day = $send_day->format('Y-m-d');
		case 'monthly':
		default:
			$send_day = new DateTime($start);
			$todays_date = new DateTime();
			if($send_day->format('j') < $todays_date->format('j')) {
				$send_day = new DateTime(date('Y-m-'.$send_day->format('d')));
			} else {
				$send_day = new DateTime(date('Y-m-'.$send_day->format('d')));
				$send_day->sub(new DateInterval('P1M'));
			}
			$send_day = $send_day->format('Y-m-d');
			break;
	}
	if($send_day == date('Y-m-d')) {
		try {
			send_email([$project['invoice_email']=>$project['invoice_sender']], [$project['invoice_recip_address']=>$project['invoice_recip_name']], '', '', $project['invoice_subject'], html_entity_decode($project['invoice_body']), '');
			echo $project['invoice_subject']." sent to $recipient. (Invoice Reminder)\n";
		} catch (Exception $e) {
			echo "Unable to send Invoice Reminder email.\n";
		}
	}
}

//Check usage and send limit notification emails
include('../usage_update.php');
?>
