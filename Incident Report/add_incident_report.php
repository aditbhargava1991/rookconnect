<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('incident_report');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
echo "<script> var active_accordion = '".$_GET['active_accordion']."'; </script>";
$quick_action_icons = explode(',',get_config($dbc, 'inc_rep_quick_action_icons'));

if (isset($_POST['add_ir']) || isset($_POST['save_ir'])) {
	$user_form_id = filter_var($_POST['user_form_id'],FILTER_SANITIZE_STRING);
	$is_userform = '';
	if($user_form_id > 0) {
		$is_userform = 'increp_';
	}

    $type = filter_var($_POST[$is_userform.'type'],FILTER_SANITIZE_STRING);
    $completed_by = filter_var(implode(',',$_POST[$is_userform.'completed_by']),FILTER_SANITIZE_STRING);
    $date_of_happening = filter_var($_POST[$is_userform.'date_of_happening'],FILTER_SANITIZE_STRING);
    $date_of_report = filter_var($_POST[$is_userform.'date_of_report'],FILTER_SANITIZE_STRING);
    $project_type = filter_var($_POST[$is_userform.'project_type'],FILTER_SANITIZE_STRING);
    $projectid = filter_var($_POST[$is_userform.'projectid'],FILTER_SANITIZE_STRING);
    $ticketid = filter_var($_POST[$is_userform.'ticketid'],FILTER_SANITIZE_STRING);
    $contactid = filter_var(implode(',',$_POST[$is_userform.'contactid']),FILTER_SANITIZE_STRING);
    $clientid = filter_var(implode(',',$_POST[$is_userform.'clientid']),FILTER_SANITIZE_STRING);
    $programid = filter_var($_POST[$is_userform.'programid'],FILTER_SANITIZE_STRING);
    $memberid = filter_var(implode(',',$_POST[$is_userform.'memberid']),FILTER_SANITIZE_STRING);
	$equipmentid = filter_var(implode(',',$_POST[$is_userform.'equipmentid']),FILTER_SANITIZE_STRING);
    $other_names = filter_var($_POST[$is_userform.'other_names'],FILTER_SANITIZE_STRING);
	$other_driver_name = filter_var(htmlentities($_POST[$is_userform.'other_driver_name']),FILTER_SANITIZE_STRING);
	$other_driver_address = filter_var(htmlentities($_POST[$is_userform.'other_driver_address']),FILTER_SANITIZE_STRING);
	$other_driver_licence = filter_var(htmlentities($_POST[$is_userform.'other_driver_licence']),FILTER_SANITIZE_STRING);
	$other_driver_ins_company = filter_var(htmlentities($_POST[$is_userform.'other_driver_ins_company']),FILTER_SANITIZE_STRING);
	$other_driver_ins_policy = filter_var(htmlentities($_POST[$is_userform.'other_driver_ins_policy']),FILTER_SANITIZE_STRING);
	$other_owner_name = filter_var(htmlentities($_POST[$is_userform.'other_owner_name']),FILTER_SANITIZE_STRING);
	$other_owner_address = filter_var(htmlentities($_POST[$is_userform.'other_owner_address']),FILTER_SANITIZE_STRING);
	$witness_names = filter_var(htmlentities($_POST[$is_userform.'witness_names']),FILTER_SANITIZE_STRING);
    $workerid = filter_var(implode(',',$_POST[$is_userform.'workerid']),FILTER_SANITIZE_STRING);
    $ir5 = filter_var(htmlentities($_POST[$is_userform.'ir5']),FILTER_SANITIZE_STRING);

    if(!($user_form_id > 0)) {
		$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
	    $incident_date_date = date('Y-m-d', strtotime($_POST['incident_date_date']));
	    $incident_date_time = date('h:i:s a', strtotime($_POST['incident_date_time']));
		$incident_date = date('Y-m-d H:i:s', strtotime($_POST['incident_date_date'].' '.$_POST['incident_date_time']));
		$action_taken = filter_var($_POST['action_taken'],FILTER_SANITIZE_STRING);
		$follow_up_name = filter_var(implode('#*#',$_POST['follow_up_name']),FILTER_SANITIZE_STRING);
		$follow_up_title = filter_var(implode('#*#',$_POST['follow_up_title']),FILTER_SANITIZE_STRING);
		$follow_up_date = filter_var(implode('#*#',$_POST['follow_up_date']),FILTER_SANITIZE_STRING);
		$follow_up_who = filter_var(implode('#*#',$_POST['follow_up_who']),FILTER_SANITIZE_STRING);
		$recommendations = filter_var($_POST['recommendations'],FILTER_SANITIZE_STRING);

	    $ir1 = filter_var(htmlentities($_POST['ir1']),FILTER_SANITIZE_STRING);
	    $ir2 = filter_var(htmlentities($_POST['ir2']),FILTER_SANITIZE_STRING);
	    $ir3 = filter_var(htmlentities($_POST['ir3']),FILTER_SANITIZE_STRING);
	    $ir4 = filter_var(htmlentities($_POST['ir4']),FILTER_SANITIZE_STRING);
	    $ir6 = filter_var(htmlentities($_POST['ir6']),FILTER_SANITIZE_STRING);
	    $ir7 = filter_var(htmlentities($_POST['ir7']),FILTER_SANITIZE_STRING);
	    $ir8 = filter_var(htmlentities($_POST['ir8']),FILTER_SANITIZE_STRING);
	    $ir9 = filter_var(htmlentities($_POST['ir9']),FILTER_SANITIZE_STRING);
	    $ir10 = filter_var(htmlentities($_POST['ir10']),FILTER_SANITIZE_STRING);
	    $ir11 = filter_var(htmlentities($_POST['ir11']),FILTER_SANITIZE_STRING);
	    $ir12 = filter_var(htmlentities($_POST['ir12']),FILTER_SANITIZE_STRING);
	    $ir13 = filter_var(htmlentities(implode('#*#',$_POST['ir13'])),FILTER_SANITIZE_STRING);
	    $ir14 = filter_var($_POST['ir14'],FILTER_SANITIZE_STRING);
	    $ir15 = filter_var(htmlentities($_POST['ir15']),FILTER_SANITIZE_STRING);

	    $happening_lead_up = filter_var(htmlentities($_POST['happening_lead_up']),FILTER_SANITIZE_STRING);
	    $happening_follow_up = filter_var(htmlentities($_POST['happening_follow_up']),FILTER_SANITIZE_STRING);
	    $future_considerations = filter_var(htmlentities($_POST['future_considerations']),FILTER_SANITIZE_STRING);
		
		$assign_followup = filter_var(htmlentities($_POST['assign_followup']),FILTER_SANITIZE_STRING);
		$assign_corrective = filter_var(htmlentities($_POST['assign_corrective']),FILTER_SANITIZE_STRING);
		
		$funder_name = filter_var($_POST['funder_name'],FILTER_SANITIZE_STRING);
		$funder_contacted = filter_var($_POST['funder_contacted'],FILTER_SANITIZE_STRING);
		$supervisor = $_POST['supervisor'];
		$supervisor_sign = $_POST['sign2'];
		$coordinator = $_POST['coordinator'];
		$coordinator_sign = $_POST['sign3'];
		$coordinator_comments = filter_var(htmlentities($_POST['coord_comments']),FILTER_SANITIZE_STRING);
		$comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);
		$director = $_POST['director'];
		$director_sign = $_POST['sign4'];

		$multisign = $_POST['multisign'];

	    if (!file_exists('download')) {
	        mkdir('download', 0777, true);
	    }

	    if($_FILES["upload_document"]["name"] != '') {
	        $upload_document = implode('#$#', $_FILES["upload_document"]["name"]);
	    } else {
	        $upload_document = '';
	    }

		$upload_document = htmlspecialchars($upload_document, ENT_QUOTES);

	    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
	        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;
	    }
	}
	    
    $from = filter_var($_POST['from'],FILTER_SANITIZE_STRING);

    $report_options = mysqli_fetch_array(mysqli_query($dbc, "SELECT `incident_report`, `hide_fields`, `report_info`, `keep_revisions`, `user_emails`, `all_emails` FROM `field_config_incident_report` WHERE `row_type`='$type' AND '$type'!=''"));
    $keep_revisions = $report_options['keep_revisions'];
    $revision_number = '';
    $revision_date = '';

    if(empty($_POST['incidentreportid'])) {
		$reported_by = $_SESSION['contactid'];
		$sign = $_POST['output'];
		$today_date = date('Y-m-d');

        $query_insert_vendor = "INSERT INTO `incident_report` (`type`, `completed_by`, `date_of_happening`, `date_of_report`, `project_type`, `projectid`, `ticketid`, `contactid`, `clientid`, `programid`, `memberid`, `other_names`, `location`, `ir1`, `ir2`, `ir3`, `ir4`, `ir5`, `ir6`, `ir7`, `ir8`, `ir9`, `ir10`, `ir11`, `ir12`, `ir13`, `upload_document`, `ir14`, `ir15`, `happening_lead_up`, `happening_follow_up`, `future_considerations`, `equipmentid`, `other_driver_name`, `other_driver_address`, `other_driver_licence`, `other_driver_ins_company`, `other_driver_ins_policy`, `witness_names`, `assign_followup`, `assign_corrective`, `action_taken`, `followup_contact_name`, `followup_contact_title`, `followup_contact_date`, `followup_contact_who`, `recommendations`, `sign`, `today_date`, `reported_by`, `coordinator_comments`, `funder_name`, `funder_contacted`, `incident_date`, `comments`, `workerid`)
			VALUES ('$type', '$completed_by', '$date_of_happening', '$date_of_report', '$project_type', '$projectid', '$ticketid', '$contactid', '$clientid', '$programid', '$memberid', '$other_names', '$location', '$ir1', '$ir2', '$ir3',  '$ir4', '$ir5', '$ir6', '$ir7', '$ir8', '$ir9', '$ir10', '$ir11', '$ir12', '$ir13', '$upload_document', '$ir14', '$ir15', '$happening_lead_up', '$happening_follow_up', '$future_considerations', '$equipmentid', '$other_driver_name', '$other_driver_address', '$other_driver_licence', '$other_driver_ins_company', '$other_driver_ins_policy', '$witness_names', '$assign_followup', '$assign_corrective', '$action_taken', '$follow_up_name', '$follow_up_title', '$follow_up_date', '$follow_up_who', '$recommendations', '$sign', '$today_date', '$reported_by', '$coordinator_comments', '$funder_name', '$funder_contacted', '$incident_date', '$comments' ,'$workerid')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $incidentreportid = mysqli_insert_id($dbc);
        $img = sigJsonToImage($sign);
        imagepng($img, 'download/sign_'.$incidentreportid.'_reporting.png');

        $url = 'Added';
    } else {
        $incidentreportid = $_POST['incidentreportid'];
        if($keep_revisions == 1) {
            $revision_number = empty($_POST['revision_number']) ? 1 : intval($_POST['revision_number'] + 1);
            $revision_date = empty($_POST['revision_date']) ? date('Y-m-d') : $_POST['revision_date'].'*#*'.date('Y-m-d');
        }
        if($upload_document != '') {
            $upload_document = $_POST['upload_document_current'].'#$#'.$upload_document;
        } else {
			$upload_document = $_POST['upload_document_current'];
		}
        $query_update_vendor = "UPDATE `incident_report` SET `type` = '$type', `completed_by` = '$completed_by', `date_of_happening` = '$date_of_happening', `date_of_report` = '$date_of_report', `project_type` = '$project_type', `projectid` = '$projectid', `ticketid` = '$ticketid', `contactid` = '$contactid', `clientid`='$clientid', `programid` = '$programid', `memberid` = '$memberid', `other_names`='$other_names', `location`='$location', `ir1` = '$ir1', `ir2` = '$ir2', `ir3` = '$ir3', `ir4` = '$ir4', `ir5` = '$ir5', `ir6` = '$ir6', `ir7` = '$ir7', `ir8` = '$ir8', `ir9` = '$ir9', `ir10` = '$ir10', `ir11` = '$ir11', `ir12` = '$ir12', `ir13` = '$ir13', `upload_document` = '$upload_document', `ir14` = '$ir14', `ir15` = '$ir15', `happening_lead_up` = '$happening_lead_up', `happening_follow_up` = '$happening_follow_up', `future_considerations` = '$future_considerations', `action_taken`='$action_taken', `followup_contact_name`='$follow_up_name', `followup_contact_title`='$follow_up_title', `followup_contact_date`='$follow_up_date', `followup_contact_who`='$follow_up_who', `recommendations`='$recommendations', `coordinator_comments`='$coordinator_comments', `funder_name`='$funder_name', `funder_contacted`='$funder_contacted', `incident_date`='$incident_date', `assign_followup`='$assign_followup', `assign_corrective`='$assign_corrective', `comments`='$comments', `workerid` = '$workerid', `revision_number` = '$revision_number', `revision_date` = '$revision_date' WHERE `incidentreportid` = '$incidentreportid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid`='$incidentreportid'"));
		$reported_by = $row['reported_by'];
		$today_date = $row['today_date'];
        $url = 'Updated';
    }

    if(isset($_POST['save_ir'])) {
    	mysqli_query($dbc, "UPDATE `incident_report` SET `saved` = 1 WHERE `incidentreportid` = '$incidentreportid'");
    } else {
    	mysqli_query($dbc, "UPDATE `incident_report` SET `saved` = 0 WHERE `incidentreportid` = '$incidentreportid'");
    }

    if(!($user_form_id > 0)) {
		$multisign_i = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '$incidentreportid'"))['multisign'];
		if(empty($multisign_i)) {
			$multisign_i = 0;
		}
		foreach($multisign as $signature) {
			if(!empty($signature)) {
				$img = sigJsonToImage($signature);
				imagepng($img, 'download/multisign_'.$incidentreportid.'_'.$multisign_i.'.png');
				$multisign_i++;
			}
		}
		mysqli_query($dbc, "UPDATE `incident_report` SET `multisign` = '$multisign_i' WHERE `incidentreportid` = '$incidentreportid'");
		if($supervisor_sign != '') {
			$img = sigJsonToImage($supervisor_sign);
			imagepng($img, 'download/sign_'.$incidentreportid.'_supervisor.png');
			$result = mysqli_query($dbc, "UPDATE `incident_report` SET `supervisor_sign`='$supervisor_sign', `supervisor`='$supervisor' WHERE `incidentreportid`='$incidentreportid'");
		}
		if($coordinator_sign != '') {
			$img = sigJsonToImage($coordinator_sign);
			imagepng($img, 'download/sign_'.$incidentreportid.'_coordinator.png');
			$result = mysqli_query($dbc, "UPDATE `incident_report` SET `coordinator_sign`='$coordinator_sign', `coordinator`='$coordinator' WHERE `incidentreportid`='$incidentreportid'");
		}
		if($director_sign != '') {
			$img = sigJsonToImage($director_sign);
			imagepng($img, 'download/sign_'.$incidentreportid.'_director.png');
			$result = mysqli_query($dbc, "UPDATE `incident_report` SET `director_sign`='$director_sign', `director`='$director' WHERE `incidentreportid`='$incidentreportid'");
		}

		$pdf_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_incident_report` WHERE `incident_report_dashboard` IS NOT NULL"));
		$current_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `incident_report`, `report_info` FROM `field_config_incident_report` WHERE `row_type`='$type' UNION SELECT GROUP_CONCAT(`incident_report`), '' FROM `field_config_incident_report` WHERE IFNULL(`incident_report`,'') != ''"));
		$pdf_logo = (empty($pdf_config['pdf_logo']) ? WEBSITE_URL.'/img/ffm-logo-support.png' : 'download/'.$pdf_config['pdf_logo']);
		$pdf_header = $pdf_config['pdf_header'];
		$pdf_footer = $pdf_config['pdf_footer'];
		$pdf_title = $pdf_config['pdf_title'];
		$pdf_start = $current_config['report_info'];
		$pdf_notes = $pdf_config['pdf_notes'];
		$value_config = ','.$current_config['incident_report'].',';
		DEFINE(PDF_LOGO,$pdf_logo);
		$logo_height = 0;
		$logo_width = 0;
		if(file_exists($pdf_logo)) {
			list($image_width, $image_height) = getimagesize($pdf_logo);
			$logo_height = $image_height;
			$logo_width = $image_width;
			if($image_height > 180) {
				$logo_width = (180 / $logo_height) * $logo_width;
				$logo_height = 180;
			}
			if($logo_width > 360) {
				$logo_height = (360 / $logo_width) * $logo_height;
				$logo_width = 360;
			}
			$logo_height = $logo_height / 7.2;
			$logo_width = $logo_width / 7.2;
		}
		DEFINE(PDF_LOGO_LOGO_HEIGHT, $logo_height);
		DEFINE(PDF_LOGO_LOGO_WIDTH, $logo_width);

		DEFINE(PDF_HEADER,html_entity_decode($pdf_header));
		DEFINE(PDF_TITLE,html_entity_decode($pdf_title));
		DEFINE(PDF_FOOTER,html_entity_decode($pdf_footer));
		
	    class MYPDF extends TCPDF {

			//Page header
			public function Header() {
				$image_file = PDF_LOGO;
				$this->Image($image_file, 10, 10, PDF_LOGO_LOGO_WIDTH, PDF_LOGO_LOGO_HEIGHT, 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
				$this->SetY(0);
				$this->WriteHTMLCell(0, 0, 70, 10, PDF_HEADER, 0, 0, 0, true, 'R');
				$this->SetFont('helvetica', '', 13);
				$this->SetY(PDF_LOGO_LOGO_HEIGHT + 10);
				$this->WriteHTMLCell(0, 0, 10, '', '<h3>'.PDF_TITLE.'</h3>', 0, 0, 0, true, 'C');
			}

			// Page footer
			public function Footer() {
				// Position at 15 mm from bottom
				$this->SetFont('helvetica', '', 9);
				$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
				$this->SetY(-20);
				$this->WriteHTMLCell(0, 0, 10, '', $footer_text, 0, 0, 0, true, 'R');
				$this->SetY(-20);
				$this->WriteHTMLCell(0, 0, 10, '', PDF_FOOTER, 0, 0, 0, true, 'L');
			}
		}

		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
		$pdf->setFooterData(array(0,64,0), array(0,64,128));
		
		$LOGO_HEIGHT = 40;
		if(PDF_LOGO != '') {
			list($img_width, $img_height, $img_type, $attr) = getimagesize(PDF_LOGO);
			$LOGO_HEIGHT = 20 + $img_height / ($img_width / 60);
		}

		$pdf->SetMargins(PDF_MARGIN_LEFT, $LOGO_HEIGHT, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->AddPage();
	    $pdf->SetFont('helvetica', '', 9);

	    if($type == 'Critical Incident' || $type == 'Non-Critical Incident') {
			$pdf->SetFont('helvetica', '', 10);
			$type_list = '';
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_incident_report"));
			foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
				$type_list .= '<input type="radio" name="incident_type" '.($in_type == $type ? 'checked="checked"' : '').' value="'.preg_replace('/[^a-zA-Z]/','',$in_type).'"> <b>'.$in_type.'</b> ';
			}
			$html = '<form action="" method="POST" enctype="multipart/form-data">';
			$html = '<p style="text-align:center;">'.$type_list."</p>";
			$html .= '<table borders="0" width="100%" cellspacing="5">
				<tr><td><br />Date of Incident:</td><td style="border-bottom:1px solid black">'.date('Y-m-d',strtotime($incident_date)).'</td>
					<td style="border-bottom:none;">Time:</td><td style="border-bottom:1px solid black">'.date('g:i a',strtotime($incident_date)).'</td></tr>
				<tr><td><br />Location of Incident:</td><td colspan="3" style="border-bottom:1px solid black">'.$location.'</td></tr>
				<tr><td><br />Client(s) Involved:</td><td colspan="3" style="border-bottom:1px solid black">';
	        if($keep_revisions == 1) {
	            if($revision_number != '') {
	                $html .= '<tr><td><br />Revision Number:</td><td style="border-bottom:1px solid black">'.$revision_number.'</td></tr>
	                    <tr><td><br />Revision Date:</td><td style="border-bottom:1px solid black">'.date('Y-m-d').'</td></tr>';
	            }
	        }
			$list = [];
			foreach(explode(',',$clientid) as $client) {
				$client_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$client'"));
				$list[] = ($client_info['name'] != '' ? decryptIt($client_info['name']) : '').($client_info['first_name'] != '' || $client_info['last_name'] != '' ? ($client_info['name'] != '' ? ': ' : '').trim(decryptIt($client_info['first_name']).' '.decryptIt($client_info['last_name'])) : '');
			}
			$html .= implode(', ',$list);
			$html .= '</td></tr>
				<tr><td><br />Staff Involved:</td><td colspan="3" style="border-bottom:1px solid black">';
			$list = [];
			foreach(explode(',',$contactid) as $staff) {
				$list[] = get_contact($dbc, $staff);
			}
			$html .= implode(', ',$list);
			$html .= '</td></tr>
				<tr><td><br />Others:</td><td colspan="3" style="border-bottom:1px solid black">'.$other_names.'</td></tr>
				<tr><td colspan="4"><br />Describe the Accident/Incident: (use attached page three if more space required)</td></tr>
				<tr><td colspan="4">'.html_entity_decode($ir1).'<br /><br /><br /></td></tr>
				<tr><td colspan="4"><b>Action taken (what, where &amp; by whom):</b></td></tr>
				<tr><td colspan="4">'.$action_taken.'<br /><br /><br /></td></tr>
				<tr><td colspan="4">Action and follow-up <b>(please indicate who was contacted, when, and by whom):</b></td></tr>';
			$follow_titles = explode('#*#',$follow_up_title);
			$follow_names = explode('#*#',$follow_up_name);
			$follow_dates = explode('#*#',$follow_up_date);
			$follow_who_list = explode('#*#',$follow_up_who);
			foreach($follow_titles as $i => $title) {
				$html .= '<tr><td><br />'.$title.':</td><td colspan="3" style="border-bottom:1px solid black">'.(empty($follow_names[$i]) ? '' : $follow_names[$i].' was contacted ('.$follow_dates[$i].') by '.$follow_who_list[$i]).'</td></tr>';
			}
			$html .= '</table>';
			
			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 10);
			
			$html = '<table borders="0" width="100%" cellspacing="5"><tr><td width="25%"></td><td width="25%"></td><td width="25%"></td><td width="25%"></td></tr>';
			$html .= '<tr><td colspan="4">Recommendations on how to correct or avoid recurrence of this type of accident or incident:</td></tr>
				<tr><td colspan="4">'.$recommendations.'<br /></td></tr>
				<tr><td colspan="4"><b>Report Completed by</b></td></tr>
				<tr><td colspan="2"><img src="download/sign_'.$incidentreportid.'_reporting.png" width="100"></td>
					<td style="vertical-align: bottom; text-align: center;"><br /><br />'.get_contact($dbc, $reported_by).'<br />'.get_contact($dbc, $reported_by, 'position').'</td>
					<td style="vertical-align: bottom; text-align: center;"><br /><br /><br />'.$today_date.'</td></tr>
				<tr><td colspan="2" align="center" style="border-top:thin solid black">(Signature)</td><td align="center" style="border-top:thin solid black">Print Name & Position</td><td align="center" style="border-top:thin solid black">Date</td></tr>
				<tr><td colspan="2"><br /><br /></td><td></td><td></td></tr>
				<tr height="20"><td align="center" style="border-top:thin solid black">Witness</td><td align="center" style="border-top:thin solid black">Date</td>
					<td align="center" style="border-top:thin solid black">Witness</td><td align="center" style="border-top:thin solid black">Date</td></tr>
				<tr><td align="center"><b>Date faxed to Admin office</b></td><td style="border-bottom:1px solid black"></td><td colspan="2" align="center"><b>All reports must be faxed to admin office within 24 hours</b></td></tr>
				<tr><td colspan="4">Comments:</td></tr>
				<tr><td colspan="4">'.html_entity_decode($comments).'<br /></td></tr>
				<tr><td colspan="4"><b>Reviewed by:</b></td></tr>
				<tr><td colspan="2">'.(empty($supervisor) ? '<br /><br /><br />' : '<img src="download/sign_'.$incidentreportid.'_supervisor.png" width="100">').'</td><td colspan="2">'.$supervisor.'</td></tr>
				<tr><td colspan="2" align="center" style="border-top:thin solid black">Supervisor\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>
				<tr><td colspan="2">'.(empty($coordinator) ? '<br /><br /><br />' : '<img src="download/sign_'.$incidentreportid.'_coordinator.png" width="100">').'</td><td colspan="2">'.$coordinator.'</td></tr>
				<tr><td colspan="2" align="center" style="border-top:thin solid black">Coordinator\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>';
			$html .= '<tr><td colspan="4">For Critical Incidents only:</td></tr>
				<tr><td><br />Funder Contacted:</td><td style="border-bottom:thin solid black">'.$funder_name.'</td><td><br />Date:</td><td style="border-bottom:thin solid black">'.$funder_contacted.'</td></tr>';
			$html .= '<tr><td colspan="2">'.(empty($director) ? '<br /><br /><br />' : '<img src="download/sign_'.$incidentreportid.'_director.png" width="100">').'</td><td colspan="2">'.$director.'</td></tr>
				<tr><td colspan="2" align="center" style="border-top:thin solid black">Director\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>
				</table></form>';
			
			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 10);
			$html = '';
		} else {
			$html = html_entity_decode($pdf_start);
	        if($keep_revisions == 1) {
	            if($revision_number != '') {
	                $html .= "<b>Revision Number:</b> ".$revision_number."<br />";
	                $html .= "<b>Revision Date:</b> ".date('Y-m-d')."<br />";
	            }
	        }
			$html .= "<h3>$type</h3>\n";
			if (strpos($value_config, ','."Completed By".',') !== FALSE) {
				foreach(explode(',',$completed_by) as $completed_id) {
					$html .= "<b>Completed By:</b> ".get_contact($dbc, $completed_id)."<br />";
				}
			}
			if (strpos($value_config, ','."Date of Happening".',') !== FALSE) {
				$html .= "<b>Date of Happening:</b> ".$date_of_happening."<br />";
			}
			if (strpos($value_config, ','."Date of Report".',') !== FALSE) {
				$html .= "<b>Date of Report:</b> ".$date_of_report."<br />";
			}
			if (strpos($value_config, ','."Program".',') !== FALSE) {
				$html .= "<b>Program:</b> ".(!empty(get_client($dbc, $programid)) ? get_client($dbc, $programid) : get_contact($dbc, $programid))."<br />\n";
			}
			if (strpos($value_config, ','."Project Type".',') !== FALSE) {
				$project_tabs = get_config($dbc, 'project_tabs');
				if($project_tabs == '') {
					$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
				}
				$project_tabs = explode(',',$project_tabs);
				$project_vars = [];
				foreach($project_tabs as $item) {
					$project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
				}
				$html .= "<b>".PROJECT_NOUN." Type:</b> ".$project_vars[$project_type]."<br />";
			}
			if (strpos($value_config, ','."Project".',') !== FALSE) {
				$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '$projectid'"));
				$html .= "<b>".PROJECT_NOUN.":</b> ".get_project_label($dbc, $project)."<br />";
			}
			if (strpos($value_config, ','."Ticket".',') !== FALSE) {
				$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
				$html .= "<b>".TICKET_NOUN.":</b> ".get_ticket_label($dbc, $ticket)."<br />";
			}
	        if (strpos($value_config, ','."Client".',') !== FALSE) {
				$html .= "<b>Client(s) Involved:</b> ";
				$client_list = [];
				foreach(explode(',',$clientid) as $client) {
					if($client != '') {
						$client_list[] = get_contact($dbc, $client);
					}
				}
				$html .= implode(', ', $client_list)."<br />\n";
			}
			if (strpos($value_config, ','."Member".',') !== FALSE) {
				$html .= "<b>Member(s) Involved:</b> ";
				$member_list = [];
				foreach(explode(',',$memberid) as $member) {
					if($member != '') {
						$member_list[] = get_contact($dbc, $member);
					}
				}
				$html .= implode(', ', $member_list)."<br />\n";
			}
			if (strpos($value_config, ','."Equipment".',') !== FALSE) {
				$html .= "<b>Equipment Involved:</b> ";
	            foreach(explode(',',$equipmentid) as $equipid) {
	                $equip = mysqli_fetch_array(mysqli_query($dbc, "SELECT `equipmentid`,`unit_number`,`make`,`model`,`licence_plate` FROM `equipment` WHERE `equipmentid`='$equipid'"));
	                $html .= $equip['make'].' '.$equip['model'].' Unit #'.$equip['unit_number'].' (Licence Plate '.$equip['licence_plate'].")<br />\n";
	            }
			}
			if (strpos($value_config, ','."Driver".',') !== FALSE) {
				$html .= "<b>".(strpos($value_config, ','."Driver_WorkerLabel".',') !== FALSE ? 'Worker/ Operator' : 'Driver').":</b> ";
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$contactid'"));
				$html .= decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (Licence #'.$row['license'].', Address: ';
				$html .= (!empty($row['address']) ? $row['address'] : (!empty($row['mailing_address']) ? $row['mailing_address'] : (!empty($row['business_address']) ? $row['business_address'] : $row['ship_to_address']))).')<br />';
			} else if (strpos($value_config, ','."Staff".',') !== FALSE) {
				if(strpos($value_config, ','."Staff_InvolvedLabel".',') !== FALSE) {
					$html .= "<b>Staff Involved:</b> ";
				} else {
					$html .= "<b>Staff:</b> ";
				}
				$staff_list = [];
				foreach(explode(',',$contactid) as $contact) {
					if($contact != '') {
						$staff_list[] = get_contact($dbc, $contact);
					}
				}
				$html .= implode(', ', $staff_list)."<br />\n";
			}
	        if (strpos($value_config, ','."Workers Involved".',') !== FALSE) {
	            $html .= "<b>Workers Involved:</b> ";
	            foreach(explode(',',$workerid) as $worker) {
	                $html .= get_contact($dbc, $worker)."<br />\n";
	            }
	        }
			if (strpos($value_config, ','."Others".',') !== FALSE) {
				$html .= "<b>Others:</b> ".$other_names."<br />\n";
			}
			if (strpos($value_config, ','."Other Driver".',') !== FALSE) {
				$html .= "<b>Other Driver's Name:</b> $other_driver_name<br />\n";
				$html .= "<b>Other Driver's Address:</b> $other_driver_address<br />\n";
				$html .= "<b>Other Driver's Licence #:</b> $other_driver_licence<br />\n";
				$html .= "<b>Other Driver's Insurance Company:</b> $other_driver_ins_company<br />\n";
				$html .= "Other Driver's Insurance Policy:</b> $other_driver_ins_policy<br />\n";
			}
			if (strpos($value_config, ','."Description Accordion".',')) {
				$html .= "<b>Date Of Incident:</b> $incident_date_date<br />\n";
				$html .= "<b>Time Of Incident:</b> $incident_date_time<br />\n";
			}
			if (strpos($value_config, ','."Accident Report".',') !== FALSE) {
				$html .= "<b>Accident Report</b><br>\n";
				$html .= html_entity_decode($ir1);
			} else if (strpos($value_config, ','."Description".',') !== FALSE) {
				$html .= "<b>Description Of Incident<br>\n";
				$html .= html_entity_decode($ir1);
			}
			if (strpos($value_config, ','."Location".',') !== FALSE) {
				$html .= "<b>Location Of Incident:</b> $location<br />\n";
			}
			if (strpos($value_config, ','."Record Equipment Or Property Damage".',') !== FALSE) {
				$html .= "<b>Record Equipment Or Property Damage:</b><br />\n";
				$html .= html_entity_decode($ir2);
			}
			if (strpos($value_config, ','."Action Taken".',') !== FALSE) {
				$html .= "<b>Action taken:</b><br />\n";
				$html .= html_entity_decode($action_taken)."<br />\n";
			}
			if (strpos($value_config, ','."Record Of Injury Involved".',') !== FALSE) {
				$html .= "<b>Record Of Injury Involved:</b><br />\n";
				$html .= html_entity_decode($ir9);
			}
			if (strpos($value_config, ','."Determine Causes".',') !== FALSE) {
				if(strpos($value_config, ','."Direct Indirect Root Causes".',') !== FALSE) {
					$html .= "<b>Determine Causes (Direct/Indirect/Root):</b>\n";
					$html .= html_entity_decode(str_replace('#*#','',$ir13));
					if(empty(str_replace('#*#','',$ir13))) {
						$html .= '<br>';
					}
				}
				if(strpos($value_config, ','."Happening Lead Up".',') !== FALSE) {
					$html .= "<b>Happening Lead Up:</b>\n";
					$html .= html_entity_decode($happening_lead_up);
					if(empty($happening_lead_up)) {
						$html .= '<br>';
					}
				}
				if(strpos($value_config, ','."Happening Follow Up".',') !== FALSE) {
					$html .= "<b>Happening Follow Up:</b>\n";
					$html .= html_entity_decode($happening_follow_up);
					if(empty($happening_follow_up)) {
						$html .= '<br>';
					}
				}
				if(strpos($value_config, ','."Future Considerations".',') !== FALSE) {
					$html .= "<b>Future Considerations:</b>\n";
					$html .= html_entity_decode($future_considerations);
					if(empty($future_considerations)) {
						$html .= '<br>';
					}
				}
			}
			if (strpos($value_config, ','."Supply Pictures".',') !== FALSE) {
				if (strpos($value_config, ','."Pictures_ProvideLabel".',') !== FALSE) {
					$html .= "<b>Provide Pictures</b><br />";
				} else {
					$html .= "<b>Pictures</b><br />";
				}
				foreach(explode('#$#', $upload_document) as $picture) {
					$file_type = strtolower(pathinfo($venin, PATHINFO_EXTENSION));
					if($file_type == 'pdf') {
						try {
							exec('gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o "download/field_invoice/'.$venin.'.png" "download/field_invoice/'.$venin.'"');
							$venin .= '.png';
							$file_type = 'png';
						} catch(Exception $e) { }
					}
	                $file_type = strtolower(pathinfo($picture, PATHINFO_EXTENSION));
					if($file_type == 'jpg' || $file_type == 'jpeg' || $file_type == 'bmp' || $file_type == 'gif' || $file_type == 'png') {
						$html .= '<img src="download/'.$picture."\" /><br />\n";
					}
				}
			}
			if (strpos($value_config, ','."Recommendations".',') !== FALSE) {
				$html .= "<b>Recommendations on how to correct or avoid recurrence of this type of accident or incident:</b><br />\n";
				$html .= html_entity_decode($recommendations)."<br />\n";;
			}
			if (strpos($value_config, ','."Reporting Information".',') !== FALSE) {
				$html .= "<b>Reported:</b> ".get_contact($dbc, $reported_by).': '.$today_date."<br />\n";
				$html .= '<img src="download/sign_'.$incidentreportid.'_reporting.png" width="190" height="80" border="0" alt=""><br />';
			}
			if (strpos($value_config, ','."Comments".',') !== FALSE) {
				$html .= "<b>Comments:</b><br />\n";
				$html .= html_entity_decode($comments);
			}
			if (strpos($value_config, ','."Supervisor Statement & Signoff".',') !== FALSE) {
				$html .= "<b>Supervisor Statement:</b><br />\n";
				$html .= html_entity_decode($ir6);
				if($supervisor_sign != '') {
					$html .= "<b>Supervisor Signature:</b><br />\n";
					$html .= '<img src="download/sign_'.$incidentreportid.'_supervisor.png" width="190" height="80" border="0" alt=""><br />';
					$html .= "<b>Date:</b> $supervisor<br />\n";
				}
			}
			if (strpos($value_config, ','."Coordinator Statement & Signoff".',') !== FALSE) {
				$html .= "<b>Coordinator Statement:</b><br />\n";
				$html .= html_entity_decode($ir6);
				if($supervisor_sign != '') {
					$html .= "<b>Coordinator Signature:<br />\n";
					$html .= '<img src="download/sign_'.$incidentreportid.'_coordinator.png" width="190" height="80" border="0" alt=""><br />';
					$html .= "<b>Date:</b> $coordinator<br />\n";
				}
			}
			if (strpos($value_config, ','."Funder Contact".',') !== FALSE) {
				$html .= "<b>Funder Contacted:</b> $funder_name<br />\n";
				$html .= "<b>Date Contacted:</b> $funder_contacted<br />\n";
			}
			if (strpos($value_config, ','."Director Signature".',') !== FALSE) {
				$html .= "<b>Director Statement:</b><br />\n";
				$html .= html_entity_decode($ir6);
				if($director_sign != '') {
					$html .= "<b>Director Signature:<br />\n";
					$html .= '<img src="download/sign_'.$incidentreportid.'_director.png" width="190" height="80" border="0" alt=""><br />';
					$html .= "<b>Date:</b> $director<br />\n";
				}
			}
			if (strpos($value_config, ','."Record Cause Of Accident".',') !== FALSE) {
				$html .= "<b>Record Cause Of Accident:</b><br />\n";
				$html .= html_entity_decode($ir3);
			}
			if (strpos($value_config, ','."Witness Statement".',') !== FALSE) {
				$html .= "<b>Witness(s) Name / Phone Number:</b> $witness_names<br />\n";
				$html .= "<b>Witness(s) Statement:</b><br />\n";
				$html .= html_entity_decode($ir5);
			}
			if (strpos($value_config, ','."Taken Care".',') !== FALSE) {
				$html .= "<b>Injured To Be Taken Care Of And Supervisors/Medical Aid Contacted:</b><br />\n";
				$html .= html_entity_decode($ir7);
			}
			if (strpos($value_config, ','."Initial Actions Required".',') !== FALSE) {
				$html .= "<b>Initial Actions Required:</b><br />\n";
				$html .= html_entity_decode($ir8);
			}
			if (strpos($value_config, ','."Interview Witness(s)".',') !== FALSE) {
				$html .= "<b>Witness(s) Name / Phone Number:</b> $witness_names<br />\n";
				$html .= "<b>Interview Witness(s):</b><br />\n";
				$html .= html_entity_decode($ir10);
			}
			if (strpos($value_config, ','."Check Background Info".',') !== FALSE) {
				$html .= "<b>Check Background Info:</b><br />\n";
				$html .= html_entity_decode($ir11);
			}
			if (strpos($value_config, ','."Timing".',') !== FALSE) {
				$html .= "<b>Record Length Of Time Working With The Company:</b><br />\n";
				$html .= html_entity_decode($ir12);
			}
			if (strpos($value_config, ','."Follow Up".',') !== FALSE) {
				$html .= "<b>Follow Up Date:</b> $ir14<br />\n";
				$html .= "<b>Assign Follow Up:</b> ".get_contact($dbc, $assign_followup)."<br />\n";
			}
			if (strpos($value_config, ','."Corrective Action".',') !== FALSE) {
				$html .= "<b>Corrective Action:</b><br />\n";
				$html .= html_entity_decode($ir4);
				$html .= "<b>Assign Action:</b> ".get_contact($dbc, $assign_corrective)."<br />\n";
			}
			if (strpos($value_config, ','."Managers Review Signature".',') !== FALSE) {
				$html .= "<b>Managers Review:</b><br />\n";
				$html .= html_entity_decode($ir15);
				if($supervisor_sign != '') {
					$html .= "<b>Managers Signature:<br />\n";
					$html .= '<img src="download/sign_'.$incidentreportid.'.png" width="190" height="80" border="0" alt=""><br />';
					$html .= "<b>Date:</b> $director<br />\n";
				}
			}
			if (strpos($value_config, ','."Multiple Signatures".',') !== FALSE) {
				$html .= "<b>Sign Off:</b><br />\n";
				for ($i = 0; $i < $multisign_i; $i++) {
					$html .= '<img src="download/multisign_'.$incidentreportid.'_'.$i.'.png" width="190" height="80" border="0" alt=""><br />';
				}
			}
			if (strpos($value_config, ','."Completed By Office".',') !== FALSE) {
				$html .= "<b>Follow Up Date:</b> $ir14<br />\n";
				$html .= "<b>Assign Follow Up:</b> ".get_contact($dbc, $assign_followup)."<br />\n";
				$html .= "<b>Corrective Action:</b>\n";
				$html .= html_entity_decode($ir4);
				if($director_sign != '') {
					$html .= "<b>Director Signature:<br />\n";
					$html .= '<img src="download/sign_'.$incidentreportid.'_director.png" width="190" height="80" border="0" alt=""><br />';
					$html .= "<b>Date:</b> $director<br />\n";
				}
			}
		} /*if($type == 'Near Miss') {
			$html = '';
			$html .= 'Date : '.$today_date.'<br>Staff : '.get_staff($dbc, $contactid).'<br>Type Of Incident : '.$type;
			if($clientid != '') {
				$html .= '<br>Client : '.get_client($dbc, $clientid);
			}
			$html .= '<br><br><br>';
	        $html .= '
	        <b>Description Of Accident : (Who, What, Where, When, Why - Be As Descriptive As Possible)</b><br>'.$_POST['ir1'].'
	        <br><b>Record Equipment Or Property Damage : </b>'.$_POST['ir2'].'
	        <br><b>Record Cause Of Accident : </b>'.$_POST['ir3'].'
	        <br><b>Corrective Action : </b>'.$_POST['ir4'].'
	        <br><b>Witness(s) Statement : </b>'.$_POST['ir5'].'
	        <br><b>Supervisor Statement & Signoff : </b>'.$_POST['ir6'].'
	        <br><br>
	        <img src="download/sign_'.$incidentreportid.'.png" width="190" height="80" border="0" alt="">
	        ';
	    } else if($type == 'Incident' || $type == 'Vehicle Accident') {
			$html = '';
			$html .= 'Date : '.$today_date.'<br>Staff : '.get_staff($dbc, $contactid).'<br>Type Of Incident : '.$type;
			if($clientid != '') {
				$html .= '<br>Client : '.get_client($dbc, $clientid);
			}
			$html .= '<br><br><br>';
	        $html .= '<b>Injured To Be Taken Care Of And Supervisors/Medical Aid Contacted : </b>'. $_POST['ir7']          .'
	        <br><b>Description Of Accident : </b>(Who, What, Where, When, Why - Be As Descriptive As Possible)<br>'.$_POST['ir1'].'
	        <br><b>Initial Actions Required :  </b>(Reporting, Medical Aid Required, Severity Of Injury)<br>'.$_POST['ir8'].'
	        <br><b>Record Of Injury Involved : </b>'.$_POST['ir9'].'
	        <br><b>Record Equipment Or Property Damage : </b>'.$_POST['ir2'].'
	        <br><b>Interview Witness(s) : </b>'.$_POST['ir10'].'
	        <br><b>Check Background Info : </b>(Equipment, People, Conditions That Would Contribute To The Incident. Were Safety Procedures followed? Was all PPE worn at the time of incident?)<br>'.$_POST['ir11'].'<br>';
	        if($type == 'Incident') {
	                $html .= '<b>Record Length Of Time Working With The Company : </b>(Including Tickets And Training)<br>'.$_POST['ir12'].'<br>';
	        }
	                $html .= '<b>Determine Causes : </b>'.$_POST['ir13'].'<br>';
	        if($type == 'Incident') {
	                $html .= '<b>Corrective Action : </b>'.$_POST['ir4'].'<br>';
	        }
	                $html .= '<b>Follow Up : </b>'.$_POST['ir14'].'<br><b>Managers Review Signature : </b>'.$_POST['ir15'].'<br><br>
	        <img src="download/sign_'.$incidentreportid.'.png" width="190" height="80" border="0" alt="">
	        ';
	    } */

		$pdf->writeHTML($html, true, false, true, false, '');
		if(strip_tags(html_entity_decode($pdf_notes)) != '') {
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 10);
			$pdf->writeHTML(html_entity_decode($pdf_notes));
		}

	    if($keep_revisions == 1 && $revision_number != '') {
	        unlink('download/incident_report_'.$incidentreportid.'_'.$revision_number.'.pdf');
	        $pdf_url = 'download/incident_report_'.$incidentreportid.'_'.$revision_number.'.pdf';
	        $pdf->Output($pdf_url, 'F');
	    } else {
	        unlink('download/incident_report_'.$incidentreportid.'.pdf');
	        $pdf_url = 'download/incident_report_'.$incidentreportid.'.pdf';
	        $pdf->Output($pdf_url, 'F');
	    }
	}

	//Tagged Staff
	mysqli_query($dbc, "UPDATE `contacts_tagging` SET `last_updated_date` = '".date('Y-m-d')."' WHERE `src_table` = 'incident_report' AND `item_id` = '".$incidentreportid.'"');

	//Journal Entry
    $inc_rep_save_journal = get_config($dbc, 'inc_rep_save_journal');
    if($inc_rep_save_journal == 1) {
	    $journal_note = $url.' '.INC_REP_NOUN.': <a href="'.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$incidentreportid.'">'.$type.' #'.$incidentreportid.'</a> at '.date('h:i a').'.';
	    mysqli_query($dbc, "INSERT INTO `daysheet_notepad` (`contactid`, `date`, `notes`) VALUES ('".$_SESSION['contactid']."', '".date('Y-m-d')."', '".htmlentities($journal_note)."')");
    }
    
    // Notification Emails
    $send_report_to = [];
    if($report_options['user_emails'] > 0) {
        $send_report_to[] = get_email($dbc, $_SESSION['contactid']);
    }
    if(!empty($report_options['all_emails'])) {
        $send_report_to[] = $report_options['all_emails'];
    }
    if(!empty($send_report_to)) {
        try {
            send_email('', $send_report_to, '', '', INC_REP_NOUN.' Successfully Submitted', "Thank you for submitting this ".INC_REP_NOUN.". For your reference, a copy has been attached to this email.", (!empty($pdf_url) && file_exists($pdf_url) ? $pdf_url : ''));
        } catch(Exception $e) {
            $log = "Unable to send e-mail to ".implode('; ',$send_report_to).": ".$e->getMessage()."\n";
            mysqli_query($dbc, "UPDATE `incident_report` SET `email_error_log` = CONCAT(`email_error_log`, '$log') WHERE `incidentreportid` = '$incidentreportid'");
        }
    }

	//Follow Up Emails
    if(empty($_POST['incidentreportid'])) {
        $inc_rep_followup_send = get_config($dbc, 'inc_rep_followup_send');
        if(!empty($assign_followup) && $inc_rep_followup_send == 1) {
        	$ir_url = '<a href="'.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$incidentreportid.'">'.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$incidentreportid.'</a>';
        	$ir_pdfurl = '<a href="'.WEBSITE_URL.'/Incident Report/'.$pdf_url.'">'.WEBSITE_URL.'/Incident Report/'.$pdf_url.'</a>';
        	$inc_rep_followup_email = get_config($dbc, 'inc_rep_followup_email');
        	$inc_rep_followup_subject = get_config($dbc, 'inc_rep_followup_subject');
        	$inc_rep_followup_body = html_entity_decode(get_config($dbc, 'inc_rep_followup_body'));
        	$inc_rep_followup_subject = str_replace(['[FOLLOWUPDATE]','[URL]','[PDFURL]'],[$ir14,$ir_url,$ir_pdfurl],$inc_rep_followup_subject);
        	$inc_rep_followup_body = str_replace(['[FOLLOWUPDATE]','[URL]','[PDFURL]'],[$ir14,$ir_url,$ir_pdfurl],$inc_rep_followup_body);
        	$email = get_email($dbc, $assign_followup);
			try {
				send_email($inc_rep_followup_email, $email, '', '', $inc_rep_followup_subject, $inc_rep_followup_body, '');
			} catch(Exception $e) { 
				$log = "Unable to send e-mail to ".get_contact($dbc, $assign_followup).": ".$e->getMessage()."\n";
				mysqli_query($dbc, "UPDATE `incident_report` SET `email_error_log` = CONCAT(`email_error_log`, '$log') WHERE `incidentreportid` = '$incidentreportid'"); }
        }
    }

	if(!isset($_POST['save_ir'])) {
		echo '<script type="text/javascript">window.location.replace("'. $from .'"); </script>';
		echo "<script> $(window.top.document).find('iframe[src*=Ticket]').get(0).contentWindow.reloadTab('view_ticket_incident_reports'); window.parent.reloadTab('view_ticket_incident_reports'); </script>";
	} else {
		echo "<script> active_accordion = '".$_POST['active_accordion']."'; </script>";
		echo '<script type="text/javascript">window.location.replace("?incidentreportid='.$incidentreportid.'&active_accordion='.$_POST['active_accordion'].'&tagging_click='.$_POST['quick_action_tagging'].'&flagging_click='.$_POST['quick_action_flagging'].'"); </script>';
	}
}
?>
<style>
.kbw-signature { width: 400px; height: 200px; }
</style>
<?php 
if(!empty($_GET['incidentreportid'])) {
	$incidentreportid = $_GET['incidentreportid'];
	$type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `type` FROM incident_report WHERE incidentreportid='$incidentreportid'"))['type'];
} else if(!empty($_GET['type'])) {
	$type = $_GET['type'];
}
$is_userform = '';
$user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `user_form_id` FROM `field_config_incident_report` WHERE `row_type`='$type' AND '$type'!=''"))['user_form_id'];
if($user_form_id > 0) {
    $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
    $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    $is_userform = 'increp_';
}
?>
<script type="text/javascript">
  $(document).ready(function() {
	  if(active_accordion != undefined && active_accordion != '') {
		  $('#'+active_accordion).collapse();
		  $('html,body').animate({ scrollTop:$('[href=#'+active_accordion+']').offset().top }, 1000);
	  }
    $("#form1").submit(function( event ) {
        var category = $("#category").val();
        var contactid = $("#contactid").val();
        if (category == '' || contactid == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
    setQuickActions();
    <?php if($_GET['tagging_click'] == 1) { ?>
    	$('.tagging-icon').click();
    <?php } ?>
    <?php if($_GET['flagging_click'] == 1) { ?>
    	$('.flag-icon,.manual-flag-icon').click();
    <?php } ?>
  });

    function changeType(txb) {
        window.location.replace('add_incident_report.php?<?= IFRAME_PAGE ? 'mode=iframe&' : '' ?><?= $_GET['ticketid'] > 0 ? 'ticketid='.$_GET['ticketid'].'&' : '' ?>type='+txb.value);
    }

    function addSignature() {
    	var block = $('.multisign_block').last();
    	var clone = block.clone();
    	block.after(clone);

        var options = {
          drawOnly : true,
          validateFields : false
        };
        $('.sigPad').signaturePad(options);
        $('#linear').signaturePad({drawOnly:true, lineTop:200});
        $('#smoothed').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:200});
        $('#smoothed-variableStrokeWidth').signaturePad({drawOnly:true, drawBezierCurves:true, variableStrokeWidth:true, lineTop:200});
    }
	function addRow(row) {
		destroyInputs();
		var clone = $(row).closest('.form-group').clone();
		clone.find('input,select').val('');
		$(row).closest('.form-group').after(clone);
		initInputs();
	}
	function storePanel() {
		$('[name=active_accordion]').val($('.panel-collapse.in').attr('id'));
	}
	$(document).on('change', 'select[name="<?= $is_userform ?>type"]', function() { changeType(this); });
	$(document).on('change', 'select[name="<?= $is_userform ?>ticketid"]', function() { filterTickets(this); });
	$(document).on('change', 'select[name="<?= $is_userform ?>projectid"]', function() { filterTickets(this); });
	$(document).on('change', 'select[name="<?= $is_userform ?>project_type"]', function() { filterTickets(this); });
	$(document).on('change', 'select[name="<?= $is_userform ?>programid"]', function() { filterMembers(this); });
	function filterTickets(sel) {
		if($(sel).attr('name') == '<?= $is_userform ?>ticketid') {
			var projectid = $(sel).find('option:selected').data('projectid');
			var projecttype = $(sel).find('option:selected').data('projecttype');
			$('select[name="<?= $is_userform ?>projectid"]').val(projectid).trigger('change.select2');
			if(projecttype != '') {
				$('select[name="<?= $is_userform ?>project_type"]').val(projecttype).trigger('change.select2');
			}
		} else if($(sel).attr('<?= $is_userform ?>name') == 'projectid') {
			var projectid = $(sel).val();
			var projecttype = $(sel).find('option:selected').data('projecttype');
			$('select[name="<?= $is_userform ?>ticketid"] option').hide().filter('[data-projectid="'+projectid+'"]').show();
			$('select[name="<?= $is_userform ?>ticketid"]').trigger('change.select2');
			if(projecttype != '') {
				$('select[name="<?= $is_userform ?>project_type"]').val(projecttype).trigger('change.select2');
			}
		} else if($(sel).attr('name') == '<?= $is_userform ?>project_type') {
			var projecttype = $(sel).val();
			if(projecttype != '') {
				$('select[name="<?= $is_userform ?>projectid"] option').hide().filter('[data-projecttype="'+projecttype+'"]').show();
				$('select[name="<?= $is_userform ?>projectid"]').trigger('change.select2');
				$('select[name="<?= $is_userform ?>ticketid"] option').hide().filter('[data-projecttype="'+projecttype+'"]').show();
				$('select[name="<?= $is_userform ?>ticketid"]').trigger('change.select2');
			} else {
				$('select[name="<?= $is_userform ?>projectid"] option').show();
				$('select[name="<?= $is_userform ?>projectid"]').trigger('change.select2');
				$('select[name="<?= $is_userform ?>ticketid"] option').show();
				$('select[name="<?= $is_userform ?>ticketid"]').trigger('change.select2');
			}
		}
	}
	function filterMembers(sel) {
		var programid = $(sel).val();
		$('select[name="<?= $is_userform ?>memberid[]"] option').hide().filter('[data-businessid="'+programid+'"]').show()
		$('select[name="<?= $is_userform ?>memberid[]"]').trigger('change.select2');
	}
  	function setQuickActions() {
		$('.manual-flag-icon').off('click').click(function() {
  			if($('[name="incidentreportid"]').val() != undefined && $('[name="incidentreportid"]').val() > 0) {
  				$('.flag-label-block').addClass('flag_target');
		        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=incident_report&id='+$('[name="incidentreportid"]').val(), 'auto', false, true);
		    } else {
  				$('[name="quick_action_flagging"]').val(1);
  				$('[name="save_ir"]').first().click();
		    }
		});
		$('.flag-icon').off('click').click(function() {
  			if($('[name="incidentreportid"]').val() != undefined && $('[name="incidentreportid"]').val() > 0) {
				var incidentreportid = $('[name="incidentreportid"]').val();
				var item = $('.flag-label-block');
				$.ajax({
					url: 'incident_report_ajax.php?action=quick_actions',
					method: 'POST',
					data: {
						field: 'flag_colour',
		                value: item.data('colour'),
						id: incidentreportid
					},
					success: function(response) {
						item.data('colour',response.substr(0,6));
						item.css('background-color','#'+response.substr(0,6));
						item.find('.flag-label').html(response.substr(6));
					}
				});
			} else {
  				$('[name="quick_action_flagging"]').val(1);
  				$('[name="save_ir"]').first().click();
			}
		});
  		$('.tagging-icon').off('click').click(function() {
  			if($('[name="incidentreportid"]').val() != undefined && $('[name="incidentreportid"]').val() > 0) {
				overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_tagging.php?tile=incident_report&id='+$('[name="incidentreportid"]').val(), 'auto', false, true);
  			} else {
  				$('[name="quick_action_tagging"]').val(1);
  				$('[name="save_ir"]').first().click();
  			}
  			return false;
  		});
  	}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="inc_rep_iframe" src=""></iframe>
        </div>
    </div>
	<div class="row">
	    <?php if($user_form_id > 0) { ?>
	        <h1 style="margin-top: 0; padding-top: 0;"><a href="incident_report.php"><?= INC_REP_TILE ?></a></h1>
	    <?php } ?>
	    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : '' ?>>
			<input type="hidden" name="active_accordion" value="">
			<input type="hidden" name="quick_action_tagging" value="">
			<input type="hidden" name="quick_action_flagging" value="">
	        <?php $type = '';
	        $completed_by = $_SESSION['contactid'];
	        $date_of_happening = '';
	        $date_of_report = date('Y-m-d');
	        $project_type = '';
	        $projectid = '';
	        $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	        $contactid = '';
	        $clientid = '';
	        $programid = '';
	        $memberid = '';
			$other_names = '';
			$location = '';
	        $workerid = '';
	        $revision_number = '';
	        $revision_date = '';
			
	        $ir1 = '';
	        $ir2 = '';
	        $ir3 = '';
	        $ir4 = '';
	        $ir5 = '';
	        $ir6 = '';
	        $ir7 = '';
	        $ir8 = '';
	        $ir9 = '';
	        $ir10 = '';
	        $ir11 = '';
	        $ir12 = '';
	        $ir13 = '';
	        $ir14 = '';
	        $ir15 = '';

	        $happening_lead_up = '';
	        $happening_follow_up = '';
	        $future_considerations = '';
			    
			$equipmentid = '';
			$other_driver_name = '';
			$other_driver_address = '';
			$other_driver_licence = '';
			$other_driver_ins_company = '';
			$other_driver_ins_policy = '';
			$other_owner_name = '';
			$other_owner_address = '';
			$witness_names = '';
			$assign_followup = '';
			$assign_corrective = '';
			
	        $upload_document = '';
			$action_taken = '';
			$follow_up_name = '#*##*#';
			$follow_up_title = 'Parent/Guardian#*#Doctor#*#Other';
			$follow_up_date = '#*##*#';
			$follow_up_who = '#*##*#';
			$recommendations = '';
	        $sign = '';
			$today_date = date('Y-m-d');
			$reported_by = $_SESSION['contactid'];
			$comments = '';
			$supervisor = '';
			$supervisor_sign = '';
			$coordinator = '';
			$coordinator_sign = '';
			$coordinator_comments = '';
			$funder_name = '';
			$funder_contacted = '';
			$incident_date = '';
			$director = '';
			$director_sign = '';

			$multisign = '';
			$pdf_id = '';

	        if(!empty($_GET['type'])) {
	            $type = $_GET['type'];
	        }

	        if(!empty($_GET['incidentreportid'])) {
	            $incidentreportid = $_GET['incidentreportid'];
	            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM incident_report WHERE incidentreportid='$incidentreportid'"));

				$type = $get_contact['type'];
				$completed_by = $get_contact['completed_by'];
				$date_of_happening = $get_contact['date_of_happening'];
				$date_of_report = $get_contact['date_of_report'];
				$project_type = $get_contact['project_type'];
				$projectid = $get_contact['projectid'];
				$ticketid = $get_contact['ticketid'] > 0 ? $get_contact['ticketid'] : $ticketid;
	            $contactid = $get_contact['contactid'];
	            $clientid = $get_contact['clientid'];
	            $programid = $get_contact['programid'];
	            $memberid = $get_contact['memberid'];
				$other_names = $get_contact['other_names'];
				$location = $get_contact['location'];
	            $workerid = $get_contact['workerid'];
	            $revision_number = $get_contact['revision_number'];
	            $revision_date = $get_contact['revision_date'];
				
	            $ir1 = $get_contact['ir1'];
	            $ir2 = $get_contact['ir2'];
	            $ir3 = $get_contact['ir3'];
	            $ir4 = $get_contact['ir4'];
	            $ir5 = $get_contact['ir5'];
	            $ir6 = $get_contact['ir6'];
	            $ir7 = $get_contact['ir7'];
	            $ir8 = $get_contact['ir8'];
	            $ir9 = $get_contact['ir9'];
	            $ir10 = $get_contact['ir10'];
	            $ir11 = $get_contact['ir11'];
	            $ir12 = $get_contact['ir12'];
	            $ir13 = $get_contact['ir13'];
	            $ir14 = $get_contact['ir14'];
	            $ir15 = $get_contact['ir15'];

		        $happening_lead_up = $get_contact['happening_lead_up'];
		        $happening_follow_up = $get_contact['happening_follow_up'];
		        $future_considerations = $get_contact['future_considerations'];
	            
				$equipmentid = $get_contact['equipmentid'];
				$other_driver_name = $get_contact['other_driver_name'];
				$other_driver_address = $get_contact['other_driver_address'];
				$other_driver_licence = $get_contact['other_driver_licence'];
				$other_driver_ins_company = $get_contact['other_driver_ins_company'];
				$other_driver_ins_policy = $get_contact['other_driver_ins_policy'];
				$other_owner_name = $get_contact['other_owner'];
				$other_owner_address = $get_contact['other_address'];
				$witness_names = $get_contact['witness_names'];
				$assign_followup = $get_contact['assign_followup'];
				$assign_corrective = $get_contact['assign_corrective'];
				
				$action_taken = $get_contact['action_taken'];
				$follow_up_name = $get_contact['followup_contact_name'];
				$follow_up_title = $get_contact['followup_contact_title'];
				$follow_up_date = $get_contact['followup_contact_date'];
				$follow_up_who = $get_contact['followup_contact_who'];
				$recommendations = $get_contact['recommendations'];
				$sign = $get_contact['sign'];
				$today_date = $get_contact['today_date'];
				$reported_by = $get_contact['reported_by'];
				$comments = $get_contact['comments'];
				$supervisor = $get_contact['supervisor'];
				$supervisor_sign = $get_contact['supervisor_sign'];
				$coordinator = $get_contact['coordinator'];
				$coordinator_sign = $get_contact['coordinator_sign'];
				$coordinator_comments = $get_contact['coordinator_comments'];
				$funder_name = $get_contact['funder_name'];
				$funder_contacted = $get_contact['funder_contacted'];
				$incident_date = strtotime($get_contact['incident_date']);
				$director = $get_contact['director'];
				$director_sign = $get_contact['director_sign'];

				$multisign = $get_contact['multisign'];
				$pdf_id = $get_contact['pdf_id'];

				$incident_date_date = date('Y-m-d', $incident_date);
				$incident_date_time = date('h:i a', $incident_date);
	            $upload_document = $get_contact['upload_document'];
	            echo '<input type="hidden" name="incidentreportid" value="'.$_GET['incidentreportid'].'" />';
	        }
			
	        $get_type_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `incident_report`, `hide_fields`, `report_info`, `keep_revisions`, `user_form_id` FROM `field_config_incident_report` WHERE `row_type`='$type' AND '$type'!='' UNION SELECT GROUP_CONCAT(`incident_report`), '', '', '', '' FROM `field_config_incident_report` WHERE IFNULL(`incident_report`,'') != ''"));
	        $value_config = ','.$get_type_config['incident_report'].',';
			$hide_config_list = explode('#*#',$get_type_config['hide_fields']);
			$hide_config = ',';
	        $keep_revisions = $get_type_config['keep_revisions'];
			foreach($hide_config_list as $list_fields) {
				$list_fields = explode(':|',$list_fields);
				foreach(explode(',',ROLE) as $mylevel) {
					if($mylevel == $list_fields[0]) {
						$hide_config .= ','.$list_fields[1].',';
					}
				}
			}
			if(empty($type)) {
				$value_config = '';
				$hide_config = '';
			}
			$report_info = $get_type_config['report_info'];
			$user_form_id = $get_type_config['user_form_id'];
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_incident_report WHERE row_type=''"));

	        echo '<input type="hidden" id="sign" name="sign">';
	        
	        if ( isset($_GET['from']) && !empty($_GET['from']) ) {
	            $from = trim(filter_var($_GET['from'], FILTER_SANITIZE_STRING));
	            if ( $from=='safety' ) {
	                $from = '../Safety/incident_report.php?tab=Incident Reports';
	            } else if($from == 'safety_tile') {
					$from = '../Safety/index.php?tab=incident_reports';
				}
	        } else {
	            $from = 'incident_report.php';
	        }

	        $quick_action_html = '<div class="action-icons pull-right">';
            $quick_action_html .= (in_array('flag_manual',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">' : '');
            $quick_action_html .= (!in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">' : '');
	        $quick_action_html .= (in_array('tagging',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/tagging.png" class="inline-img tagging-icon no-toggle" title="Tag Staff">' : '');
	        $quick_action_html .= '</div><div class="clearfix"></div>';
	        ?>
	        
	        <input type="hidden" name="from" value="<?= $from; ?>" />

	        <?php if ($keep_revisions == 1) { ?>
	            <input type="hidden" name="revision_number" value="<?= $revision_number ?>">
	            <input type="hidden" name="revision_date" value="<?= $revision_date ?>">
	        <?php } ?>

	        <?php if($user_form_id > 0) {
	        	echo '<input type="hidden" name="user_form_id" value="'.$user_form_id.'">';
			    $incident_report_form = 1;
		        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
		        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
		        if($user_form_layout == 'Sidebar') {
		        	include('../Incident Report/user_forms_sidebar.php');
		        }
	        	include('../Incident Report/user_forms.php');
		        if($user_form_layout == 'Sidebar') {
		        	echo '</div></div></div>';
		        }
	        } else { ?>
				<h1><?= (empty($_GET['incidentreportid']) ? 'Add '.INC_REP_NOUN.(!empty($type) ? ' - '.$type : '') : 'Edit '.INC_REP_NOUN.' - '.$type) ?>
				<?php if(time() < strtotime($get_contact['flag_start']) || time() > strtotime(str_replace('9999',date('Y'),$get_contact['flag_end']).' + 1 day')) {
					$get_contact['flag_colour'] = '';
				}
				if($get_contact['flag_colour'] != '' && $get_contact['flag_colour'] != 'FFFFFF') {
					$flag_comment = '';
					if(in_array('flag_manual',$quick_action_icons)) {
						$flag_comment = $get_contact['flag_label'];
					} else {
						$ticket_flag_names = [''=>''];
						$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
						foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
							$ticket_flag_names[$colour] = $flag_names[$i];
						}
						$flag_comment = $ticket_flag_names[$$get_contact['flag_colour']];
					} ?>
				<?php } ?>
				<span class="block-label flag-label-block" data-colour="'. $row['flag_colour'].'" style="<?= $get_contact['flag_colour'] != '' && $get_contact['flag_colour'] != 'FFFFFF' ? '' : 'dispaly:none;' ?>background-color:#<?= $get_contact['flag_colour'] ?>;">Flagged<span class="flag-label"><?= empty($flag_comment) ? '' : ': '.$flag_comment ?></span></span></h3>
					<?php if(!IFRAME_PAGE) {
						echo $quick_action_html;
					} ?>
				</h1>
				<?php if(!IFRAME_PAGE) { ?>
					<button type="submit" name="save_ir" id="save_ir" value="Submit" class="btn brand-btn btn-lg pull-right" onclick="storePanel();">Save</button>
					<div class="pad-left gap-top double-gap-bottom"><a href="<?= $from; ?>" class="btn config-btn">Back to Dashboard</a></div>
				<?php } else { ?>
					<a href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a>
					<?php echo $quick_action_html; ?>
				<?php } ?>

		        <div class="panel-group" id="accordion2">

					<?php if(!empty($report_info)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_report_info" >
										<?= INC_REP_NOUN ?> Information<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_report_info" class="panel-collapse collapse">
								<div class="panel-body">
									<?= html_entity_decode($report_info) ?>
								</div>
							</div>
						</div>
					<?php } ?>

					<?php include('add_incident_report_fields.php'); ?>
					
					<?php if($get_field_config['pdf_notes'] != '') { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
										Description<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_17" class="panel-collapse collapse">
								<div class="panel-body">
									<?= html_entity_decode($get_field_config['pdf_notes']) ?>
								</div>
							</div>
						</div>
					<?php } ?>

		        </div>

		        <div class="form-group">
		            <div class="col-sm-12">
						<?php if(!IFRAME_PAGE) { ?>
							<a href="<?= $from; ?>" class="btn brand-btn btn-lg">Back</a>
							<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
						<?php } else { ?>
							<a href="../blank_loading_page.php" class="btn brand-btn btn-lg">Cancel</a>
						<?php } ?>
			            <button type="submit" name="add_ir" id="add_ir" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			            <button type="submit" name="save_ir" id="save_ir" value="Submit" class="btn brand-btn btn-lg pull-right" onclick="storePanel();">Save</button>
			        </div>
		        </div>
	        <?php } ?>

	    </form>

    </div>
</div>

<?php include ('../footer.php'); ?>
