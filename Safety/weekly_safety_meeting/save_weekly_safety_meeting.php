 <?php
    $today_date = $_POST['today_date'];
    $location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);

    $issue_discussion = filter_var(htmlentities($_POST['issue_discussion']),FILTER_SANITIZE_STRING);
    $follow_up = filter_var(htmlentities($_POST['follow_up']),FILTER_SANITIZE_STRING);
    $contactid = $_SESSION['contactid'];

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_weekly_safety_meeting` (`safetyid`, `today_date`, `location`, `issue_discussion`, `follow_up`, `contactid`, `attendance_staff`, `attendance_extra`) VALUES	('$safetyid', '$today_date', '$location', '$issue_discussion', '$follow_up', '$contactid', '$attendance_staff', '$attendance_extra')";
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

            include ('weekly_safety_meeting_pdf.php');
            echo weekly_safety_meeting_pdf($dbc,$safetyid, $fieldlevelriskid);
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        }

    } else {
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `safety_weekly_safety_meeting` SET `location` = '$location', `issue_discussion` = '$issue_discussion', `follow_up` = '$follow_up', `contactid` = '$contactid' WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];
                $staffcheck = implode(',',$_POST['staffcheck_'.$assign_staff_id]);

                $img = sigJsonToImage($sign);
                imagepng($img, 'weekly_safety_meeting/download/safety_'.$assign_staff_id.'.png');

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

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE	fieldlevelriskid='$fieldlevelriskid' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            include ('weekly_safety_meeting_pdf.php');
            echo weekly_safety_meeting_pdf($dbc,$safetyid, $fieldlevelriskid);
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