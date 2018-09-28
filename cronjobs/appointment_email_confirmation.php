<?php $guest_access = true;
/**
** Send appointment confirmation email before 2 days.
*/
//include	('../database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
error_reporting(0);

$starttime = date('Y-m-d', strtotime('+2 days'));
$confirmation_email_date = date('Y-m-d');

$inactive_patient =	mysqli_query($dbc,"SELECT bookingid, patientid, therapistsid FROM booking WHERE `notification_sent` = 0 AND follow_up_call_status = 'Booked Unconfirmed' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$starttime."')");

while($row = mysqli_fetch_array($inactive_patient)) {
    $bookingid = $row['bookingid'];
    $patientid = $row['patientid'];
    $therapistsid = $row['therapistsid'];

    $the_link = get_all_form_contact($dbc, $therapistsid, 'profile_link');

    $email = get_email($dbc, $patientid);

    $query_update_patient = "UPDATE `booking` SET `confirmation_email_date` = '$confirmation_email_date', `notification_sent` = 1 WHERE `bookingid` = '$bookingid'";
    $result_update_vendor = mysqli_query($dbc, $query_update_patient);

    $email_body = html_entity_decode(get_config($dbc, 'confirmation_email_body'));
    $email_body = str_replace("[Therapist Name]", get_contact($dbc, $therapistsid), $email_body);
    $email_body = str_replace("[Customer Name]", get_contact($dbc, $patientid), $email_body);
    $email_body = str_replace("[Appointment Date]", get_patient_from_booking($dbc, $bookingid, 'appoint_date'), $email_body);

    $profile_link = '<a target="_blank" href="'.$the_link.'">Click Here</a>';

    $email_body = str_replace("[Staff Profile Link]", $profile_link, $email_body);

    $confirmation_link = '<a href="'.WEBSITE_URL.'/Confirmation/confirmation_booking.php?id='.$bookingid.'&status=Confirmed">Click to Confirm</a>';

    $email_body = str_replace("[Confirmation Link]", $confirmation_link, $email_body);

    $res_link = '<a href="'.WEBSITE_URL.'/Confirmation/confirmation_booking.php?id='.$bookingid.'&status=Reschedule">Click to Reschedule</a>';

    $email_body = str_replace("[Reschedule]", $res_link, $email_body);

    $subject = get_config($dbc, 'confirmation_email_subject');

    //Mail
    if($email != '') {
        send_email('', $email, '', '', $subject, $email_body, '');
    }

}
    //echo 'Email Sent';

?>
