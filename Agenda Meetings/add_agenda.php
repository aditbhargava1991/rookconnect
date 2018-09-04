<?php
/*
Add	Asset
*/
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
include ('../include.php');
include_once('../function.php');
error_reporting(0);
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"));
$value_config = ','.$get_field_config['field_config'].',';

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
            $query_insert_client_doc = "INSERT INTO `agenda_meeting_upload` (`agendaid`, `upload_agenda_document`) VALUES ('$agendameetingid', '$document')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `agenda_meeting_upload` (`meetingid`, `upload_agenda_document`) VALUES ('$agendameetingid', '$document')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

	if($button == 'temp_save') {
		$back_url = addOrUpdateUrlParam('agendameetingid', $agendameetingid);
		echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
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
		echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
	}

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function () {
    $(".other_location").hide();
    $(".other_heading").hide();
    $(".location").change(function(){
    $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="Other"){
                    $(".other_location").show();
                } else {
                    $(".other_location").hide();
                }
    });
    }).change();

    $(".heading").change(function(){
    $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="Other"){
                    $(".other_heading").show();
                } else {
                    $(".other_heading").hide();
                }
    });
    }).change();

    $("#form1").submit(function( event ) {
        if ($("input[name=code]").val() == '') {
            alert("Please make sure you have provided a valid code.");
            return false;
        }
        else if ($("#category").val() == '') {
            alert("Please make sure you have selected a category.");
            return false;
        }
        else if ($("#sub_category").val() == '') {
            alert("Please make sure you have selected a sub category.");
            return false;
        }
        else if ($("input[name=name]").val() == '') {
            alert("Please make sure you have filled in a name.");
            return false;
        }
        else if($("#category").val() == 'Other' && $("input[name=category_name]").val() == '') {
            alert("Please make sure you have filled in a category name.");
            return false;
        }
        else if($("#sub_category").val() == 'Other' && $("input[name=sub_category_name]").val() == '') {
            alert("Please make sure you have filled in a sub category name.");
            return false;
        }
    });

});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('agenda_meeting');
$back_url = (empty($_GET['from']) ? 'agenda.php' : urldecode($_GET['from']));
?>
<div class="container">
  <div class="row">

    <h3 class="gap-left pull-left">Agendas</h3>
    <?php if ( isset($_GET['projectid']) ) { ?>
        <div class="pull-right offset-top-15"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
    <?php } ?>
    <div class="clearfix"></div>

	<?php if ( !isset($_GET['projectid']) ) { ?>
        <div class="gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>
    <?php } ?>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
    $businessid = $get_field_config['default_business'] > 0 ? $get_field_config['default_business'] : '0';
    $businesscontactid = $get_field_config['default_contact'] > 0 ? $get_field_config['default_contact'] : '';
    $companycontactid = '';
    $new_contact = '';
    $date_of_meeting = '';
    $time_of_meeting = '';
    $end_time_of_meeting = '';
    $location = '';
     $heading = '';
   $meeting_requested_by = '';
    $meeting_objective = '';
    $items_to_bring = '';
    $projectid = (!empty($_GET['projectid']) ? $_GET['projectid'] : '');
    $servicecategory = '';
    $agenda_topic = '';
    $agenda_note = '';
    $qa_ticket = '';
    $agenda_email_business = '';
    $agenda_email_company = '';
    $agenda_additional_email = '';
    $status = 'Pending';
    $meeting_topic = '';
    $meeting_note = '';
    $client_deliverables = '';
    $company_deliverables = '';
    $subcommittee = '';

    $clientid = '';

    if(!empty($_GET['agendameetingid']))	{

        $agendameetingid = $_GET['agendameetingid'];
        $get_asset =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	agenda_meeting WHERE	agendameetingid='$agendameetingid'"));

        $businessid = ($get_asset['businessid'] == '' ? 0 : $get_asset['businessid']);
        $business_address = get_contact($dbc, $businessid, 'business_address').', '.get_contact($dbc, $businessid, 'city').', '.get_contact($dbc, $businessid, 'zip_code');

        $businesscontactid = $get_asset['businesscontactid'];
        $companycontactid = $get_asset['companycontactid'];
        $new_contact = $get_asset['new_contact'];
        $date_of_meeting = $get_asset['date_of_meeting'];
        $time_of_meeting = $get_asset['time_of_meeting'];
        $end_time_of_meeting = $get_asset['end_time_of_meeting'];
        $location = $get_asset['location'];
        $heading = $get_asset['heading'];
        $meeting_requested_by = $get_asset['meeting_requested_by'];
        $meeting_objective = $get_asset['meeting_objective'];
        $items_to_bring = $get_asset['items_to_bring'];
        $projectid = $get_asset['projectid'];
        $servicecategory = $get_asset['servicecategory'];
        $agenda_topic = $get_asset['agenda_topic'].'##FFM##';
        $agenda_note = $get_asset['agenda_note'].'##FFM##';
        $qa_ticket = $get_asset['qa_ticket'];
        $agenda_email_business = $get_asset['agenda_email_business'];
        $agenda_email_company = $get_asset['agenda_email_company'];
        $agenda_additional_email = $get_asset['agenda_additional_email'];
        $status = $get_asset['status'];
        $meeting_topic = $get_asset['meeting_topic'];
        $meeting_note = $get_asset['meeting_note'];
        $client_deliverables = $get_asset['client_deliverables'];
        $company_deliverables = $get_asset['company_deliverables'];
	    $subcommittee = $get_asset['subcommittee'];
		?>
		<input type="hidden" id="agendameetingid"	name="agendameetingid" value="<?php echo $agendameetingid ?>" />
    <?php } else {
		if(!empty($_GET['projectid'])) {
			$projectid = $_GET['projectid'];
			$_GET['bid'] = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid` FROM `project` WHERE `projectid`='$projectid'"))['businessid'];
		}
		if(!empty($_GET['bid'])) {
			$businessid = $_GET['bid'];
			$business_address = get_contact($dbc, $businessid, 'business_address').', '.get_contact($dbc, $businessid, 'city').', '.get_contact($dbc, $businessid, 'zip_code');
		}
	} ?>
    <input type="hidden" name="new_status" value="<?php echo $status; ?>" />

<div class="panel-group <?= !empty($projectid) ? 'block-panels main-screen' : '' ?>" id="accordion2" <?= !empty($projectid) ? 'style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);"' : '' ?>>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_bc" >
                        <?php echo (strpos($value_config, ','."Business".',') === FALSE ? 'Contacts' : BUSINESS_CAT); ?> & Attendees<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_bc" class="panel-collapse collapse in">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_meeting_business_contact.php');
                    ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mi" >
                        Meeting Basic Info<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_mi" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_meeting_basic_info.php');
                    ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Project".',') !== FALSE || strpos($value_config, ','."Service".',') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ps" >
	                        Project & Services<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse_ps" class="panel-collapse collapse">
	                <div class="panel-body">

	                    <?php
	                    include ('add_agenda_meeting_project_services.php');
	                    ?>

	                </div>
	            </div>
	        </div>
	    <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ai" >
                        Agendas Information<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ai" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_basic_info.php');
                    ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Documents".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_au" >
                        Agendas Uploader<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_au" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_uploader.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Tickets Waiting for QA".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_qa" >
                        <?= TICKET_TILE ?> Waiting for QA<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_qa" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_tickets.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status == 'Pending') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ea" >
                        Email Agendas<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ea" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_email.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status != 'Pending') { ?>
        <h1	class="triple-pad-bottom">Meeting</h1>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mi2" >
                        Meeting Information<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_mi2" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_basic_info.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Documents".',') !== FALSE) { ?>
        <?php if($status != 'Pending') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_meeu" >
                        Meeting Uploader<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_meeu" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_uploader.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>
        <?php } ?>

        <?php if($status == 'Approve') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_em" >
                        Email Meeting<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_em" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_email.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status == 'Approve') { ?>
        <!--
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tt" >
                        Ticket(s) & Task(s)<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_tt" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_ticket_task.php');
                    ?>

                </div>
            </div>
        </div>
        -->
        <?php } ?>

    </div>

		<!--
        <div class="form-group">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>
        -->

		<?php if ( isset($_GET['projectid']) ) { ?>
            <div class="form-group">
                <div class="pull-right">
                    <?php if($status == 'Pending') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Approve and Email Agendas</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else if($status == 'Approve') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Create Meeting and Email</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Update Agendas</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } ?>
                </div>
                <div class="pull-right">
                    <button	type="submit" name="submit"	value="Save" class="btn brand-btn pull-right">Save<?= ($status == 'Pending' || $status == 'Approve' ? ' and Email' : '') ?> Agendas</button>
                    <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save, in order to make changes later on."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
                <div class="pull-right">
                    <button	type="submit" name="submit"	value="temp_save" class="btn brand-btn pull-right">Save</button>
                    <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save Agenda without sending an email."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
                <div class="pull-right">
                    <span class="popover-examples" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="" class="btn brand-btn">Cancel</a>
                </div>
            </div>
        <?php } else { ?>
            <div class="form-group">
                <div class="col-sm-4">
                    <span class="popover-examples" style="margin:15px 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="<?php echo $back_url; ?>" class="btn brand-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-8">
                    <?php if($status == 'Pending') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Approve and Email Agendas</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else if($status == 'Approve') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Create Meeting and Email</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Update Agendas</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } ?>

                    <button	type="submit" name="submit"	value="Save" class="btn brand-btn btn-lg pull-right">Save<?= ($status == 'Pending' || $status == 'Approve' ? ' and Email' : '') ?> Agendas</button>
                    <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save, in order to make changes later on."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>

                    <button	type="submit" name="submit"	value="temp_save" class="btn brand-btn btn-lg pull-right">Save</button>
                    <span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save Agenda without sending an email."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
            </div>
        <?php } ?>

		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>