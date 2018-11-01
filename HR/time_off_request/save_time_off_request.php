 <?php
    if(!file_exists('../HR/time_off_request/download')) {
        mkdir('../HR/time_off_request/download', 0777, true);
    }
    $today_date = date('Y-m-d');
    $contactid = isset($_POST['contactid']) ? filter_var($_POST['contactid'],FILTER_SANITIZE_STRING) : $_SESSION['contactid'];

    $fields = '';
    for($i=0; $i<=3; $i++) {
        $fields .= $_POST['fields_'.$i].'**FFM**';
    }

    $fields = filter_var(htmlentities($fields),FILTER_SANITIZE_STRING);
    $desc = filter_var(htmlentities($_POST['desc']),FILTER_SANITIZE_STRING);

    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `hr_time_off_request` (`hrid`, `today_date`, `contactid`, `desc`, `fields`) VALUES	('$hrid', '$today_date', '$contactid', '$desc', '$fields')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $fieldlevelriskid = mysqli_insert_id($dbc);
    } else {
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `hr_time_off_request` SET `fields` = '$fields', `desc` = '$desc' WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    }
	try {
		$email = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `completed_recipient`, `completed_subject`, `completed_message` FROM `hr` WHERE `hrid`='$hrid'"));
		$message = html_entity_decode($email['completed_message']);
		$message .= "<p>Staff: ".get_contact($dbc, $contactid)."<br />
			Type: ".$_POST['fields_0']."<br />
			Date: ".$_POST['fields_1']." - ".$_POST['fields_2']."<br />
			Reason: ".$_POST['fields_3']."<br />
			<a href='".WEBSITE_URL."/HR/time_off_request/approve_request.php?hrid=".$fieldlevelriskid."&status=Approved'>Approve Request</a> | <a href='".WEBSITE_URL."/HR/time_off_request/approve_request.php?hrid=".$fieldlevelriskid."&status=Denied'>Deny Request</a></p>";
		send_email('', get_email($dbc, $email['completed_recipient']), '', '', $email['completed_subject'], $message, '');
	} catch(Exception $e) {
		echo "<script> alert('Your request has been saved, but it could not be sent. Please ensure that HR is aware of your request.'); </script>";
	}

	include_once('../phpsign/signature-to-image.php');
    $img = sigJsonToImage($_POST['output']);
    imagepng($img, '../HR/time_off_request/download/hr_'.$_SESSION['contactid'].'.png');

    $tab = get_hr($dbc, $hrid, 'tab');
    if($tab == 'Form') {
        $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

        $query_insert_upload = "INSERT INTO `hr_attendance` (`hrid`, `fieldlevelriskid`, `assign_staff`, `done`, `assign_staffid`) VALUES ('$hrid', '$fieldlevelriskid', '$assign_staff', 1, '$contactid')";
        $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

        include ('time_off_request_pdf.php');
        echo time_off_request_pdf($dbc,$hrid, $fieldlevelriskid, $url_redirect);
        $url_redirect = ($url_redirect == 'N/A' ? 'N/A' : 'manual_reporting.php?type=hr');
    }

    if($url_redirect == '') {
        $url_redirect = '?tile_name='.$tile.'&hr='.$hrid;
    }

    if($field_level_hazard == 'field_level_hazard_save') {
        echo '<script type="text/javascript"> window.location.replace("?tab='.config_safe_str($get_hr['category']).'"); </script>';
    } else if($url_redirect != 'N/A') {
        echo '<script type="text/javascript">
        window.location.replace("'.$url_redirect.'"); </script>';
    }