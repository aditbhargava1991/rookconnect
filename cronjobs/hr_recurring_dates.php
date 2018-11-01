<?php //HR Recurring Dates Cron Job
$guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();
$today_date = date('Y-m-d');

$hrs = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `recurring_due_date` = 1 AND `deleted` = 0"),MYSQLI_ASSOC);
//Check all hr forms that have recurring due dates
foreach($hrs as $hr) {
	//Make sure the interval is at least 1 and the type is a valid type
	if($hr['recurring_due_date_interval'] > 0 && in_array($hr['recurring_due_date_type'], ['days','weeks','months','years'])) {
		//This is the added date to calculate the recurring date
		$add_date = $hr['recurring_due_date_interval'].' '.$hr['recurring_due_date_type'];

		//All assigned staff
		$assign_staff = explode(',',$hr['assign_staff']);
		foreach ($assign_staff as $staffid) {
			//Verify that staff is a normal contactid
			if($staffid > 0) {
				//Check if there is already a hr_staff row for this hrid
				$hr_staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `hr_staff` WHERE `hrid` = '".$hr['hrid']."' AND `done` = 0 AND `staffid` = '$staffid'"));

				//If the hr_staff doesn't exist yet, check if the recurring date has happened since their last completed date
				if(empty($hr_staff)) {
					$hr_staff_last = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `hr_staff` WHERE `hrid` = '".$hr['hrid']."' AND `done` = 1 AND `staffid` = '$staffid' ORDER BY `today_date` DESC"));
					$last_completed = $hr_staff_last['today_date'];
					$next_date = date('Y-m-d', strtotime($last_completed.' + '.$add_date));

					//If the next date is today or already passed, then create the hr_staff row
					if(strtotime($next_date) <= strtotime($today_date) || empty($hr_staff_last)) {
						mysqli_query($dbc, "INSERT INTO `hr_staff` (`hrid`, `staffid`, `done`) VALUES ('".$hr['hrid']."', '$staffid', 0)");

						//If reminders are set, create reminder here
						if(!empty($hr['recurring_due_date_reminder'])) {
							mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `src_table`, `src_tableid`, `sent`) VALUES ('$staffid', '$today_date', 'HR', 'A reminder to complete form ".$hr['sub_heading']."', 'hr', '".$hr['hrid']."', 1)");
						}

						//If emails are set, send email here
						if(!empty($hr['recurring_due_date_email'])) {
							$subject = str_replace(['[CATEGORY]','[HEADING]'],[$hr['category'],$hr['heading']],$hr['email_subject']);
							$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$hr['category'],$hr['heading']],$hr['email_message']).'<p>Click <a href="'.WEBSITE_URL.'/HR/index.php?hr='.$hr['hrid'].'">here</a> to complete the form.</p>');
							$to = get_email($dbc, $staffid);
							try {
								send_email('', $to, '', '', $subject, $body, '');
							} catch(Exception $e) { }
						}
					}
				}
			}
		}
	}
}

$manuals = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `recurring_due_date` = 1 AND `deleted` = 0"),MYSQLI_ASSOC);
//Check all manuals forms that have recurring due dates
foreach($manuals as $manual) {
	//Make sure the interval is at least 1 and the type is a valid type
	if($manual['recurring_due_date_interval'] > 0 && in_array($manual['recurring_due_date_type'], ['days','weeks','months','years'])) {
		//This is the added date to calculate the recurring date
		$add_date = $manual['recurring_due_date_interval'].' '.$manual['recurring_due_date_type'];

		//All assigned staff
		$assign_staff = explode(',',$manual['assign_staff']);
		foreach ($assign_staff as $staffid) {
			//Verify that staff is a normal contactid
			if($staffid > 0) {
				//Check if there is already a manual_staff row for this manualtypeid
				$manual_staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `manuals_staff` WHERE `manualtypeid` = '".$manual['manualtypeid']."' AND `done` = 0 AND `staffid` = '$staffid'"));

				//If the manuals_staff doesn't exist yet, check if the recurring date has happened since their last completed date
				if(empty($manual_staff)) {
					$manual_staff_last = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `manauls_staff` WHERE `manualtypeid` = '".$manual['manualtypeid']."' AND `done` = 1 AND `staffid` = '$staffid' ORDER BY `today_date` DESC"));
					$last_completed = $manual_staff_last['today_date'];
					$next_date = date('Y-m-d', strtotime($last_completed.' + '.$add_date));

					//If the next date is today or already passed, then create the manuals_staff row
					if(strtotime($next_date) <= strtotime($today_date) || empty($manual_staff_last)) {
						mysqli_query($dbc, "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`, `done`) VALUES ('".$manual['manualtypeid']."', '$staffid', 0)");

						//If reminders are set, create reminder here
						if(!empty($manual['recurring_due_date_reminder'])) {
							mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `src_table`, `src_tableid`, `sent`) VALUES ('$staffid', '$today_date', 'MANUALS', 'A reminder to complete manual ".$manual['sub_heading']."', 'manuals', '".$manual['manualtypeid']."', 1)");
						}

						//If emails are set, send email here
						if(!empty($manual['recurring_due_date_email'])) {
							$subject = str_replace(['[CATEGORY]','[HEADING]'],[$manual['category'],$manual['heading']],$manual['email_subject']);
							$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$manual['category'],$manual['heading']],$manual['email_message']).'<p>Click <a href="'.WEBSITE_URL.'/Manuals/index.php?manual='.$manual['manualtypeid'].'">here</a> to complete the manual.</p>');
							$to = get_email($dbc, $staffid);
							try {
								send_email('', $to, '', '', $subject, $body, '');
							} catch(Exception $e) { }
						}
					}
				}
			}
		}
	}
}
