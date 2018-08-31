 <?php
    $today_date = $_POST['today_date'];
    $contactid = $_SESSION['contactid'];

    $today_time = filter_var($_POST['today_time'],FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'],FILTER_SANITIZE_STRING);
    $fields = implode(',',$_POST['fields']);

    $fields_value = '**FFM****FFM****FFM****FFM**';
    for($i=4; $i<=9; $i++) {
        $fields_value .= filter_var($_POST['fields_value_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }
    $fields_value = filter_var(htmlentities($fields_value),FILTER_SANITIZE_STRING);

	$incident = '';
    for($i=0; $i<=58; $i++) {
        $incident .= filter_var($_POST['incident_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }
    $incident = filter_var(htmlentities($incident),FILTER_SANITIZE_STRING);

	$accident = '';
    for($i=0; $i<=36; $i++) {
        $accident .= filter_var($_POST['accident_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }
    $accident = filter_var(htmlentities($accident),FILTER_SANITIZE_STRING);

	$total_task = count($_POST['task']);
    $all_task = '';
    for($i=0; $i<$total_task; $i++) {
        if($_POST['task'][$i] != '') {
            $all_task .= $_POST['task'][$i].'**'.$_POST['hazard'][$i].'**'.$_POST['level'][$i].'**'.$_POST['plan'][$i].'**##**';
        }
    }
    $all_task = filter_var($all_task,FILTER_SANITIZE_STRING);

    $person_in_charge = filter_var($_POST['person_in_charge'],FILTER_SANITIZE_STRING);
    $reporter = filter_var($_POST['reporter'],FILTER_SANITIZE_STRING);
    $reported_to = filter_var($_POST['reported_to'],FILTER_SANITIZE_STRING);
    $date_reported = filter_var($_POST['date_reported'],FILTER_SANITIZE_STRING);
    $description_of_incident = filter_var(htmlentities($_POST['description_of_incident']),FILTER_SANITIZE_STRING);
    $direct_cause_of_incident = filter_var(htmlentities($_POST['direct_cause_of_incident']),FILTER_SANITIZE_STRING);
    $contributing_factor = filter_var(htmlentities($_POST['contributing_factor']),FILTER_SANITIZE_STRING);
    $over_the_cause = filter_var($_POST['over_the_cause'],FILTER_SANITIZE_STRING);
    $imm_act_req = filter_var(htmlentities($_POST['imm_act_req']),FILTER_SANITIZE_STRING);
    $ltm_act_req = filter_var(htmlentities($_POST['ltm_act_req']),FILTER_SANITIZE_STRING);
    $immidiate_correcctive_act_req = filter_var($_POST['immidiate_correcctive_act_req'],FILTER_SANITIZE_STRING);
    $immi_date_comp = filter_var($_POST['immi_date_comp'],FILTER_SANITIZE_STRING);
    $long_trm_act_assign = filter_var($_POST['long_trm_act_assign'],FILTER_SANITIZE_STRING);
    $long_term_date = filter_var($_POST['long_term_date'],FILTER_SANITIZE_STRING);
    $dia_scene = filter_var(htmlentities($_POST['dia_scene']),FILTER_SANITIZE_STRING);

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];
	$desc = filter_var(htmlentities($_POST['desc']),FILTER_SANITIZE_STRING);
	$desc1 = filter_var(htmlentities($_POST['desc1']),FILTER_SANITIZE_STRING);

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_incident_investigation_report` (`safetyid`, `today_date`, `contactid`, `today_time`, `address`, `fields`, `fields_value`, `person_in_charge`, `reporter`, `reported_to`, `date_reported`, `description_of_incident`, `direct_cause_of_incident`, `contributing_factor`, `over_the_cause`, `imm_act_req`, `ltm_act_req`, `immidiate_correcctive_act_req`, `immi_date_comp`, `long_trm_act_assign`, `long_term_date`, `dia_scene`, `incident`, `desc`, `desc1`, `all_task`, `accident`, `attendance_staff`, `attendance_extra`) VALUES	('$safetyid', '$today_date', '$contactid', '$today_time', '$address', '$fields', '$fields_value', '$person_in_charge', '$reporter', '$reported_to', '$date_reported', '$description_of_incident', '$direct_cause_of_incident', '$contributing_factor', '$over_the_cause', '$imm_act_req', '$ltm_act_req', '$immidiate_correcctive_act_req', '$immi_date_comp', '$long_trm_act_assign', '$long_term_date', '$dia_scene', '$incident', '$desc', '$desc1', '$all_task', '$accident', '$attendance_staff', '$attendance_extra')";

        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $fieldlevelriskid = mysqli_insert_id($dbc);

        $attendance_staff_each = $_POST['attendance_staff'];
        for($i = 0; $i < count($_POST['attendance_staff']); $i++) {
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$attendance_staff_each[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        for($i=1;$i<=$attendance_extra;$i++) {
            $att_ex = 'Extra '.$i;
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$att_ex')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $tab = get_safety($dbc, $safetyid, 'tab');
        if($tab == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 1)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            include ('incident_investigation_report_pdf.php');
            echo incident_investigation_report_pdf($dbc,$safetyid, $fieldlevelriskid);
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
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `safety_incident_investigation_report` SET `contactid` = '$contactid', `today_time` = '$today_time', `address` = '$address', `fields` = '$fields', `fields_value` = '$fields_value', `person_in_charge` = '$person_in_charge', `reporter` = '$reporter', `reported_to` = '$reported_to', `date_reported` = '$date_reported', `description_of_incident` = '$description_of_incident', `direct_cause_of_incident` = '$direct_cause_of_incident', `contributing_factor` = '$contributing_factor', `over_the_cause` = '$over_the_cause', `imm_act_req` = '$imm_act_req', `ltm_act_req` = '$ltm_act_req', `immidiate_correcctive_act_req` = '$immidiate_correcctive_act_req', `immi_date_comp` = '$immi_date_comp', `long_trm_act_assign` = '$long_trm_act_assign', `long_term_date` = '$long_term_date', `dia_scene` = '$dia_scene', `incident` = '$incident', `desc` = '$desc', `desc1` = '$desc1', `accident` = '$accident', `all_task` = CONCAT(all_task,'$all_task') WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];
                $staffcheck = implode('*#*',$_POST['staffcheck_'.$assign_staff_id]);

                $img = sigJsonToImage($sign);
                imagepng($img, 'incident_investigation_report/download/safety_'.$assign_staff_id.'.png');

                $assign_staff = filter_var($_POST['assign_staff_'.$assign_staff_id],FILTER_SANITIZE_STRING);

                if($assign_staff != '') {
                    $query_update_employee = "UPDATE `safety_attendance` SET `assign_staff` = '$assign_staff', `staffcheck` = '$staffcheck', `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                } else {
                    $query_update_employee = "UPDATE `safety_attendance` SET `staffcheck` = '$staffcheck', `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                }
            }
        }

        $before_change = '';
        $history = "safety_attendance entry has been updated for safetyattid $assign_staff_id <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE	fieldlevelriskid='$fieldlevelriskid' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            include ('incident_investigation_report_pdf.php');
            echo incident_investigation_report_pdf($dbc,$safetyid, $fieldlevelriskid);
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        }
    }

    if($url_redirect == '' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
        $url_redirect = 'index.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
    } else if($url_redirect == '') {
        $url_redirect = 'add_manual.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
	}

    if($field_level_hazard == 'field_level_hazard_save') {
        echo '<script type="text/javascript">  window.location.replace("safety.php?tab='.$get_manual['tab'].'&category='.$get_manual['category'].'"); </script>';
    } else {
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
