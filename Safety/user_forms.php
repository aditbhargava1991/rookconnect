<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['field_level_hazard'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');

    $today_date = $_POST['safety_today_date'];
    $contactid = $_SESSION['contactid'];

    $form_id = $_POST['form_id'];
    $assign_id = $_POST['assign_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");

    $url_redirect = '';
    $complete_pdf = 0;

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    if(empty($_POST['fieldlevelriskid'])) {
        $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`, `safetyid`, `today_date`, `contactid`, `attendance_staff`, `attendance_extra`, `status`) VALUES ('$form_id', '$user_id', '$safetyid', '$today_date', '$contactid', '$attendance_staff', '$attendance_extra', 'New')");
        $pdf_id = mysqli_insert_id($dbc);

        $attendance_staff_each = $_POST['attendance_staff'];
        for($i = 0; $i < count($_POST['attendance_staff']); $i++) {
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$pdf_id', '$attendance_staff_each[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        for($i=1;$i<=$attendance_extra;$i++) {
            $att_ex = 'Extra '.$i;
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$pdf_id', '$att_ex')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $tab = get_safety($dbc, $safetyid, 'tab');
        if($tab == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$pdf_id', '$assign_staff', 1)";
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
        $history = "Safety upload entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);
    } else {
        $pdf_id = $_POST['fieldlevelriskid'];

        $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$pdf_id' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];
                $staffcheck = implode('*#*',$_POST['staffcheck_'.$assign_staff_id]);

                $img = sigJsonToImage($sign);
                imagepng($img, 'download/user_form_'.$form_id.'_'.$assign_staff_id.'.png');

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

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE  fieldlevelriskid='$pdf_id' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            $complete_pdf = 1;
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        }
    }
    if(mysqli_num_rows($result)) {
        $assign_id = mysqli_fetch_array($result)['assign_id'];
        mysqli_query($dbc, "UPDATE `user_form_assign` SET `completed_date`=CURRENT_TIMESTAMP, `pdf_id`='$pdf_id' WHERE `assign_id`='$assign_id'");
    } else {
        mysqli_query($dbc, "INSERT INTO `user_form_assign` (`form_id`, `user_id`, `completed_date`, `pdf_id`) VALUES ('$form_id', '$user_id', CURRENT_TIMESTAMP, '$pdf_id')");
        $assign_id = mysqli_insert_id($dbc);
    }

    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
    $pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_'.$assign_id.'.pdf';
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");

    $safety_projectid = $_POST['safety_projectid'];
    $safety_siteid = $_POST['safety_siteid'];
    $safety_ticketid = $_POST['safety_ticketid'];
    $safety_clientid = $_POST['safety_clientid'];
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `safety_projectid` = '$safety_projectid', `safety_siteid` = '$safety_siteid', `safety_ticketid` = '$safety_ticketid', `safety_clientid` = '$safety_clientid' WHERE `pdf_id` = '$pdf_id'");

    include('../Form Builder/generate_form_pdf.php');

    $sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$pdf_id' AND safetyid='$safetyid'");

    $pdf_text .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $pdf_text .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        $staffcheck = $row_sa['staffcheck'];

        $pdf_text .= '<tr nobr="true">';
        $pdf_text .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';

        // avs_near_miss = form name

        $pdf_text .= '<td data-title="Email"><img src="download/user_form_'.$form_id.'_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $pdf_text .= '</tr>';
    }
    $pdf_text .= '</table>';

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');

    if($url_redirect == '' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
        $url_redirect = 'index.php?safetyid='.$safetyid.'&action=view&formid='.$pdf_id.'';
    } else if($url_redirect == '') {
        $url_redirect = 'add_manual.php?safetyid='.$safetyid.'&action=view&formid='.$pdf_id.'';
	}

    if ($complete_pdf == 1) {
        if(!file_exists('download')) {
            mkdir('download', 0777, true);
        }
        $pdf->Output('download/'.$pdf_name, 'F');

        $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$pdf_id' AND safetyid='$safetyid'");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            // avs_near_miss = form name
            unlink('download/user_form_'.$form_id.'_'.$assign_staff_id.'.png');
        }
        mysqli_query($dbc, "UPDATE `user_form_pdf` SET `status`='Done' WHERE `pdf_id`='$pdf_id'");
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
} else {
    $today_date = date('Y-m-d');
    $contactid = $_SESSION['contactid'];
    if(!empty($_GET['formid'])) {
        $pdf_id = $_GET['formid'];
        echo '<input type="hidden" name="fieldlevelriskid" value="'.$pdf_id.'">';
        $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_form_pdf WHERE pdf_id='$pdf_id'"));
        $today_date = $get_field_level['today_date'];
        $contactid = $get_field_level['contactid'];
    }
    $form_id = $user_form_id;

    include('../Form Builder/generate_form_contents.php');
} ?>
<div class="form-group">
    <p><span class="hp-red"><em>Required Fields *</em></span></p>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('[name="field_level_hazard"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>
