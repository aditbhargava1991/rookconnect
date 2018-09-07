 <?php
	if($_POST['new_category'] != '') {
		$category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
	} else {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	}
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
    $deadline = (!empty (filter_var($_POST['deadline'],FILTER_SANITIZE_STRING) ) ) ? filter_var($_POST['deadline'],FILTER_SANITIZE_STRING) : date('Y-m-d');

	$email_subject	= filter_var ( $_POST['email_subject'], FILTER_SANITIZE_STRING );
	$email_message	= filter_var ( htmlentities ( $_POST['email_message'] ), FILTER_SANITIZE_STRING );
	$completed_recipient	= filter_var ( $_POST['completed_recipient'], FILTER_SANITIZE_STRING );
	$completed_subject	= filter_var ( $_POST['completed_subject'], FILTER_SANITIZE_STRING );
	$completed_message	= filter_var ( htmlentities ( $_POST['completed_message'] ), FILTER_SANITIZE_STRING );
	$approval_subject	= filter_var ( $_POST['approval_subject'], FILTER_SANITIZE_STRING );
	$approval_message	= filter_var ( htmlentities ( $_POST['approval_message'] ), FILTER_SANITIZE_STRING );
	$rejected_subject	= filter_var ( $_POST['rejected_subject'], FILTER_SANITIZE_STRING );
	$rejected_message	= filter_var ( htmlentities ( $_POST['rejected_message'] ), FILTER_SANITIZE_STRING );
	$subject	 	= $email_subject;
	$message	 	= $email_message;

    $permissions_position = implode(',',$_POST['permissions_position']);

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

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    $last_edited = date('Y-m-d');
    if(empty($_POST['hrid'])) {
        $query_insert_vendor = "INSERT INTO `hr` (`tab`, `form`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading`, `third_heading_number`, `form_name`, `description`, `assign_staff`, `deadline`, `email_subject`, `email_message`, `completed_recipient`, `completed_subject`, `completed_message`, `approval_subject`, `approval_message`, `rejected_subject`, `rejected_message`, `last_edited`, `user_form_id`, `permissions_position`) VALUES ('$tab_field', '$form', '$category', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$third_heading', '$third_heading_number', '$form_name', '$description', '$assign_staff', '$deadline', '$email_subject', '$email_message', '$completed_recipient', '$completed_subject', '$completed_message', '$approval_subject', '$approval_message', '$rejected_subject', '$rejected_message', '$last_edited', '$user_form_id', '$permissions_position')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $hrid = mysqli_insert_id($dbc);
        $before_change = '';
    		$history = "HR entry added. <br />";
    		add_update_history($dbc, 'hr_history', $history, '', $before_change);
        $url = 'Added';
    } else {
        $hrid = $_POST['hrid'];

        $get_manual = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT description, last_edited FROM hr WHERE hrid='$hrid'"));
        $db_desc = $get_manual['description'];

        if($db_desc == $description) {
            $last_edited = $get_manual['last_edited'];
        } else {
            $last_edited = date('Y-m-d');
        }

        $before_change = capture_before_change($dbc, 'hr', 'form', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'category', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'favourite', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'heading_number', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'heading', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'sub_heading_number', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'sub_heading', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'third_heading_number', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'third_heading', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'hr_description', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'assign_staff', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'deadline', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'fields', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'email_subject', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'email_message', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'approval_message', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'rejected_subject', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'user_form_id', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'recurring_due_date', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'rejected_subject', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'recurring_due_date_interval', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'recurring_due_date_type', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'recurring_due_date_reminder', 'hrid', $hrid);
    		$before_change .= capture_before_change($dbc, 'hr', 'recurring_due_date_email', 'hrid', $hrid);

        $query_update_vendor = "UPDATE `hr` SET `tab` = '$tab_field', `form` = '$form', `category` = '$category', `heading_number` = '$heading_number', `heading` = '$heading', `sub_heading_number` = '$sub_heading_number', `sub_heading` = '$sub_heading', `third_heading_number` = '$third_heading_number', `third_heading` = '$third_heading', `form_name` = '$form_name', `description` = '$description', `assign_staff` = '$assign_staff', `deadline` = '$deadline', `email_subject` = '$email_subject', `email_message` = '$email_message', `completed_recipient`='$completed_recipient', `completed_subject` = '$completed_subject', `completed_message` = '$completed_message', `approval_subject` = '$approval_subject', `approval_message` = '$approval_message', `rejected_subject` = '$rejected_subject', `rejected_message` = '$rejected_message', `last_edited` = '$last_edited', `user_form_id` = '$user_form_id', `permissions_position` = '$permissions_position' WHERE `hrid` = '$hrid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        $history = capture_after_change('form', $form_name);
    		$history .= capture_after_change('category', $category);
    		$history .= capture_after_change('favourite', $favourite);
    		$history .= capture_after_change('heading_number', $heading_number);
    		$history .= capture_after_change('heading', $heading);
    		$history .= capture_after_change('sub_heading_number', $sub_heading_number);
    		$history .= capture_after_change('sub_heading', $sub_heading);
    		$history .= capture_after_change('third_heading_number', $third_heading_number);
    		$history .= capture_after_change('third_heading', $third_heading);
    		$history .= capture_after_change('hr_description', $hr_description);
    		$history .= capture_after_change('assign_staff', $assign_staff);
    		$history .= capture_after_change('deadline', $deadline);
    		$history .= capture_after_change('fields', $fields);
    		$history .= capture_after_change('email_subject', $email_subject);
    		$history .= capture_after_change('email_message', $email_message);
    		$history .= capture_after_change('approval_message', $approval_message);
    		$history .= capture_after_change('rejected_subject', $rejected_subject);
    		$history .= capture_after_change('user_form_id', $user_form_id);
    		$history .= capture_after_change('recurring_due_date', $recurring_due_date);
    		$history .= capture_after_change('rejected_subject', $rejected_subject);
    		$history .= capture_after_change('recurring_due_date_interval', $recurring_due_date_interval);
    		$history .= capture_after_change('recurring_due_date_type', $recurring_due_date_type);
    		$history .= capture_after_change('recurring_due_date_reminder', $recurring_due_date_reminder);
    		$history .= capture_after_change('recurring_due_date_email', $recurring_due_date_email);

        add_update_history($dbc, 'hr_history', $history, '', $before_change);
        $url = 'Updated';
    }

    $assign_staff = $_POST['assign_staff'];
    for($i = 0; $i < count($_POST['assign_staff']); $i++) {
        if($assign_staff[$i] != '') {
            $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(hrstaffid) AS total_id FROM hr_staff WHERE hrid='$hrid' AND staffid='$assign_staff[$i]' AND done=0"));
            if($get_staff['total_id'] == 0) {

                //Mail
                $to = get_email($dbc, $assign_staff[$i]);
                //$subject = 'HR Assigned to you for Review';
                //$message = "Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>";
                $message .= '<br><br>HR: <a target="_blank" href="'.WEBSITE_URL.'/Staff/add_manual.php?hrid='.$hrid.'&type='.$manual_type.'&action=view">Click Here</a><br>';

                send_email('', $to, '', '', $subject, $message, '');

                //Mail

                $query_insert_upload = "INSERT INTO `hr_staff` (`hrid`, `staffid`) VALUES ('$hrid', '$assign_staff[$i]')";
                $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
                $before_change = '';
            		$history = "HR Staff entry added. <br />";
            		add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    $last_edited = date('Y-m-d');
    $document = htmlspecialchars($_FILES["document"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `hr_upload` (`hrid`, `type`, `upload`) VALUES ('$hrid', 'document', '$document[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
        		$history = "HR Upload entry added. <br />";
        		add_update_history($dbc, 'hr_history', $history, '', $before_change);

            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'hr', 'last_edited', 'hrid', $hrid);

                $result_update_manual = mysqli_query($dbc, "UPDATE `hr` SET `last_edited` = '$last_edited' WHERE `hrid` = '$hrid'");

                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    $link = $_POST['link'];
    for($i = 0; $i < count($_POST['link']); $i++) {
        if($link[$i] != '') {
            $query_insert_upload = "INSERT INTO `hr_upload` (`hrid`, `type`, `upload`) VALUES ('$hrid', 'link', '$link[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
        		$history = "HR Upload entry added. <br />";
        		add_update_history($dbc, 'hr_history', $history, '', $before_change);

            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'hr', 'last_edited', 'hrid', $hrid);

                $result_update_manual = mysqli_query($dbc, "UPDATE `hr` SET `last_edited` = '$last_edited' WHERE `hrid` = '$hrid'");

                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    $video = htmlspecialchars($_FILES["video"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['video']['name']); $i++) {
        if($video[$i] != '') {
            move_uploaded_file($_FILES["video"]["tmp_name"][$i], "download/" . $_FILES["video"]["name"][$i]) ;

            $query_insert_upload = "INSERT INTO `hr_upload` (`hrid`, `type`, `upload`) VALUES ('$hrid', 'video', '$video[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
        		$history = "HR Upload entry added. <br />";
        		add_update_history($dbc, 'hr_history', $history, '', $before_change);
            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'hr', 'last_edited', 'hrid', $hrid);

                $result_update_manual = mysqli_query($dbc, "UPDATE `hr` SET `last_edited` = '$last_edited' WHERE `hrid` = '$hrid'");

                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    echo '<script type="text/javascript"> window.location.replace("hr.php?tab='.$tab_field.'&category='.$category.'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
