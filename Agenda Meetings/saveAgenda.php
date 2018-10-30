<?php
include('../include.php');
include_once('../function.php');
ob_clean();
//ajax detection code
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if ($isAjax)
{
    $_POST['submit'] = 'temp_save';
}

if (isset($_POST['submit'])) {
	$project_history = '';
    $button = $_POST['submit'];

    $businessid = implode(',',$_POST['businessid']);
    $businesscontactid = implode(',',$_POST['businesscontactid']);

	$newbusiness = filter_var($_POST['new_business_name'],FILTER_SANITIZE_STRING);
	$newcontact = filter_var($_POST['new_contact_name'],FILTER_SANITIZE_STRING);
	$newbusid = 0;
	if(trim($newbusiness) != '') {
		$result = mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`name`) VALUES ('Business','$newbusiness')");
		$newbusid = mysqli_insert_id($dbc);
		$businessid = str_replace('New Business','',$businessid).','.$newbusid;
	}
	if(trim($newcontact) != '') {
		$newcontact = explode(' ',$newcontact);
		$new_first = $newcontact[0];
		unset($newcontact[0]);
		$new_last = implode(' ',$newcontact);
		$result = mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`first_name`,`last_name`,`businessid`) VALUES ('Customers','$new_first','$new_last','$newbusid')");
		$newcontid = mysqli_insert_id($dbc);
		$businesscontactid = str_replace('New Contact','',$businesscontactid).','.$newcontid;
	}

    $companycontactid = ','.implode(',',$_POST['companycontactid']).',';
    $new_contact = filter_var($_POST['new_contact'],FILTER_SANITIZE_STRING);
    $date_of_meeting = filter_var($_POST['date_of_meeting'],FILTER_SANITIZE_STRING);
    $time_of_meeting = filter_var($_POST['time_of_meeting'],FILTER_SANITIZE_STRING);
    $end_time_of_meeting = filter_var($_POST['end_time_of_meeting'],FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $meeting_requested_by = filter_var($_POST['meeting_requested_by'],FILTER_SANITIZE_STRING);
    $meeting_objective = filter_var(htmlentities($_POST['meeting_objective']),FILTER_SANITIZE_STRING);
    $items_to_bring = filter_var($_POST['items_to_bring'],FILTER_SANITIZE_STRING);
    $projectid = implode(',',$_POST['projectid']);
    $servicecategory = implode('*#*',$_POST['servicecategory']);
    $agenda_topic = implode('##FFM##',$_POST['agenda_topic']);
    $ag_note = implode('##FFM##',$_POST['agenda_note']);
    $agenda_note = filter_var(htmlentities($ag_note),FILTER_SANITIZE_STRING);

   // $agenda_topic = filter_var($_POST['agenda_topic'],FILTER_SANITIZE_STRING);
   // $agenda_note = filter_var(htmlentities($_POST['agenda_note']),FILTER_SANITIZE_STRING);
    $qa_ticket = implode(',',$_POST['qa_ticket']);
    $agenda_email_business = implode(',',$_POST['agenda_email_business']);
    $agenda_email_company = implode(',',$_POST['agenda_email_company']);
    $agenda_additional_email = filter_var($_POST['agenda_additional_email'],FILTER_SANITIZE_STRING);

    $meeting_topic = filter_var($_POST['meeting_topic'],FILTER_SANITIZE_STRING);
    $meeting_note = filter_var(htmlentities($_POST['meeting_note']),FILTER_SANITIZE_STRING);
    $businesscontactemailid = implode(',',$_POST['businesscontactemailid']);
    $companycontactemailid = implode(',',$_POST['companycontactemailid']);
    $new_emailid = filter_var($_POST['new_emailid'],FILTER_SANITIZE_STRING);

    $client_deliverables = filter_var(htmlentities($_POST['client_deliverables']),FILTER_SANITIZE_STRING);
    $company_deliverables = filter_var(htmlentities($_POST['company_deliverables']),FILTER_SANITIZE_STRING);

    if($_POST['other_location'] != '') {
        $location = filter_var($_POST['other_location'],FILTER_SANITIZE_STRING);
    }
    if($_POST['other_heading'] != '') {
        $heading = filter_var($_POST['other_heading'],FILTER_SANITIZE_STRING);
    }
    $new_status = $_POST['new_status'];

    if($new_status == 'Pending') {
        $status = 'Pending';
    } else {
        $status = 'Approve';
    }
    if(($status == 'Pending') && ($button == 'Submit')) {
        $status = 'Approve';
    }
    if(($status == 'Approve') && ($button == 'Submit')) {
        $status = 'Done';
    }

    $subcommittee = filter_var($_POST['subcommittee'],FILTER_SANITIZE_STRING);

    if(empty($_POST['agendameetingid'])) {
        $query_insert_asset = "INSERT INTO `agenda_meeting` (`type`, `businessid`, `businesscontactid`, `companycontactid`, `new_contact`,	`date_of_meeting`, `time_of_meeting`, `end_time_of_meeting`, `location`, `heading`, `meeting_requested_by`, `meeting_objective`, `items_to_bring`, `projectid`, `servicecategory`, `agenda_topic`, `agenda_note`, `qa_ticket`, `agenda_email_business`, `agenda_email_company`, `agenda_additional_email`, `status`, `meeting_topic`, `meeting_note`, `businesscontactemailid`, `companycontactemailid`, `new_emailid`, `client_deliverables`, `company_deliverables`, `subcommittee`
        ) VALUES ('Agenda', '$businessid', '$businesscontactid', '$companycontactid', '$new_contact', '$date_of_meeting', '$time_of_meeting', '$end_time_of_meeting', '$location', '$heading', '$meeting_requested_by', '$meeting_objective', '$items_to_bring', '$projectid', '$servicecategory', '$agenda_topic', '$agenda_note', '$qa_ticket', '$agenda_email_business', '$agenda_email_company', '$agenda_additional_email', '$status', '$meeting_topic', '$meeting_note', '$businesscontactemailid', '$companycontactemailid', '$new_emailid', '$client_deliverables', '$company_deliverables', '$subcommittee')";
        $result_insert_asset = mysqli_query($dbc, $query_insert_asset);
        $agendameetingid = mysqli_insert_id($dbc);
        $url = 'Added';
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' created Meeting (#'.$agendameetingid.') for '.$meeting_objective.' regarding '.$agenda_topic.' at '.date('Y-m-d H:i');
    } else {
        $agendameetingid = $_POST['agendameetingid'];
        $query_update_asset = "UPDATE `agenda_meeting` SET `businessid` = '$businessid', `businesscontactid` = '$businesscontactid', `companycontactid` = '$companycontactid', `new_contact` = '$new_contact', `date_of_meeting`	= '$date_of_meeting', `time_of_meeting`	= '$time_of_meeting', `end_time_of_meeting`	= '$end_time_of_meeting', `location`	= '$location', `heading` = '$heading', `meeting_requested_by` = '$meeting_requested_by', `meeting_objective` = '$meeting_objective', `items_to_bring` = '$items_to_bring', `projectid` = '$projectid', `servicecategory`	= '$servicecategory', `agenda_topic` = '$agenda_topic', `agenda_note` = '$agenda_note', `qa_ticket` = '$qa_ticket', `agenda_email_business` = '$agenda_email_business', `agenda_email_company` = '$agenda_email_company', `agenda_additional_email` = '$agenda_additional_email', `status` = '$status', `meeting_topic` = '$meeting_topic', `meeting_note` = '$meeting_note', `businesscontactemailid` = '$businesscontactemailid', `companycontactemailid` = '$companycontactemailid', `new_emailid` = '$new_emailid', `client_deliverables` = '$client_deliverables', `company_deliverables` = '$company_deliverables', `subcommittee` = '$subcommittee' WHERE `agendameetingid` = '$agendameetingid'";
		$result_update_asset = mysqli_query($dbc, $query_update_asset);
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' updated Meeting (#'.$agendameetingid.') for '.$meeting_objective.' regarding '.$agenda_topic.' at '.date('Y-m-d H:i');
        $url = 'Updated';
    }

    //Document
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    for($i = 0; $i < count($_FILES['upload_agenda_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_agenda_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_agenda_document"]["tmp_name"][$i], "download/".$_FILES["upload_agenda_document"]["name"][$i]) ;

        if($document != '') {
            $result = mysqli_query($dbc, "SELECT * FROM agenda_meeting_upload WHERE agendaid='$agendameetingid'");
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $query_insert_client_doc = "UPDATE `agenda_meeting_upload` SET `upload_agenda_document` = '$document' where agendaid='$agendameetingid' ";
                $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            }else{
                $query_insert_client_doc = "INSERT INTO `agenda_meeting_upload` (`agendaid`, `upload_agenda_document`) VALUES ('$agendameetingid', '$document')";
                $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            }
        }
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $result = mysqli_query($dbc, "SELECT * FROM agenda_meeting_upload WHERE agendaid='$agendameetingid'");
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $query_insert_client_doc = "UPDATE `agenda_meeting_upload` SET `upload_agenda_document` = '$document' where agendaid='$agendameetingid' ";
                $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            }else{
                $query_insert_client_doc = "INSERT INTO `agenda_meeting_upload` (`agendaid`, `upload_agenda_document`) VALUES ('$agendameetingid', '$document')";
                $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            }
        }
    }

	if($button == 'temp_save') {
		$back_url = addOrUpdateUrlParam('agendameetingid', $agendameetingid);
		if($isAjax)
		{
			echo json_encode(array('status' => true ,'agendameetingid' => $agendameetingid, 'backUrl' => $back_url));exit();
		}else{
			echo '<script type="text/javascript"> window.location.replace("'.WEBSITE_URL.'/Agenda Meetings/add_agenda.php?agendameetingid='.$agendameetingid.'"); </script>';
		}
	}
	else {
		//Agenda Email
		if($agenda_email_business != '' || $agenda_email_company != '' || $agenda_additional_email != '') {
			$email_send = $agenda_email_business.','.$agenda_email_company.','.$agenda_additional_email;
		}
		$arr_email=array_filter(explode(",",$email_send));

		if($email_send != '') {
			$business = get_client($dbc, $businessid);

            if($heading != '') {
                $subject = $heading;
            } else {
			    $subject = $_POST['email_subject'];
            }

			$custom_body = html_entity_decode(str_replace(['[Business]','[Date]','[Start]','[End]','[Location]'],
				[$business, $date_of_meeting, $time_of_meeting, $end_time_of_meeting, $location],
				$get_field_config['email_body']));

			$email_body .= "<table width='100%' border='0'>";
			$email_body .= "<tr><td colspan='2'>".$custom_body."</td></tr>";

			if($business != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">'.BUSINESS_CAT.' :</td><td>'.$business.'</td></tr>';
			}
			if($businesscontactid != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Contact(s) :</td><td>'.get_multiple_contact($dbc, $businesscontactid.',').'</td></tr>';
			}
			if($companycontactid != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Staff Members :</td><td>'.get_multiple_contact($dbc, $companycontactid.',').'</td></tr>';
			}
			if($new_contact != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">New Contact :</td><td>'.$new_contact.'</td></tr>';
			}
			if($heading != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Heading :</td><td>'.$heading.'</td></tr>';
			}
			if($subcommittee != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Sub-Committee :</td><td>'.$subcommittee.'</td></tr>';
			}
			if($date_of_meeting != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Date of Meeting :</td><td>'.$date_of_meeting.'</td></tr>';
			}
			if($time_of_meeting != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Time of Meeting :</td><td>'.$time_of_meeting.($end_time_of_meeting != '' ? ' - '.$end_time_of_meeting : '').'</td></tr>';
			}
			if($location != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Location :</td><td>'.$location.'</td></tr>';
			}
			if($meeting_objective != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Objective :</td><td>'.$meeting_objective.'</td></tr>';
			}
			if($items_to_bring != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Items to Bring :</td><td>'.$items_to_bring.'</td></tr>';
			}
			if($projectid != '') {
				$projectlist = explode(',',$projectid);
				$projectid_list = [];
				$client_projectid_list = [];
				foreach($projectlist as $id) {
					if(substr($id,0,1) == 'C') {
						$client_projectid_list[] = $id;
					} else {
						$projectid_list[] = $id;
					}
				}
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Project :</td><td>'.get_multiple_project($dbc, implode(',',$projectid).',').get_multiple_client_project($dbc, implode(',',$client_projectid).',').'</td></tr>';
			}
			if($servicecategory != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Service(s) :</td><td>'.$servicecategory.'</td></tr>';
			}
			if($agenda_topic != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Agenda Topic(s) :</td><td>'.		str_replace('##FFM##', '<br>', $agenda_topic).'</td></tr>';
			}
			if($agenda_note != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Agenda Note :</td><td>'.html_entity_decode(str_replace('##FFM##', '<br><br>', $agenda_note)).'</td></tr>';
			}
			if($qa_ticket != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">'.TICKET_TILE.' Waiting for QA :</td><td>'.get_multiple_ticket($dbc, $qa_ticket.',').'</td></tr>';
			}
			$email_body .= ($get_field_config['email_logo'] != '' ? '<tr><td colspan="2"><img src="'.WEBSITE_URL.'/Agenda Meetings/download/'.$get_field_config['email_logo'].'" width="200" /></td>' : '');
			$email_body .= "</table>";

			$attachment = '';
			$result = mysqli_query($dbc, "SELECT * FROM agenda_meeting_upload WHERE agendaid='$agendameetingid'");
			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				while($row = mysqli_fetch_array($result)) {
					$file_support = 'download/'.$row['upload_agenda_document'];
					$attachment .= $file_support.'*#FFM#*';
				}
			}

			// foreach($arr_email as $to) {
                try {
					send_email([$_POST['email_sender']=>$_POST['email_name']], $arr_email, '', '', $subject, $email_body, $attachment);
				} catch (Exception $e) {
					echo "<script> alert('Unable to send the meeting to ".implode(', ',$arr_email)."'); </script>";
				}
			// }
		}
		//Agenda Email

		// Meeting Note Email
		if($businesscontactemailid != '' || $companycontactemailid != '' || $new_emailid != '') {
			$meeting_email_send = $businesscontactemailid.','.$companycontactemailid.','.$new_emailid;
		}
		$meeting_email_send = str_replace(',,', ',', $meeting_email_send);
		$meeting_email_send = rtrim($meeting_email_send,',');
		$meeting_email_send = ltrim($meeting_email_send,',');
		$meeting_arr_email=explode(",",$meeting_email_send);

		if($meeting_email_send != '') {
			$business = get_client($dbc, $businessid);

			if($get_field_config['meeting_email_subject'] == '') {
				$subject = 'Meeting Note for Meeting'.($date_of_meeting != '' ? ' on '.$date_of_meeting : '');
			} else {
				$subject = str_replace(['[Business]','[Date]','[Start]','[End]','[Location]'],
					[$business, $date_of_meeting, $time_of_meeting, $end_time_of_meeting, $location],
					$get_field_config['email_subject']);
			}
			$custom_body = html_entity_decode(str_replace(['[Business]','[Date]','[Start]','[End]','[Location]'],
				[$business, $date_of_meeting, $time_of_meeting, $end_time_of_meeting, $location],
				$get_field_config['email_body']));

			$email_body .= "<table width='100%' border='0'>";
			$email_body .= "<tr><td colspan='2'>".$custom_body."</td></tr>";

			if($business != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">'.BUSINESS_CAT.' :</td><td>'.$business.'</td></tr>';
			}
			if($businesscontactid != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Contact(s) :</td><td>'.get_multiple_contact($dbc, $businesscontactid.',').'</td></tr>';
			}
			if($companycontactid != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Staff Members :</td><td>'.get_multiple_contact($dbc, $companycontactid.',').'</td></tr>';
			}
			if($new_contact != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">New Contact :</td><td>'.$new_contact.'</td></tr>';
			}
			if($heading != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Heading :</td><td>'.$heading.'</td></tr>';
			}
			if($subcommittee != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Sub-Committee :</td><td>'.$subcommittee.'</td></tr>';
			}
			if($date_of_meeting != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Date of Meeting :</td><td>'.$date_of_meeting.'</td></tr>';
			}
			if($time_of_meeting != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Time of Meeting :</td><td>'.$time_of_meeting.($end_time_of_meeting != '' ? ' - '.$end_time_of_meeting : '').'</td></tr>';
			}
			if($location != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Location :</td><td>'.$location.'</td></tr>';
			}
			if($meeting_objective != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Objective :</td><td>'.$meeting_objective.'</td></tr>';
			}
			if($projectid != '') {
				$projectlist = explode(',',$projectid);
				$projectid_list = [];
				$client_projectid_list = [];
				foreach($projectlist as $id) {
					if(substr($id,0,1) == 'C') {
						$client_projectid_list[] = $id;
					} else {
						$projectid_list[] = $id;
					}
				}
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Project :</td><td>'.get_multiple_project($dbc, implode(',',$projectid).',').get_multiple_client_project($dbc, implode(',',$client_projectid).',').'</td></tr>';
			}
			if($servicecategory != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Service(s) :</td><td>'.$servicecategory.'</td></tr>';
			}
			if($meeting_topic != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Topic(s) :</td><td>'.$meeting_topic.'</td></tr>';
			}
			if($meeting_note != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Note :</td><td>'.html_entity_decode($meeting_note).'</td></tr>';
			}
			if($client_deliverables != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Client Deliverables :</td><td>'.html_entity_decode($client_deliverables).'</td></tr>';
			}
			if($company_deliverables != '') {
				$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Company Deliverables :</td><td>'.html_entity_decode($company_deliverables).'</td></tr>';
			}
			$email_body .= ($get_field_config['email_logo'] != '' ? '<tr><td colspan="2"><img src="'.WEBSITE_URL.'/Agenda Meetings/download/'.$get_field_config['email_logo'].'" width="200" /></td>' : '');
			$email_body .= "</table>";

			$meeting_attachment = '';
			$result = mysqli_query($dbc, "SELECT * FROM agenda_meeting_upload WHERE meetingid='$agendameetingid' AND meetingid IS NOT NULL");
			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				while($row = mysqli_fetch_array($result)) {
					$file_support = 'download/'.$row['upload_agenda_document'];
					$meeting_attachment .= $file_support.'*#FFM#*';
				}
			}

			try {
				send_email([$_POST['meeting_email_sender']=>$_POST['meeting_email_name']], $meeting_arr_email, '', '', $subject, $email_body, $meeting_attachment);
			} catch (Exception $e) {
				echo "<script> alert('Unable to send the meeting to ".implode(', ',$meeting_arr_email)."'); </script>";
			}
		}
		// Meeting Note Email

		// Save Project History
		foreach($_POST['projectid'] as $projectid) {
			if($projectid != '' && substr($projectid,0,1) != 'C') {
				$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
				mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '".htmlentities($project_history)."', '$projectid')");
			} else {
				$project_history_result = mysqli_query($dbc, "UPDATE `client_project` SET `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'".htmlentities($project_history)."') WHERE CONCAT('C',`projectid`) = '$projectid'");
			}
		}

		$back_url = (empty($_GET['from']) ? 'agenda.php' : urldecode($_GET['from']));
		if($isAjax){
			return json_encode(array('status' => true ,'agendameetingid' => '', 'backUrl' => $back_url));
		}else{
			echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
		}
	}

   // mysqli_close($dbc); //Close the DB Connection
}