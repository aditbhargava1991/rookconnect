 <?php
    $today_date = $_POST['today_date'];
    $contactid = $_SESSION['contactid'];

    $company = filter_var($_POST['company'],FILTER_SANITIZE_STRING);
    $inspection_time = filter_var($_POST['inspection_time'],FILTER_SANITIZE_STRING);
    $job_number = filter_var($_POST['job_number'],FILTER_SANITIZE_STRING);

    $model = filter_var($_POST['model'],FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
    $check = filter_var($_POST['check'],FILTER_SANITIZE_STRING);
    $equ_unit = filter_var($_POST['equ_unit'],FILTER_SANITIZE_STRING);
    $odometer = filter_var($_POST['odometer'],FILTER_SANITIZE_STRING);
    $trip_type = filter_var($_POST['trip_type'],FILTER_SANITIZE_STRING);
    $fields = implode(',',$_POST['fields']);

    $fields_value = '**FFM****FFM****FFM****FFM****FFM****FFM****FFM****FFM****FFM****FFM****FFM****FFM**';
    for($i=12; $i<=49; $i++) {
        $fields_value .= filter_var($_POST['fields_value_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }

    $remarks = filter_var(htmlentities($_POST['remarks']),FILTER_SANITIZE_STRING);
    $defect_status= filter_var($_POST['defect_status'],FILTER_SANITIZE_STRING);

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_daily_equipment_inspection_checklist` (`safetyid`, `today_date`, `contactid`, `company`, `inspection_time`, `job_number`, `model`, `type`, `check`, `equ_unit`, `odometer`, `trip_type`, `fields`, `fields_value`, `remarks`, `defect_status`, `attendance_staff`, `attendance_extra`) VALUES	('$safetyid', '$today_date', '$contactid', '$company', '$inspection_time', '$job_number', '$model', '$type', '$check', '$equ_unit', '$odometer', '$trip_type', '$fields', '$fields_value', '$remarks', '$defect_status', '$attendance_staff', '$attendance_extra')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $fieldlevelriskid = mysqli_insert_id($dbc);

        $before_change = '';
        $history = "Safety attendance entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);

        $attendance_staff_each = $_POST['attendance_staff'];
        for($i = 0; $i < count($_POST['attendance_staff']); $i++) {
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$attendance_staff_each[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $before_change = '';
        $history = "Safety attendance entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);

        for($i=1;$i<=$attendance_extra;$i++) {
            $att_ex = 'Extra '.$i;
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$att_ex')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $before_change = '';
        $history = "Safety attendance entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);

        $tab = get_safety($dbc, $safetyid, 'tab');
        if($tab == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 1)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            $before_change = '';
            $history = "Safety attendance entry has been added. <br />";
            add_update_history($dbc, 'safety_history', $history, '', $before_change);

            include ('daily_equipment_inspection_checklist_pdf.php');
            echo daily_equipment_inspection_checklist_pdf($dbc,$safetyid, $fieldlevelriskid);
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        } else if($tab == 'Toolbox' || $tab == 'Tailgate') {
            $assign_staff = 'Organizer: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 0)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            $before_change = '';
            $history = "Safety attendance entry has been added. <br />";
            add_update_history($dbc, 'safety_history', $history, '', $before_change);
        }

    } else {
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `safety_daily_equipment_inspection_checklist` SET `contactid` = '$contactid', `company` = '$company', `inspection_time` = '$inspection_time', `job_number` = '$job_number', `model` = '$model', `type` = '$type', `check` = '$check', `equ_unit` = '$equ_unit', `odometer` = '$odometer', `trip_type` = '$trip_type', `fields` = '$fields', `fields_value` = '$fields_value', `remarks` = '$remarks', `defect_status` = '$defect_status' WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];

                $img = sigJsonToImage($sign);
                imagepng($img, 'daily_equipment_inspection_checklist/download/safety_'.$assign_staff_id.'.png');

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
            include ('daily_equipment_inspection_checklist_pdf.php');
            echo daily_equipment_inspection_checklist_pdf($dbc,$safetyid, $fieldlevelriskid);
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
        echo '<script type="text/javascript"> window.location.replace("safety.php?tab='.$get_manual['tab'].'&category='.$get_manual['category'].'"); </script>';
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
