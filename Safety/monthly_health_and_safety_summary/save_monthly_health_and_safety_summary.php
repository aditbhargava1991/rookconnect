 <?php
    $today_date = date('Y-m-d');
    $contactid = $_SESSION['contactid'];

    $period_ending = filter_var($_POST['period_ending'],FILTER_SANITIZE_STRING);
    $summary_type = filter_var($_POST['summary_type'],FILTER_SANITIZE_STRING);
    $workers = filter_var($_POST['workers'],FILTER_SANITIZE_STRING);
    $comp_orent = filter_var($_POST['comp_orent'],FILTER_SANITIZE_STRING);
    $toolbox_meeting = filter_var($_POST['toolbox_meeting'],FILTER_SANITIZE_STRING);
    $conducetd_number = filter_var($_POST['conducetd_number'],FILTER_SANITIZE_STRING);
    $per_attendance = filter_var($_POST['per_attendance'],FILTER_SANITIZE_STRING);
    $inspection_schd = filter_var($_POST['inspection_schd'],FILTER_SANITIZE_STRING);
    $comp_num = filter_var($_POST['comp_num'],FILTER_SANITIZE_STRING);
    $unsafe_acts = filter_var($_POST['unsafe_acts'],FILTER_SANITIZE_STRING);
    $corrected_num = filter_var($_POST['corrected_num'],FILTER_SANITIZE_STRING);
    $outstanding_num = filter_var($_POST['outstanding_num'],FILTER_SANITIZE_STRING);
    $incident_reported = filter_var($_POST['incident_reported'],FILTER_SANITIZE_STRING);
    $damage_only = filter_var($_POST['damage_only'],FILTER_SANITIZE_STRING);
    $injury_only = filter_var($_POST['injury_only'],FILTER_SANITIZE_STRING);
    $injuty_and_damage = filter_var($_POST['injuty_and_damage'],FILTER_SANITIZE_STRING);
    $vehicle_accident = filter_var($_POST['vehicle_accident'],FILTER_SANITIZE_STRING);
    $no_loss = filter_var($_POST['no_loss'],FILTER_SANITIZE_STRING);
    $comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_monthly_health_and_safety_summary` (`safetyid`, `today_date`, `contactid`, `period_ending`, `summary_type`, `workers`, `comp_orent`,
        `toolbox_meeting`, `conducetd_number`, `per_attendance`, `inspection_schd`, `comp_num`, `unsafe_acts`, `corrected_num`, `outstanding_num`, `incident_reported`, `damage_only`, `injury_only`, `injuty_and_damage`, `vehicle_accident`, `no_loss`, `comments`, `attendance_staff`, `attendance_extra`) VALUES	('$safetyid', '$today_date', '$contactid', '$period_ending', '$summary_type', '$workers', '$comp_orent', '$toolbox_meeting', '$conducetd_number', '$per_attendance', '$inspection_schd', '$comp_num', '$unsafe_acts', '$corrected_num', '$outstanding_num', '$incident_reported', '$damage_only', '$injury_only', '$injuty_and_damage', '$vehicle_accident', '$no_loss', '$comments', '$attendance_staff', '$attendance_extra')";
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

            include ('monthly_health_and_safety_summary_pdf.php');
            echo monthly_health_and_safety_summary_pdf($dbc,$safetyid, $fieldlevelriskid);
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
        $query_update_employee = "UPDATE `safety_monthly_health_and_safety_summary` SET `contactid` = '$contactid', `period_ending` = '$period_ending', `summary_type` = '$summary_type', `workers` = '$workers', `comp_orent` = '$comp_orent', `toolbox_meeting` = '$toolbox_meeting', `conducetd_number` = '$conducetd_number', `per_attendance` = '$per_attendance', `inspection_schd` = '$inspection_schd', `comp_num` = '$comp_num', `unsafe_acts` = '$unsafe_acts', `corrected_num` = '$corrected_num', `outstanding_num` = '$outstanding_num', `incident_reported` = '$incident_reported', `damage_only` = '$damage_only',`injury_only` = '$injury_only', `injuty_and_damage` = '$injuty_and_damage', `vehicle_accident` = '$vehicle_accident', `no_loss` = '$no_loss', `comments` = '$comments' WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];
                $staffcheck = implode('*#*',$_POST['staffcheck_'.$assign_staff_id]);

                $img = sigJsonToImage($sign);
                imagepng($img, 'monthly_health_and_safety_summary/download/safety_'.$assign_staff_id.'.png');

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

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE	fieldlevelriskid='$fieldlevelriskid' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            include ('monthly_health_and_safety_summary_pdf.php');
            echo monthly_health_and_safety_summary_pdf($dbc,$safetyid, $fieldlevelriskid);
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
