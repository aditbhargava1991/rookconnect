 <?php
	if($_POST['new_category'] != '') {
		$category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
	} else {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	}
	$category = ( !empty($category) ) ? $category : 'Safety';
	if($_POST['new_heading'] != '') {
		$heading = filter_var($_POST['new_heading'],FILTER_SANITIZE_STRING);
	} else {
		$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
	}

	if($_POST['new_heading_number'] != '') {
		$heading_number = filter_var($_POST['new_heading_number'],FILTER_SANITIZE_STRING);
	} else {
		$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
	}

    //$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
    $sub_heading_number = filter_var($_POST['sub_heading_number'],FILTER_SANITIZE_STRING);
    $sub_heading = filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);

    $third_heading = filter_var($_POST['third_heading'],FILTER_SANITIZE_STRING);
    $third_heading_number = filter_var($_POST['third_heading_number'],FILTER_SANITIZE_STRING);

    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $assign_staff = ','.implode(',',$_POST['assign_staff']).',';
    $assign_sites = ','.implode(',',$_POST['assign_sites']).',';
    $assign_work_orders = ','.implode(',',$_POST['assign_work_orders']).',';
    $deadline = (!empty (filter_var($_POST['deadline'],FILTER_SANITIZE_STRING) ) ) ? filter_var($_POST['deadline'],FILTER_SANITIZE_STRING) : date('Y-m-d');

	$email_subject	= filter_var ( $_POST['email_subject'], FILTER_SANITIZE_STRING );
	$email_message	= filter_var ( htmlentities ( $_POST['email_message'] ), FILTER_SANITIZE_STRING );
	$subject	 	= $email_subject;
	$message	 	= $email_message;

    $form_name = implode(',',$_POST['form_name']);

    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $form = filter_var($_POST['form_name'],FILTER_SANITIZE_STRING);

    $user_form_id = '';
    $is_user_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_forms` WHERE `form_id` = '$form'"));
    if ($is_user_form['num_rows'] > 0) {
        $query = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$form'"));
        $user_form_id = $query['form_id'];
        $form = $query['name'];
    }

	$marker = filter_var($_POST['colorpickers'],FILTER_SANITIZE_STRING);

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    $last_edited = date('Y-m-d');
    if(empty($_POST['safetyid'])) {
        $query_insert_vendor = "INSERT INTO `safety` (`tab`, `form`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading`, `third_heading_number`, `form_name`, `description`, `assign_staff`, `assign_sites`, `assign_work_orders`, `deadline`, `email_subject`, `email_message`, `last_edited`, `marker`, `user_form_id`) VALUES ('$tab_field', '$form', '$category', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$third_heading', '$third_heading_number', '$form_name', '$description', '$assign_staff', '$assign_sites', '$assign_work_orders', '$deadline', '$email_subject', '$email_message', '$last_edited', '$marker', '$user_form_id')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $safetyid = mysqli_insert_id($dbc);

        $url = 'Added';

        $before_change = '';
        $history = "Safety entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);
    } else {
        $safetyid = $_POST['safetyid'];

        $get_manual = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT description, last_edited FROM safety WHERE safetyid='$safetyid'"));
        $db_desc = $get_manual['description'];

        if($db_desc == $description) {
            $last_edited = $get_manual['last_edited'];
			$last_edited = ( !empty ( $last_edited ) ) ? $last_edited : date('Y-m-d');
        } else {
            $last_edited = date('Y-m-d');
        }

        $query_update_vendor = "UPDATE `safety` SET `tab` = '$tab_field', `form` = '$form', `category` = '$category', `heading_number` = '$heading_number', `heading` = '$heading', `sub_heading_number` = '$sub_heading_number', `sub_heading` = '$sub_heading', `third_heading_number` = '$third_heading_number', `third_heading` = '$third_heading', `form_name` = '$form_name', `description` = '$description', `assign_staff` = '$assign_staff', `assign_sites` = '$assign_sites', `assign_work_orders` = '$assign_work_orders', `deadline` = '$deadline', `email_subject` = '$email_subject', `email_message` = '$email_message', `last_edited` = '$last_edited', `marker` = '$marker', `user_form_id` = '$user_form_id' WHERE `safetyid` = '$safetyid'";

        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    $assign_staff = $_POST['assign_staff'];
    for($i = 0; $i < count($_POST['assign_staff']); $i++) {
        if($assign_staff[$i] != '') {
            $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetystaffid) AS total_id FROM safety_staff WHERE safetyid='$safetyid' AND staffid='{$assign_staff[$i]}' AND done=0"));
            if($get_staff['total_id'] == 0) {

                //Mail
                $to = get_email($dbc, $assign_staff[$i]);
                $subject = 'Safety Assigned to you for Review';
                $message = "Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>";
                $message .= '<br><br>Safety: <a target="_blank" href="http://'.$_SERVER['SERVER_NAME'].'/Safety/index.php?safetyid='.$safetyid.'&type='.$manual_type.'&action=view">Click Here</a><br>';

                if($to != '') {
                    // send_email('', $to, '', '', $subject, $message, '');
                }

                //Mail

                $query_insert_upload = "INSERT INTO `safety_staff` (`safetyid`, `staffid`) VALUES ('$safetyid', '$assign_staff[$i]')";
                $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            }
        }
    }

    $before_change = '';
    $history = "Safety staff entry has been added. <br />";
    add_update_history($dbc, 'safety_history', $history, '', $before_change);

    $last_edited = date('Y-m-d');

	// Save Documents
	foreach($_FILES['document']['name'] as $i => $filename) {
		if($filename != '') {
			$filename = file_safe_str($filename);
			move_uploaded_file($_FILES['document']['tmp_name'][$i],'download/'.$filename);
			$query_insert_upload = "INSERT INTO `safety_upload` (`safetyid`, `type`, `upload`) VALUES ('$safetyid', 'document', '$filename')";
			$result_insert_upload = mysqli_query($dbc, $query_insert_upload);

			if($url == 'Updated') {
				$result_update_manual = mysqli_query($dbc, "UPDATE `safety` SET `last_edited` = '$last_edited' WHERE `safetyid` = '$safetyid'");
			}
		}
	}
	// Save Links
	foreach($_POST['link'] as $link) {
		if($link != '') {
            $query_insert_upload = "INSERT INTO `safety_upload` (`safetyid`, `type`, `upload`) VALUES ('$safetyid', 'link', '$link')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            if($url == 'Updated') {
                $result_update_manual = mysqli_query($dbc, "UPDATE `safety` SET `last_edited` = '$last_edited' WHERE `safetyid` = '$safetyid'");
            }
		}
	}

	// Save Videos
	foreach($_FILES['video']['name'] as $i => $videoname) {
		if($videoname != '') {
			$videoname = file_safe_str($videoname);
			move_uploaded_file($_FILES['video']['tmp_name'][$i],'download/'.$videoname);
			$query_insert_upload = "INSERT INTO `safety_upload` (`safetyid`, `type`, `upload`) VALUES ('$safetyid', 'video', '$videoname')";
			$result_insert_upload = mysqli_query($dbc, $query_insert_upload);
			if($url == 'Updated') {
				$result_update_manual = mysqli_query($dbc, "UPDATE `safety` SET `last_edited` = '$last_edited' WHERE `safetyid` = '$safetyid'");
			}
		}
	}

  $before_change = '';
  $history = "Safety upload entry has been added. <br />";
  add_update_history($dbc, 'safety_history', $history, '', $before_change);

	$return_url = 'index.php?tab='.config_safe_str($tab_field == 'Manual' ? 'manuals' : $tab_field).($category != '' ? '&cat_name='.$category : '');
	if(!empty($_GET['return_url'])) {
		$return_url = urldecode($_GET['return_url']);
	}
    echo '<script type="text/javascript"> window.location.replace("'.$return_url.'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
