 <?php
    $today_date = $_POST['today_date'];
    $jobid = $_POST['jobid'];
    $contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
    $location = filter_var(implode('*#*',$_POST['location']),FILTER_SANITIZE_STRING);
	$get_safety = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `safety` WHERE `safetyid`='$safetyid'"));

    $contactid = $_SESSION['contactid'];
    $fields = implode(',',$_POST['fields']);

    $fields_value = '**FFM**';
    for($i=1; $i<=166; $i++) {
		$fields_value .= filter_var($_POST['fields_value_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }

    $job_complete_value = '**FFM**';
    for($i=1; $i<=3; $i++) {
        $job_complete_value .= filter_var($_POST['job_complete_value_'.$i],FILTER_SANITIZE_STRING).'**FFM**';
    }

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    $working_alone = $_POST['working_alone'];
    $total_task = count($_POST['task']);
    $all_task = '';
     $total_task = count($_POST['task']);
    for($i=0; $i<$total_task; $i++) {
        if($_POST['task'][$i] != '') {
            $all_task .= $_POST['task'][$i].'**'.$_POST['hazard'][$i].'**'.$_POST['hazard_level'][$i].'**'.$_POST['hazard_plan'][$i].'**##**';
        }
    }
    $all_task = filter_var($all_task,FILTER_SANITIZE_STRING);
    $job_complete = implode(',',$_POST['job_complete']);

    $crew_leader = filter_var($_POST['crew_leader'], FILTER_SANITIZE_STRING);
    $worker_inc = 0;
    foreach ($_POST['workers_on_crew'] as $worker) {
        $sign = $_POST['sign_worker_' . $worker_inc];
        $img = sigJsonToImage($sign);
        imagepng($img, 'field_level_hazard_assessment/download/safety_worker_'.$worker.'.png');
        $worker_inc++;
    }
    $workers_on_crew = implode(',',$_POST['workers_on_crew']);

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_field_level_risk_assessment` (`safetyid`, `today_date`, `jobid`, `contactid`, `location`, `fields`, `fields_value`, `working_alone`, `all_task`, `job_complete`, `job_complete_value`, `attendance_staff`, `attendance_extra`, `workers_on_crew`, `crew_leader`) VALUES	('$safetyid', '$today_date', '$jobid', '$contactid', '$location', '$fields', '$fields_value', '$working_alone', '$all_task', '$job_complete', '$job_complete_value', '$attendance_staff', '$attendance_extra', '$worker_on_crew', '$crew_leader')";
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

        if($get_safety['tab'] == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 1)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            include ('field_level_hazard_pdf.php');
            echo field_level_hazard_pdf($dbc,$safetyid, $fieldlevelriskid);
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

		if($fieldlevelriskid == 'new') {
			$query_insert_site = "INSERT INTO `safety_field_level_risk_assessment` (`safetyid`, `today_date`, `jobid`, `contactid`, `location`, `fields`, `fields_value`, `working_alone`, `all_task`, `job_complete`, `job_complete_value`, `attendance_staff`, `attendance_extra`, `workers_on_crew`, `crew_leader`) VALUES	('$safetyid', '$today_date', '$jobid', '$contactid', '$location', '$fields', '$fields_value', '$working_alone', '$all_task', '$job_complete', '$job_complete_value', '$attendance_staff', '$attendance_extra', '$workers_on_crew', '$crew_leader')";
			$result_insert_site	= mysqli_query($dbc, $query_insert_site);
			$fieldlevelriskid = mysqli_insert_id($dbc);

      $before_change = '';
      $history = "Safety field level risk Assessment entry has been added. <br />";
      add_update_history($dbc, 'safety_history', $history, '', $before_change);

			$query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '".get_contact($dbc, $_SESSION['contactid'])."')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            $before_change = '';
            $history = "Safety attendance entry has been added. <br />";
            add_update_history($dbc, 'safety_history', $history, '', $before_change);
		} else {
			$query_update_employee = "UPDATE `safety_field_level_risk_assessment` SET `fields` = '$fields', `fields_value` = '$fields_value', `working_alone` = '$working_alone',`all_task` = CONCAT(all_task,'$all_task'), `job_complete` = '$job_complete', `job_complete_value` = '$job_complete_value' WHERE fieldlevelriskid='$fieldlevelriskid'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		}
    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '' || $fieldlevelriskid == 'new') {
                $sign = $_POST['sign_'.($fieldlevelriskid == 'new' ? 'new' : $assign_staff_id)];
                $img = sigJsonToImage($sign);
                imagepng($img, 'field_level_hazard_assessment/download/safety_'.$assign_staff_id.'_'.$fieldlevelriskid.'.png');

                $assign_staff = filter_var($_POST['assign_staff_'.$assign_staff_id],FILTER_SANITIZE_STRING);

                if($assign_staff != '') {
                    $query_update_employee = "UPDATE `safety_attendance` SET `assign_staff` = '$assign_staff', `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                } else {
                    $query_update_employee = "UPDATE `safety_attendance` SET `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                }
            }
            else if($_POST['sign_new'] != '') {
                $sign = $_POST['sign_new'];
                $img = sigJsonToImage($sign);
                imagepng($img, 'field_level_hazard_assessment/download/safety_'.$assign_staff_id.'_'.$fieldlevelriskid.'.png');

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
        if($get_total_notdone['total_notdone'] == 0 && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
            include ('field_level_hazard_pdf.php');
            echo field_level_hazard_pdf($dbc,$safetyid, $fieldlevelriskid);
            $url_redirect = 'index.php?type=safety&reports=view';
        } else if($get_total_notdone['total_notdone'] == 0) {
            include ('field_level_hazard_pdf.php');
            echo field_level_hazard_pdf($dbc,$safetyid, $fieldlevelriskid);
            $url_redirect = 'manual_reporting.php?type=safety';
        }
    }

    if(!empty($_GET['return_url'])) {
		$url_redirect = $_GET['return_url'];
	} else if($url_redirect == '' && $field_level_hazard == 'field_level_hazard_submit' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
		$url_redirect = 'index.php?tab='.$get_safety['tab'].'&category='.$get_safety['category'];
	} else if($url_redirect == '' && $field_level_hazard == 'field_level_hazard_submit') {
		$url_redirect = 'safety.php?tab='.$get_safety['tab'].'&category='.$get_safety['category'];
	} else if($url_redirect == '' && $field_level_hazard == 'field_level_hazard_submit' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
        $url_redirect = 'index.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
	} else if($url_redirect == '' && $field_level_hazard == 'field_level_hazard_submit') {
        $url_redirect = 'add_manual.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
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
