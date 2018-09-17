<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();

if(isset($_GET['id'])) {
	$query = "cert.certificateid={$_GET['id']}";
}
else {
	$query = "cert.reminder_date=DATE(NOW())";
}
$managers = explode(',',get_config($dbc, 'certificate_reminder_contact'));
$subject = get_config($dbc, 'certificate_reminder_subject');
$message = get_config($dbc, 'certificate_reminder_body');

if(date('H') == '08' || isset($_GET['id'])) {
	$sql = "SELECT c.first_name, c.last_name, cert.*
		FROM certificate cert LEFT JOIN contacts c ON cert.contactid=c.contactid WHERE $query";
	$cert_results = mysqli_query($dbc, $sql);

	while($row = mysqli_fetch_array($cert_results)) {
		$staff = trim(decryptIt($row['first_name']).' '.decryptIt($row['last_name']));
		$subject = str_replace("[ISSUE]", $row['issue_date'], $subject);
		$subject = str_replace("[EXPIRY]", $row['expiry_date'], $subject);
		$subject = str_replace("[STAFF]", $staff, $subject);
		$subject = str_replace("[TYPE]", $row['certificate_type'], $subject);
		$subject = str_replace("[TITLE]", $row['title'], $subject);
		$subject = str_replace("[DESCRIPTION]", $row['description'], $subject);

		$message = str_replace("[ISSUE]", $row['issue_date'], $message);
		$message = str_replace("[EXPIRY]", $row['expiry_date'], $message);
		$message = str_replace("[STAFF]", $staff, $message);
		$message = str_replace("[TYPE]", $row['certificate_type'], $message);
		$message = str_replace("[TITLE]", $row['title'], $message);
		$message = str_replace("[DESCRIPTION]", $row['description'], $message);

		$emails = array_unique(array_filter(array_merge($managers, explode(',',$row['certificate_reminder']))));
		foreach($emails as $email_contact) {
			if($email_contact > 0) {
				$email = get_email($dbc, $email_contact);

				$time = date('Y-m-d h:i:s');
				$title = $row['title'];

				try {
					echo "<p>$subject sent to $email at $time</p>";
					send_email("", $email, '', '', $subject, $message, '');
				} catch (Exception $e) {
					echo "Unable to send email: $title to $email\n";
				}
			} else if(filter_var($email_contact, FILTER_VALIDATE_EMAIL)) {
				$email = $email_contact;

				$time = date('Y-m-d h:i:s');
				$title = $row['title'];

				try {
					echo "<p>$subject sent to $email at $time</p>";
					send_email("", $email, '', '', $subject, $message, '');
				} catch (Exception $e) {
					echo "Unable to send email: $title to $email\n";
				}
			}
		}
	}
} ?>
