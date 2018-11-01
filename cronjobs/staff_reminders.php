<?php $guest_access = true;
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'function.php');
include(substr(dirname(__FILE__), 0, -8).'phpmailer.php');
ob_clean();

if(isset($_GET['reminderid'])) {
	$query = "`reminderid`={$_GET['reminderid']}";
}
else {
	$query = "`reminder_date`=DATE(NOW()) AND `reminder_time` < NOW()";
}

$sql = "SELECT * FROM `reminders` WHERE $query AND `reminder_type`='STAFF' AND `deleted`=0 AND `sent`=0";
$results = mysqli_query($dbc, $sql);

while($row = mysqli_fetch_array($results)) {
	$email = $row['sender'];
	if($row['sender_name'] != '') {
		$from = [$row['sender_name'] => $email];
	} else {
		$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."' OR `office_email` = '".encryptIt($email)."'");
		if($email != '' && $email_name_id = mysqli_fetch_array($email_name_result)) {
			$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
		} else {
			$from = [$email => $email];
		}
	}

	$contacts = explode(',',$row['contactid']);
	foreach($contacts as $contactid) {
		$email = get_email($dbc, $contactid);
		if($email != '') {
			$time = date('Y-m-d h:i:s');
			$title = $row['subject'];

			try {
				send_email($from, $email, '', '', $row['subject'], html_entity_decode($row['body']), '');
				echo $row['subject']." sent to $email at $time. (Staff Reminder: #".$row['reminderid'].")\n";
				$sql = "UPDATE `reminders` SET `sent`=1 WHERE `reminderid`='".$row['reminderid']."'";
				$results = mysqli_query($dbc, $sql);
			} catch (Exception $e) {
				echo "Unable to send email.\n";
			}
		}
	}
}
?>
