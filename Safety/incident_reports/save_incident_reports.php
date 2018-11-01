<?php
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
if(!empty($_POST['field_level_hazard'])) {
    $safety_contactid = $_SESSION['contactid'];
    $url_redirect = '';

    $type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
    $contactid = filter_var(implode(',',$_POST['contactid']),FILTER_SANITIZE_STRING);
    $clientid = filter_var(implode(',',$_POST['clientid']),FILTER_SANITIZE_STRING);
    $other_names = filter_var($_POST['other_names'],FILTER_SANITIZE_STRING);
    $incident_date = date('Y-m-d H:i:s', strtotime($_POST['incident_date_date'].' '.$_POST['incident_date_time']));
    $location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
    $workerid = filter_var(implode(',',$_POST['workerid']),FILTER_SANITIZE_STRING);
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
    $ir5 = filter_var(htmlentities($_POST['ir5']),FILTER_SANITIZE_STRING);
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

    $equipmentid = filter_var(implode(',',$_POST['equipmentid']),FILTER_SANITIZE_STRING);
    $other_driver_name = filter_var(htmlentities($_POST['other_driver_name']),FILTER_SANITIZE_STRING);
    $other_driver_address = filter_var(htmlentities($_POST['other_driver_address']),FILTER_SANITIZE_STRING);
    $other_driver_licence = filter_var(htmlentities($_POST['other_driver_licence']),FILTER_SANITIZE_STRING);
    $other_driver_ins_company = filter_var(htmlentities($_POST['other_driver_ins_company']),FILTER_SANITIZE_STRING);
    $other_driver_ins_policy = filter_var(htmlentities($_POST['other_driver_ins_policy']),FILTER_SANITIZE_STRING);
    $other_owner_name = filter_var(htmlentities($_POST['other_owner_name']),FILTER_SANITIZE_STRING);
    $other_owner_address = filter_var(htmlentities($_POST['other_owner_address']),FILTER_SANITIZE_STRING);
    $witness_names = filter_var(htmlentities($_POST['witness_names']),FILTER_SANITIZE_STRING);
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

    $complete_pdf = 0;
    $sign = $_POST['output'];

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    if (!file_exists('../Incident Report/download')) {
        mkdir('../Incident Report/download', 0777, true);
    }

    if($_FILES["upload_document"]["name"] != '') {
        $upload_document = implode('#$#', $_FILES["upload_document"]["name"]);
    } else {
        $upload_document = '';
    }

    $upload_document = htmlspecialchars($upload_document, ENT_QUOTES);

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "../Incident Report/download/".$_FILES["upload_document"]["name"][$i]) ;
    }

    if(empty($_POST['incidentreportid'])) {
        $reported_by = $_SESSION['contactid'];
        $today_date = date('Y-m-d');

        $query_insert_vendor = "INSERT INTO `incident_report` (`type`, `contactid`, `clientid`, `other_names`, `location`, `ir1`, `ir2`, `ir3`, `ir4`, `ir5`, `ir6`, `ir7`, `ir8`, `ir9`, `ir10`, `ir11`, `ir12`, `ir13`, `upload_document`, `ir14`, `ir15`, `equipmentid`, `other_driver_name`, `other_driver_address`, `other_driver_licence`, `other_driver_ins_company`, `other_driver_ins_policy`, `witness_names`, `assign_followup`, `assign_corrective`, `action_taken`, `followup_contact_name`, `followup_contact_title`, `followup_contact_date`, `followup_contact_who`, `recommendations`, `sign`, `today_date`, `reported_by`, `coordinator_comments`, `funder_name`, `funder_contacted`, `incident_date`, `comments`, `workerid`, `safetyid`, `attendance_staff`, `attendance_extra`, `status`, `safety_contactid`)
            VALUES ('$type', '$contactid', '$clientid', '$other_names', '$location', '$ir1', '$ir2', '$ir3',  '$ir4', '$ir5', '$ir6', '$ir7', '$ir8', '$ir9', '$ir10', '$ir11', '$ir12', '$ir13', '$upload_document', '$ir14', '$ir15', '$equipmentid', '$other_driver_name', '$other_driver_address', '$other_driver_licence', '$other_driver_ins_company', '$other_driver_ins_policy', '$witness_names', '$assign_followup', '$assign_corrective', '$action_taken', '$follow_up_name', '$follow_up_title', '$follow_up_date', '$follow_up_who', '$recommendations', '$sign', '$today_date', '$reported_by', '$coordinator_comments', '$funder_name', '$funder_contacted', '$incident_date', '$comments' ,'$workerid', '$safetyid', '$attendance_staff', '$attendance_extra', 'New', '$safety_contactid')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $incidentreportid = mysqli_insert_id($dbc);
        $img = sigJsonToImage($sign);
        imagepng($img, '../Incident Report/download/sign_'.$incidentreportid.'_reporting.png');
        $url = 'Added';

        $attendance_staff_each = $_POST['attendance_staff'];
        for($i = 0; $i < count($_POST['attendance_staff']); $i++) {
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$incidentreportid', '$attendance_staff_each[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        for($i=1;$i<=$attendance_extra;$i++) {
            $att_ex = 'Extra '.$i;
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$incidentreportid', '$att_ex')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $tab = get_safety($dbc, $safetyid, 'tab');
        if($tab == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$incidentreportid', '$assign_staff', 1)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            $complete_pdf = 1;
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        } else if($tab == 'Toolbox' || $tab == 'Tailgate') {
            $assign_staff = 'Organizer: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 0)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $before_change = '';
        $history = "Safety attendance entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);
    } else {
        $incidentreportid = $_POST['incidentreportid'];
        if($upload_document != '') {
            $upload_document = $_POST['upload_document_current'].'#$#'.$upload_document;
        } else {
            $upload_document = $_POST['upload_document_current'];
        }
        $query_update_vendor = "UPDATE `incident_report` SET `type` = '$type', `contactid` = '$contactid', `clientid`='$clientid', `other_names`='$other_names', `location`='$location', `ir1` = '$ir1', `ir2` = '$ir2', `ir3` = '$ir3', `ir4` = '$ir4', `ir5` = '$ir5', `ir6` = '$ir6', `ir7` = '$ir7', `ir8` = '$ir8', `ir9` = '$ir9', `ir10` = '$ir10', `ir11` = '$ir11', `ir12` = '$ir12', `ir13` = '$ir13', `upload_document` = '$upload_document', `ir14` = '$ir14', `ir15` = '$ir15', `equipmentid` = '$equipmentid', `other_driver_name` = '$other_driver_name', `other_driver_address` = '$other_driver_address', `other_driver_licence` = '$other_driver_licence', `other_driver_ins_company` = '$other_driver_ins_company', `other_driver_ins_policy` = '$other_driver_ins_policy', `witness_names` = '$witness_names',  `action_taken`='$action_taken', `followup_contact_name`='$follow_up_name', `followup_contact_title`='$follow_up_title', `followup_contact_date`='$follow_up_date', `followup_contact_who`='$follow_up_who', `recommendations`='$recommendations', `sign` = '$sign', `coordinator_comments`='$coordinator_comments', `funder_name`='$funder_name', `funder_contacted`='$funder_contacted', `incident_date`='$incident_date', `assign_followup`='$assign_followup', `assign_corrective`='$assign_corrective', `comments`='$comments', `workerid` = '$workerid' WHERE `incidentreportid` = '$incidentreportid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid`='$incidentreportid'"));
        $reported_by = $row['reported_by'];
        $today_date = $row['today_date'];

        $img = sigJsonToImage($sign);
        imagepng($img, '../Incident Report/download/sign_'.$incidentreportid.'_reporting.png');
        $url = 'Updated';

        $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$incidentreportid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];
                $staffcheck = implode('*#*',$_POST['staffcheck_'.$assign_staff_id]);

                $img = sigJsonToImage($sign);
                imagepng($img, '../Incident Report/download/safety_'.$incidentreportid.'_'.$assign_staff_id.'.png');

                $assign_staff = filter_var($_POST['assign_staff_'.$assign_staff_id],FILTER_SANITIZE_STRING);

                if($assign_staff != '') {
                    $query_update_employee = "UPDATE `safety_attendance` SET `assign_staff` = '$assign_staff', `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                } else {
                    $query_update_employee = "UPDATE `safety_attendance` SET `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                }
            }
        }

        $before_change = '';
        $history = "safety_attendance entry has been updated for safetyattid $assign_staff_id <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE  fieldlevelriskid='$incidentreportid' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            $complete_pdf = 1;
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        }
    }
    if($supervisor_sign != '') {
        $img = sigJsonToImage($supervisor_sign);
        imagepng($img, '../Incident Report/download/sign_'.$incidentreportid.'_supervisor.png');
        $result = mysqli_query($dbc, "UPDATE `incident_report` SET `supervisor_sign`='$supervisor_sign', `supervisor`='$supervisor' WHERE `incidentreportid`='$incidentreportid'");
    }
    if($coordinator_sign != '') {
        $img = sigJsonToImage($coordinator_sign);
        imagepng($img, '../Incident Report/download/sign_'.$incidentreportid.'_coordinator.png');
        $result = mysqli_query($dbc, "UPDATE `incident_report` SET `coordinator_sign`='$coordinator_sign', `coordinator`='$coordinator' WHERE `incidentreportid`='$incidentreportid'");
    }
    if($director_sign != '') {
        $img = sigJsonToImage($director_sign);
        imagepng($img, '../Incident Report/download/sign_'.$incidentreportid.'_director.png');
        $result = mysqli_query($dbc, "UPDATE `incident_report` SET `director_sign`='$director_sign', `director`='$director' WHERE `incidentreportid`='$incidentreportid'");
    }

    $pdf_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_incident_report` WHERE `incident_report_dashboard` IS NOT NULL"));
    $current_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `incident_report`, `report_info` FROM `field_config_incident_report` WHERE `row_type`='$type' UNION SELECT GROUP_CONCAT(`incident_report`), '' FROM `field_config_incident_report` WHERE IFNULL(`incident_report`,'') != ''"));
    $pdf_logo = (empty($pdf_config['pdf_logo']) ? WEBSITE_URL.'/img/ffm-logo-support.png' : '../Incident Report/download/'.$pdf_config['pdf_logo']);
    $pdf_header = $pdf_config['pdf_header'];
    $pdf_footer = $pdf_config['pdf_footer'];
    $pdf_title = $pdf_config['pdf_title'];
    $pdf_start = $current_config['report_info'];
    $pdf_notes = $pdf_config['pdf_notes'];
    $value_config = ','.$current_config['incident_report'].',';
    DEFINE(PDF_LOGO,$pdf_logo);
    DEFINE(PDF_HEADER,html_entity_decode($pdf_header));
    DEFINE(PDF_TITLE,html_entity_decode($pdf_title));
    DEFINE(PDF_FOOTER,html_entity_decode($pdf_footer));

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            $image_file = PDF_LOGO;
            $this->Image($image_file, 10, 10, 60, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
            $this->SetY(0);
            $this->WriteHTMLCell(0, 0, 70, 10, PDF_HEADER, 0, 0, 0, true, 'R');
            list($img_width, $img_height, $img_type, $attr) = getimagesize(PDF_LOGO);
            $img_height /= ($img_width / 60);
            $this->SetFont('helvetica', '', 13);
            $this->SetY($img_height + 10);
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
        foreach(explode(',',$get_field_config['incident_types']) as $in_type) {
            $type_list .= '<input type="radio" name="incident_type" '.($in_type == $type ? 'checked="checked"' : '').' value="'.preg_replace('/[^a-zA-Z]/','',$in_type).'"> <b>'.$in_type.'</b> ';
        }
        $html = '<form action="" method="POST" enctype="multipart/form-data">';
        $html = '<p style="text-align:center;">'.$type_list."</p>";
        $html .= '<table borders="0" width="100%" cellspacing="5">
            <tr><td><br />Date of Incident:</td><td style="border-bottom:1px solid black">'.date('Y-m-d',strtotime($incident_date)).'</td>
                <td style="border-bottom:none;">Time:</td><td style="border-bottom:1px solid black">'.date('g:i a',strtotime($incident_date)).'</td></tr>
            <tr><td><br />Location of Incident:</td><td colspan="3" style="border-bottom:1px solid black">'.$location.'</td></tr>
            <tr><td><br />Client(s) Involved:</td><td colspan="3" style="border-bottom:1px solid black">';
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
            <tr><td colspan="4">'.$ir2.'<br /><br /><br /></td></tr>
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
            <tr><td colspan="2"><img src="../Incident Report/download/sign_'.$incidentreportid.'_reporting.png" width="100"></td>
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
            <tr><td colspan="2">'.(empty($supervisor) ? '<br /><br /><br />' : '<img src="../Incident Report/download/sign_'.$incidentreportid.'_supervisor.png" width="100">').'</td><td colspan="2">'.$supervisor.'</td></tr>
            <tr><td colspan="2" align="center" style="border-top:thin solid black">Supervisor\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>
            <tr><td colspan="2">'.(empty($coordinator) ? '<br /><br /><br />' : '<img src="../Incident Report/download/sign_'.$incidentreportid.'_coordinator.png" width="100">').'</td><td colspan="2">'.$coordinator.'</td></tr>
            <tr><td colspan="2" align="center" style="border-top:thin solid black">Coordinator\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>';
        $html .= '<tr><td colspan="4">For Critical Incidents only:</td></tr>
            <tr><td><br />Funder Contacted:</td><td style="border-bottom:thin solid black">'.$funder_name.'</td><td><br />Date:</td><td style="border-bottom:thin solid black">'.$funder_contacted.'</td></tr>';
        $html .= '<tr><td colspan="2">'.(empty($director) ? '<br /><br /><br />' : '<img src="../Incident Report/download/sign_'.$incidentreportid.'_director.png" width="100">').'</td><td colspan="2">'.$director.'</td></tr>
            <tr><td colspan="2" align="center" style="border-top:thin solid black">Director\'s Signature</td><td colspan="2" align="center" style="border-top:thin solid black">Date</td></tr>
            </table></form>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $html = '';
    } else {
        $html = html_entity_decode($pdf_start);
        $html .= "<h3>$type</h3>\n";
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
        if (strpos($value_config, ','."Equipment".',') !== FALSE) {
            $html .= "<b>Equipment Involved:</b> ";
            foreach(explode(',',$equipmentid) as $equipid) {
                $equip = mysqli_fetch_array(mysqli_query($dbc, "SELECT `equipmentid`,`unit_number`,`make`,`model`,`licence_plate` FROM `equipment` WHERE `equipmentid`='$equipid'"));
                $html .= $equip['make'].' '.$equip['model'].' Unit #'.$equip['unit_number'].' (Licence Plate '.$equip['licence_plate'].")<br />\n";
            }
        }
        if (strpos($value_config, ','."Driver".',') !== FALSE) {
            $html .= "<b>Driver:</b> ";
            $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$contactid'"));
            $html .= decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (Licence #'.$row['license'].', Address: ';
            $html .= (!empty($row['address']) ? $row['address'] : (!empty($row['mailing_address']) ? $row['mailing_address'] : (!empty($row['business_address']) ? $row['business_address'] : $row['ship_to_address']))).')<br />';
        } else if (strpos($value_config, ','."Staff".',') !== FALSE) {
            $html .= "<b>Staff:</b> ".get_contact($dbc, $contactid)."<br />\n";
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
        $html .= "<b>Date Of Incident:</b> $incident_date_date<br />\n";
        $html .= "<b>Time Of Incident:</b> $incident_date_time<br />\n";
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
            $html .= html_entity_decode($action_taken);
        }
        if (strpos($value_config, ','."Record Of Injury Involved".',') !== FALSE) {
            $html .= "<b>Record Of Injury Involved:</b><br />\n";
            $html .= html_entity_decode($ir9);
        }
        if (strpos($value_config, ','."Determine Causes".',') !== FALSE) {
            $html .= "<b>Determine Causes (Direct/Indirect/Root):</b><br />\n";
            $html .= html_entity_decode(str_replace('#*#','',$ir13));
        }
        if (strpos($value_config, ','."Supply Pictures".',') !== FALSE) {
            $html .= "<b>Pictures</b><br />";
            foreach(explode('#$#', $upload_document) as $picture) {
                $file_type = strtolower(pathinfo($venin, PATHINFO_EXTENSION));
                if($file_type == 'pdf') {
                    try {
                        exec('gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o "../Incident Report/download/field_invoice/'.$venin.'.png" "../Incident Report/download/field_invoice/'.$venin.'"');
                        $venin .= '.png';
                        $file_type = 'png';
                    } catch(Exception $e) { }
                }
                $file_type = strtolower(pathinfo($picture, PATHINFO_EXTENSION));
                if($file_type == 'jpg' || $file_type == 'jpeg' || $file_type == 'bmp' || $file_type == 'gif' || $file_type == 'png') {
                    $html .= '<img src="../Incident Report/download/'.$picture."\" /><br />\n";
                }
            }
        }
        if (strpos($value_config, ','."Recommendations".',') !== FALSE) {
            $html .= "<b>Recommendations on how to correct or avoid recurrence of this type of accident or incident:</b><br />\n";
            $html .= html_entity_decode($recommendations);
        }
        $html .= "<b>Reported:</b> ".get_contact($dbc, $reported_by).': '.$today_date."<br />\n";
        $html .= '<img src="../Incident Report/download/sign_'.$incidentreportid.'_reporting.png" width="190" height="80" border="0" alt=""><br />';
        if (strpos($value_config, ','."Comments".',') !== FALSE) {
            $html .= "<b>Comments:</b><br />\n";
            $html .= html_entity_decode($comments);
        }
        if (strpos($value_config, ','."Supervisor Statement & Signoff".',') !== FALSE) {
            $html .= "<b>Supervisor Statement:</b><br />\n";
            $html .= html_entity_decode($ir6);
            if($supervisor_sign != '') {
                $html .= "<b>Supervisor Signature:<br />\n";
                $html .= '<img src="../Incident Report/download/sign_'.$incidentreportid.'_supervisor.png" width="190" height="80" border="0" alt=""><br />';
                $html .= "<b>Date:</b> $supervisor<br />\n";
            }
        }
        if (strpos($value_config, ','."Coordinator Statement & Signoff".',') !== FALSE) {
            $html .= "<b>Coordinator Statement:</b><br />\n";
            $html .= html_entity_decode($ir6);
            if($supervisor_sign != '') {
                $html .= "<b>Coordinator Signature:<br />\n";
                $html .= '<img src="../Incident Report/download/sign_'.$incidentreportid.'_coordinator.png" width="190" height="80" border="0" alt=""><br />';
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
            if($supervisor_sign != '') {
                $html .= "<b>Director Signature:<br />\n";
                $html .= '<img src="../Incident Report/download/sign_'.$incidentreportid.'_director.png" width="190" height="80" border="0" alt=""><br />';
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
                $html .= '<img src="../Incident Report/download/sign_'.$incidentreportid.'.png" width="190" height="80" border="0" alt=""><br />';
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

    $sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$incidentreportid' AND safetyid='$safetyid'");

    $html .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        $staffcheck = $row_sa['staffcheck'];

        $html .= '<tr nobr="true">';
        $html .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';

        // avs_near_miss = form name

        $html .= '<td data-title="Email"><img src="../Incident Report/download/safety_'.$incidentreportid.'_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $html .= '</tr>';
    }
    $html .= '</table>';

    if($url_redirect == '' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
        $url_redirect = 'index.php?safetyid='.$safetyid.'&action=view&formid='.$incidentreportid.'';
    } else if($url_redirect == '') {
        $url_redirect = 'add_manual.php?safetyid='.$safetyid.'&action=view&formid='.$incidentreportid.'';
	}

    $pdf->writeHTML($html, true, false, true, false, '');
    if(strip_tags(html_entity_decode($pdf_notes)) != '') {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTML(html_entity_decode($pdf_notes));
    }
    unlink('../Incident Report/download/incident_report_'.$incidentreportid.'.pdf');
    $pdf->Output('../Incident Report/download/incident_report_'.$incidentreportid.'.pdf', 'F');

    if($complete_pdf == 1) {
        $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$incidentreportid' AND safetyid='$safetyid'");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            // avs_near_miss = form name
            unlink('../Incident Report/download/safety_'.$incidentreportid.'_'.$assign_staff_id.'.png');
        }
        mysqli_query($dbc, "UPDATE `incident_report` SET `status`='Done' WHERE `incidentreportid`='$incidentreportid'");
    }

    if(IFRAME_PAGE && strpos($url_redirect, 'reports') !== FALSE) {
        echo '<script type="text/javascript">
        top.window.location.replace("'.$url_redirect.'"); </script>';
    } else {
        if(IFRAME_PAGE) {
            $url_redirect .= '&mode=iframe';
        }
        echo '<script type="text/javascript">
        window.location.replace("'.$url_redirect.'"); </script>';
    }
}
