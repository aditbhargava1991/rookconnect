<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
error_reporting(0);

$today_date = date('Y-m-d');
$inc_rep_followup_reminder_send = get_config($dbc, 'inc_rep_followup_reminder_send');
if($inc_rep_followup_reminder_send == 1) {
	$followup_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `ir14` = '$today_date' AND `assign_followup` != '' AND `deleted` = 0 AND `followup_reminder_sent` = 0"),MYSQLI_ASSOC);
	foreach ($followup_list as $followup) {
		$incidentreportid = $followup['incidentreportid'];
		$assign_followup = $followup['assign_followup'];
		$revision_number = $followup['revision_number'];
		$ir14 = $followup['ir14'];
		if($revision_number > 0) {
	        $pdf_url = 'download/incident_report_'.$incidentreportid.'_'.$revision_number.'.pdf';
		} else {
	        $pdf_url = 'download/incident_report_'.$incidentreportid.'.pdf';
		}

    	$ir_url = '<a href="'.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$incidentreportid.'">'.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$incidentreportid.'</a>';
    	$ir_pdfurl = '<a href="'.WEBSITE_URL.'/Incident Report/'.$pdf_url.'">'.WEBSITE_URL.'/Incident Report/'.$pdf_url.'</a>';
    	$inc_rep_followup_email = get_config($dbc, 'inc_rep_followup_email');
    	$inc_rep_followup_subject = get_config($dbc, 'inc_rep_followup_subject');
    	$inc_rep_followup_body = html_entity_decode(get_config($dbc, 'inc_rep_followup_body'));
    	$inc_rep_followup_subject = str_replace(['[FOLLOWUPDATE]','[URL]','[PDFURL]'],[$ir14,$ir_url,$ir_pdfurl],$inc_rep_followup_subject);
    	$inc_rep_followup_body = str_replace(['[FOLLOWUPDATE]','[URL]','[PDFURL]'],[$ir14,$ir_url,$ir_pdfurl],$inc_rep_followup_body);
    	$email = get_email($dbc, $assign_followup);
		mysqli_query($dbc, "UPDATE `incident_report` SET `followup_reminder_sent` = 1 WHERE `incidentreportid` = '$incidentreportid'");
		try {
			send_email($inc_rep_followup_email, $email, '', '', $inc_rep_followup_subject, $inc_rep_followup_body, '');
		} catch(Exception $e) {
			$log = "Unable to send e-mail to ".get_contact($dbc, $assign_followup).": ".$e->getMessage()."\n";
			mysqli_query($dbc, "UPDATE `incident_report` SET `email_error_log` = CONCAT(`email_error_log`, '$log') WHERE `incidentreportid` = '$incidentreportid'"); }
	}
}
