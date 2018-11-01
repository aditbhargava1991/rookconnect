<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
$from_url = (!empty($_GET['from_url']) ? $_GET['from_url'] : 'individual_support_plan.php');

//Get the Contact Tile settings
$tile_contacts = 'clientinfo';
if(!tile_visible($dbc, 'client_info', 'super')) {
	if(tile_visible($dbc, 'contacts_rolodex', 'super')) {
		$tile_contacts = 'contactsrolodex';
	} else if(tile_visible($dbc, 'contact3', 'super')) {
		$tile_contacts = 'contacts3';
	} else if(tile_visible($dbc, 'membegrs', 'super')) {
		$tile_contacts = 'members';
	} else {
		$tile_contacts = 'contacts';
	}
}
$contact_tabs = get_config($dbc, $tile_contacts.'_tabs');
if(get_software_name() == 'breakthebarrier') {
	str_replace('Business',BUSINESS_CAT,$contact_tabs);
} else if($rookconnect == 'highland') {
	str_replace('Business','Customer',$contact_tabs);
}

if(!empty($_GET['meduploadid'])) {
    $meduploadid = $_GET['meduploadid'];
    $query = mysqli_query($dbc,"DELETE FROM medication_uploads WHERE meduploadid='$meduploadid'");
    $individualsupportplanid = $_GET['individualsupportplanid'];

    echo '<script type="text/javascript"> window.location.replace("add_medication.php?individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'"); </script>';
}

if (isset($_POST['add_medication'])) {

    $support_contact_category = $_POST['support_contact_category'];
    $support_contact = $_POST['support_contact'];
    $support_contact_gender = $_POST['support_contact_gender'];
    $support_contact_school = $_POST['support_contact_school'];
    $support_contact_grade = $_POST['support_contact_grade'];
    $support_contact_diagnosis = $_POST['support_contact_diagnosis'];
    $support_contact_date_of_birth = $_POST['support_contact_date_of_birth'];
    $support_contact_other_supports = $_POST['support_contact_other_supports'];
	if($support_contact == 'NEW_CONTACT') {
		$first_name = explode(' ', $_POST['support_contact_new_contact'])[0];
		$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$_POST['support_contact_new_contact']))),FILTER_SANITIZE_STRING);
		$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `gender`, `school`, `birth_date`) VALUES ('$tile_contacts', '$support_contact_category', '$first_name', '$last_name', '$support_contact_gender', '$support_contact_school', '$support_contact_date_of_birth')");
		$support_contact = mysqli_insert_id($dbc);
	}
    $dayprimary_contact_category = implode(',',$_POST['dayprimary_contact_category']);
    $dayprimary_contact = implode(',',$_POST['dayprimary_contact']);
	foreach($_POST['dayprimary_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['dayprimary_contact_category'][$i]."', '$first_name', '$last_name')");
			$dayprimary_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $dayprimary_contact, 1);
		}
	}
    $daytl_contact_category = implode(',',$_POST['daytl_contact_category']);
    $daytl_contact = implode(',',$_POST['daytl_contact']);
	foreach($_POST['daytl_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['daytl_contact_category'][$i]."', '$first_name', '$last_name')");
			$daytl_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $daytl_contact, 1);
		}
	}
    $daykey_contact_category = implode(',',$_POST['daykey_contact_category']);
    $daykey_contact = implode(',',$_POST['daykey_contact']);
	if($daykey_contact == 'NEW_CONTACT') {
		$first_name = explode(' ', $_POST['daykey_contact_new_contact'])[0];
		$last_name = filter_var(trim(str_replace($first_name,'',$_POST['daykey_contact_new_contact'])),FILTER_SANITIZE_STRING);
		$first_name = filter_var($first_name,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '$daykey_contact_category', '$first_name', '$last_name')");
		$daykey_contact = mysqli_insert_id($dbc);
	}
    $resiprimary_contact_category = implode(',',$_POST['resiprimary_contact_category']);
    $resiprimary_contact = implode(',',$_POST['resiprimary_contact']);
	foreach($_POST['resiprimary_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['resiprimary_contact_category'][$i]."', '$first_name', '$last_name')");
			$resiprimary_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $resiprimary_contact, 1);
		}
	}
    $resitl_contact_category = implode(',',$_POST['resitl_contact_category']);
    $resitl_contact = implode(',',$_POST['resitl_contact']);
	foreach($_POST['resitl_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['resitl_contact_category'][$i]."', '$first_name', '$last_name')");
			$resitl_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $resitl_contact, 1);
		}
	}
    $resikey_contact_category = implode(',',$_POST['resikey_contact_category']);
    $resikey_contact = implode(',',$_POST['resikey_contact']);
	if($resikey_contact == 'NEW_CONTACT') {
		$first_name = explode(' ', $_POST['resikey_contact_new_contact'])[0];
		$last_name = filter_var(trim(str_replace($first_name,'',$_POST['resikey_contact_new_contact'])),FILTER_SANITIZE_STRING);
		$first_name = filter_var($first_name,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '$resikey_contact_category', '$first_name', '$last_name')");
		$resikey_contact = mysqli_insert_id($dbc);
	}
    $guardianprimary_contact_category = implode(',',$_POST['guardianprimary_contact_category']);
    $guardianprimary_contact = implode(',',$_POST['guardianprimary_contact']);
	foreach($_POST['guardianprimary_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['guardianprimary_contact_category'][$i]."', '$first_name', '$last_name')");
			$guardianprimary_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $guardianprimary_contact, 1);
		}
	}
    $guardiansecondary_contact_category = implode(',',$_POST['guardiansecondary_contact_category']);
    $guardiansecondary_contact = implode(',',$_POST['guardiansecondary_contact']);
	foreach($_POST['guardiansecondary_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '".$_POST['guardiansecondary_contact_category'][$i]."', '$first_name', '$last_name')");
			$guardiansecondary_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $guardiansecondary_contact, 1);
		}
	}
    $guardianalt_contact_category = implode(',',$_POST['guardianalt_contact_category']);
    $guardianalt_contact = implode(',',$_POST['guardianalt_contact']);
	if($guardianalt_contact == 'NEW_CONTACT') {
		$first_name = explode(' ', $_POST['guardianalt_contact_new_contact'])[0];
		$last_name = filter_var(trim(str_replace($first_name,'',$_POST['guardianalt_contact_new_contact'])),FILTER_SANITIZE_STRING);
		$first_name = filter_var($first_name,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '$guardianalt_contact_category', '$first_name', '$last_name')");
		$guardianalt_contact = mysqli_insert_id($dbc);
	}
    $eme_contact_category = implode(',',$_POST['eme_contact_category']);
    $eme_contact = implode(',',$_POST['eme_contact']);
	if($eme_contact == 'NEW_CONTACT') {
		$first_name = explode(' ', $_POST['eme_contact_new_contact'])[0];
		$last_name = filter_var(trim(str_replace($first_name,'',$_POST['eme_contact_new_contact'])),FILTER_SANITIZE_STRING);
		$first_name = filter_var($first_name,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`) VALUES ('$tile_contacts', '$eme_contact_category', '$first_name', '$last_name')");
		$eme_contact = mysqli_insert_id($dbc);
	}
    $isp_start_date = $_POST['isp_start_date'];
    $isp_review_date = $_POST['isp_review_date'];
    $isp_end_date = $_POST['isp_end_date'];

	if($_POST['isp_quality_name'] != '') {
		$isp_quality = filter_var($_POST['isp_quality_name'],FILTER_SANITIZE_STRING);
	} else {
		$isp_quality = filter_var($_POST['isp_quality'],FILTER_SANITIZE_STRING);
	}

	if($_POST['isp_goals_name'] != '') {
		$isp_goals = filter_var(implode('*#*',$_POST['isp_goals_name']),FILTER_SANITIZE_STRING);
	} else {
		$isp_goals = filter_var($_POST['isp_goals'],FILTER_SANITIZE_STRING);
	}

    $isp_needs = filter_var(htmlentities($_POST['isp_needs']),FILTER_SANITIZE_STRING);
    $isp_strategies = filter_var(htmlentities($_POST['isp_strategies']),FILTER_SANITIZE_STRING);
    $isp_objectives = filter_var(htmlentities($_POST['isp_objectives']),FILTER_SANITIZE_STRING);

	if($_POST['isp_sis_name'] != '') {
		$isp_sis = filter_var($_POST['isp_sis_name'],FILTER_SANITIZE_STRING);
	} else {
		$isp_sis = filter_var($_POST['isp_sis'],FILTER_SANITIZE_STRING);
	}

    $isp_detail_responsible_contact_category = $_POST['isp_detail_responsible_contact_category'];
    $isp_detail_responsible_contact = implode(',',$_POST['isp_detail_responsible_contact']);
    $isp_updates = filter_var(htmlentities($_POST['isp_updates']),FILTER_SANITIZE_STRING);
    $isp_notes = filter_var(htmlentities($_POST['isp_notes']),FILTER_SANITIZE_STRING);

    $daycoordinator_contact_category = implode(',',$_POST['daycoordinator_contact_category']);
    $daycoordinator_contact = implode(',',$_POST['daycoordinator_contact']);
    $daycoordinator_contact_hours = implode('*#*',$_POST['daycoordinator_contact_hours']);
    $daycoordinator_contact_phone = implode('*#*',$_POST['daycoordinator_contact_phone']);
    $daycoordinator_contact_email = implode('*#*',$_POST['daycoordinator_contact_email']);
	foreach($_POST['daycoordinator_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['daycoordinator_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$daycoordinator_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $daycoordinator_contact, 1);
		}
	}

    $daysl_contact_category = implode(',',$_POST['daysl_contact_category']);
    $daysl_contact = implode(',',$_POST['daysl_contact']);
    $daysl_contact_hours = implode('*#*',$_POST['daysl_contact_hours']);
    $daysl_contact_phone = implode('*#*',$_POST['daysl_contact_phone']);
    $daysl_contact_email = implode('*#*',$_POST['daysl_contact_email']);
	foreach($_POST['daysl_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['daysl_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$daysl_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $daysl_contact, 1);
		}
	}

    $dayot_contact_category = implode(',',$_POST['dayot_contact_category']);
    $dayot_contact = implode(',',$_POST['dayot_contact']);
    $dayot_contact_hours = implode('*#*',$_POST['dayot_contact_hours']);
    $dayot_contact_phone = implode('*#*',$_POST['dayot_contact_phone']);
    $dayot_contact_email = implode('*#*',$_POST['dayot_contact_email']);
	foreach($_POST['dayot_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['dayot_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$dayot_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $dayot_contact, 1);
		}
	}

    $daypp_contact_category = implode(',',$_POST['daypp_contact_category']);
    $daypp_contact = implode(',',$_POST['daypp_contact']);
    $daypp_contact_hours = implode('*#*',$_POST['daypp_contact_hours']);
    $daypp_contact_phone = implode('*#*',$_POST['daypp_contact_phone']);
    $daypp_contact_email = implode('*#*',$_POST['daypp_contact_email']);
	foreach($_POST['daypp_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['daypp_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$daypp_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $daypp_contact, 1);
		}
	}

    $dayaide_contact_category = implode(',',$_POST['dayaide_contact_category']);
    $dayaide_contact = implode(',',$_POST['dayaide_contact']);
    $dayaide_contact_hours = implode('*#*',$_POST['dayaide_contact_hours']);
    $dayaide_contact_phone = implode('*#*',$_POST['dayaide_contact_phone']);
    $dayaide_contact_email = implode('*#*',$_POST['dayaide_contact_email']);
	foreach($_POST['dayaide_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['dayaide_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$dayaide_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $dayaide_contact, 1);
		}
	}

    $dayfscd_contact_category = implode(',',$_POST['dayfscd_contact_category']);
    $dayfscd_contact = implode(',',$_POST['dayfscd_contact']);
    $dayfscd_contact_hours = implode('*#*',$_POST['dayfscd_contact_hours']);
    $dayfscd_contact_phone = implode('*#*',$_POST['dayfscd_contact_phone']);
    $dayfscd_contact_email = implode('*#*',$_POST['dayfscd_contact_email']);
	foreach($_POST['dayfscd_contact_new_contact'] as $i => $full_name) {
		if($full_name != '') {
			$first_name = explode(' ', $full_name)[0];
			$last_name = filter_var(encryptIt(trim(str_replace($first_name,'',$full_name))),FILTER_SANITIZE_STRING);
			$first_name = filter_var(encryptIt(trim($first_name)),FILTER_SANITIZE_STRING);
			$phone_number = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_phone'][$i])),FILTER_SANITIZE_STRING);
			$email_address = filter_var(encryptIt(trim($_POST['daycoordiantor_contact_email'][$i])),FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `email_address`) VALUES ('$tile_contacts', '".$_POST['dayfscd_contact_category'][$i]."', '$first_name', '$last_name', '$phone_number', '$email_address')");
			$dayfscd_contact = preg_replace('/NEW_CONTACT/', mysqli_insert_id($dbc), $dayfscd_contact, 1);
		}
	}

	$goal1_date = filter_var($_POST['goal1_date'],FILTER_SANITIZE_STRING);
	$goal1_outcomes = filter_var(htmlentities($_POST['goal1_outcomes']),FILTER_SANITIZE_STRING);
	$goal2_date = filter_var($_POST['goal2_date'],FILTER_SANITIZE_STRING);
	$goal2_outcomes = filter_var(htmlentities($_POST['goal2_outcomes']),FILTER_SANITIZE_STRING);
	$goal3_date = filter_var($_POST['goal3_date'],FILTER_SANITIZE_STRING);
	$goal3_outcomes = filter_var(htmlentities($_POST['goal3_outcomes']),FILTER_SANITIZE_STRING);
	$goal4_date = filter_var($_POST['goal4_date'],FILTER_SANITIZE_STRING);
	$goal4_outcomes = filter_var(htmlentities($_POST['goal4_outcomes']),FILTER_SANITIZE_STRING);
	$longterm_goal1_notes = filter_var(htmlentities($_POST['longterm_goal1_notes']),FILTER_SANITIZE_STRING);

	$rating_behaviour_objective = filter_var($_POST['rating_behaviour_objective'],FILTER_SANITIZE_STRING);
    $rating_behaviour_child = filter_var(htmlentities($_POST['rating_behaviour_child']),FILTER_SANITIZE_STRING);
    $rating_behaviour_child_date = filter_var($_POST['rating_behaviour_child_date'],FILTER_SANITIZE_STRING);
    $rating_behaviour_child_rating = filter_var($_POST['rating_behaviour_child_rating'],FILTER_SANITIZE_STRING);
    $rating_behaviour_family = filter_var(htmlentities($_POST['rating_behaviour_family']),FILTER_SANITIZE_STRING);
    $rating_behaviour_family_date = filter_var($_POST['rating_behaviour_family_date'],FILTER_SANITIZE_STRING);
    $rating_behaviour_family_rating = filter_var($_POST['rating_behaviour_family_rating'],FILTER_SANITIZE_STRING);
    $rating_behaviour_targeted = filter_var(htmlentities($_POST['rating_behaviour_targeted']),FILTER_SANITIZE_STRING);
    $rating_behaviour_targeted_date = filter_var($_POST['rating_behaviour_targeted_date'],FILTER_SANITIZE_STRING);
    $rating_behaviour_targeted_rating = filter_var($_POST['rating_behaviour_targeted_rating'],FILTER_SANITIZE_STRING);
    $rating_behaviour_strategies_individual = filter_var(htmlentities($_POST['rating_behaviour_strategies_individual']),FILTER_SANITIZE_STRING);
    $rating_behaviour_strategies_family = filter_var(htmlentities($_POST['rating_behaviour_strategies_family']),FILTER_SANITIZE_STRING);
    $rating_behaviour_review_date = filter_var($_POST['rating_behaviour_review_date'],FILTER_SANITIZE_STRING);
    $rating_behaviour_parent_update = filter_var(htmlentities($_POST['rating_behaviour_parent_update']),FILTER_SANITIZE_STRING);
    $rating_behaviour_therapist_update = filter_var(htmlentities($_POST['rating_behaviour_therapist_update']),FILTER_SANITIZE_STRING);
    $rating_behaviour_aide_update = filter_var(htmlentities($_POST['rating_behaviour_aide_update']),FILTER_SANITIZE_STRING);
    $rating_behaviour_next_step = filter_var(htmlentities($_POST['rating_behaviour_next_step']),FILTER_SANITIZE_STRING);

    $rating_comm_objective = filter_var($_POST['rating_comm_objective'],FILTER_SANITIZE_STRING);
    $rating_comm_child = filter_var(htmlentities($_POST['rating_comm_child']),FILTER_SANITIZE_STRING);
    $rating_comm_child_date = filter_var($_POST['rating_comm_child_date'],FILTER_SANITIZE_STRING);
    $rating_comm_child_rating = filter_var($_POST['rating_comm_child_rating'],FILTER_SANITIZE_STRING);
    $rating_comm_family = filter_var(htmlentities($_POST['rating_comm_family']),FILTER_SANITIZE_STRING);
    $rating_comm_family_date = filter_var($_POST['rating_comm_family_date'],FILTER_SANITIZE_STRING);
    $rating_comm_family_rating = filter_var($_POST['rating_comm_family_rating'],FILTER_SANITIZE_STRING);
    $rating_comm_targeted = filter_var(htmlentities($_POST['rating_comm_targeted']),FILTER_SANITIZE_STRING);
    $rating_comm_targeted_date = filter_var($_POST['rating_comm_targeted_date'],FILTER_SANITIZE_STRING);
    $rating_comm_targeted_rating = filter_var($_POST['rating_comm_targeted_rating'],FILTER_SANITIZE_STRING);
    $rating_comm_strategies_individual = filter_var(htmlentities($_POST['rating_comm_strategies_individual']),FILTER_SANITIZE_STRING);
    $rating_comm_strategies_family = filter_var(htmlentities($_POST['rating_comm_strategies_family']),FILTER_SANITIZE_STRING);
    $rating_comm_review_date = filter_var($_POST['rating_comm_review_date'],FILTER_SANITIZE_STRING);
    $rating_comm_parent_update = filter_var(htmlentities($_POST['rating_comm_parent_update']),FILTER_SANITIZE_STRING);
    $rating_comm_therapist_update = filter_var(htmlentities($_POST['rating_comm_therapist_update']),FILTER_SANITIZE_STRING);
    $rating_comm_aide_update = filter_var(htmlentities($_POST['rating_comm_aide_update']),FILTER_SANITIZE_STRING);
    $rating_comm_next_step = filter_var(htmlentities($_POST['rating_comm_next_step']),FILTER_SANITIZE_STRING);

    $rating_physical_objective = filter_var($_POST['rating_physical_objective'],FILTER_SANITIZE_STRING);
    $rating_physical_child = filter_var(htmlentities($_POST['rating_physical_child']),FILTER_SANITIZE_STRING);
    $rating_physical_child_date = filter_var($_POST['rating_physical_child_date'],FILTER_SANITIZE_STRING);
    $rating_physical_child_rating = filter_var($_POST['rating_physical_child_rating'],FILTER_SANITIZE_STRING);
    $rating_physical_family = filter_var(htmlentities($_POST['rating_physical_family']),FILTER_SANITIZE_STRING);
    $rating_physical_family_date = filter_var($_POST['rating_physical_family_date'],FILTER_SANITIZE_STRING);
    $rating_physical_family_rating = filter_var($_POST['rating_physical_family_rating'],FILTER_SANITIZE_STRING);
    $rating_physical_targeted = filter_var(htmlentities($_POST['rating_physical_targeted']),FILTER_SANITIZE_STRING);
    $rating_physical_targeted_date = filter_var($_POST['rating_physical_targeted_date'],FILTER_SANITIZE_STRING);
    $rating_physical_targeted_rating = filter_var($_POST['rating_physical_targeted_rating'],FILTER_SANITIZE_STRING);
    $rating_physical_strategies_individual = filter_var(htmlentities($_POST['rating_physical_strategies_individual']),FILTER_SANITIZE_STRING);
    $rating_physical_strategies_family = filter_var(htmlentities($_POST['rating_physical_strategies_family']),FILTER_SANITIZE_STRING);
    $rating_physical_review_date = filter_var($_POST['rating_physical_review_date'],FILTER_SANITIZE_STRING);
    $rating_physical_parent_update = filter_var(htmlentities($_POST['rating_physical_parent_update']),FILTER_SANITIZE_STRING);
    $rating_physical_therapist_update = filter_var(htmlentities($_POST['rating_physical_therapist_update']),FILTER_SANITIZE_STRING);
    $rating_physical_aide_update = filter_var(htmlentities($_POST['rating_physical_aide_update']),FILTER_SANITIZE_STRING);
    $rating_physical_next_step = filter_var(htmlentities($_POST['rating_physical_next_step']),FILTER_SANITIZE_STRING);

    $rating_cognitive_objective = filter_var($_POST['rating_cognitive_objective'],FILTER_SANITIZE_STRING);
    $rating_cognitive_child = filter_var(htmlentities($_POST['rating_cognitive_child']),FILTER_SANITIZE_STRING);
    $rating_cognitive_child_date = filter_var($_POST['rating_cognitive_child_date'],FILTER_SANITIZE_STRING);
    $rating_cognitive_child_rating = filter_var($_POST['rating_cognitive_child_rating'],FILTER_SANITIZE_STRING);
    $rating_cognitive_family = filter_var(htmlentities($_POST['rating_cognitive_family']),FILTER_SANITIZE_STRING);
    $rating_cognitive_family_date = filter_var($_POST['rating_cognitive_family_date'],FILTER_SANITIZE_STRING);
    $rating_cognitive_family_rating = filter_var($_POST['rating_cognitive_family_rating'],FILTER_SANITIZE_STRING);
    $rating_cognitive_targeted = filter_var(htmlentities($_POST['rating_cognitive_targeted']),FILTER_SANITIZE_STRING);
    $rating_cognitive_targeted_date = filter_var($_POST['rating_cognitive_targeted_date'],FILTER_SANITIZE_STRING);
    $rating_cognitive_targeted_rating = filter_var($_POST['rating_cognitive_targeted_rating'],FILTER_SANITIZE_STRING);
    $rating_cognitive_strategies_individual = filter_var(htmlentities($_POST['rating_cognitive_strategies_individual']),FILTER_SANITIZE_STRING);
    $rating_cognitive_strategies_family = filter_var(htmlentities($_POST['rating_cognitive_strategies_family']),FILTER_SANITIZE_STRING);
    $rating_cognitive_review_date = filter_var($_POST['rating_cognitive_review_date'],FILTER_SANITIZE_STRING);
    $rating_cognitive_parent_update = filter_var(htmlentities($_POST['rating_cognitive_parent_update']),FILTER_SANITIZE_STRING);
    $rating_cognitive_therapist_update = filter_var(htmlentities($_POST['rating_cognitive_therapist_update']),FILTER_SANITIZE_STRING);
    $rating_cognitive_aide_update = filter_var(htmlentities($_POST['rating_cognitive_aide_update']),FILTER_SANITIZE_STRING);
    $rating_cognitive_next_step = filter_var(htmlentities($_POST['rating_cognitive_next_step']),FILTER_SANITIZE_STRING);

    $rating_safety_objective = filter_var($_POST['rating_safety_objective'],FILTER_SANITIZE_STRING);
    $rating_safety_child = filter_var(htmlentities($_POST['rating_safety_child']),FILTER_SANITIZE_STRING);
    $rating_safety_child_date = filter_var($_POST['rating_safety_child_date'],FILTER_SANITIZE_STRING);
    $rating_safety_child_rating = filter_var($_POST['rating_safety_child_rating'],FILTER_SANITIZE_STRING);
    $rating_safety_family = filter_var(htmlentities($_POST['rating_safety_family']),FILTER_SANITIZE_STRING);
    $rating_safety_family_date = filter_var($_POST['rating_safety_family_date'],FILTER_SANITIZE_STRING);
    $rating_safety_family_rating = filter_var($_POST['rating_safety_family_rating'],FILTER_SANITIZE_STRING);
    $rating_safety_targeted = filter_var(htmlentities($_POST['rating_safety_targeted']),FILTER_SANITIZE_STRING);
    $rating_safety_targeted_date = filter_var($_POST['rating_safety_targeted_date'],FILTER_SANITIZE_STRING);
    $rating_safety_targeted_rating = filter_var($_POST['rating_safety_targeted_rating'],FILTER_SANITIZE_STRING);
    $rating_safety_strategies_individual = filter_var(htmlentities($_POST['rating_safety_strategies_individual']),FILTER_SANITIZE_STRING);
    $rating_safety_strategies_family = filter_var(htmlentities($_POST['rating_safety_strategies_family']),FILTER_SANITIZE_STRING);
    $rating_safety_review_date = filter_var($_POST['rating_safety_review_date'],FILTER_SANITIZE_STRING);
    $rating_safety_parent_update = filter_var(htmlentities($_POST['rating_safety_parent_update']),FILTER_SANITIZE_STRING);
    $rating_safety_therapist_update = filter_var(htmlentities($_POST['rating_safety_therapist_update']),FILTER_SANITIZE_STRING);
    $rating_safety_aide_update = filter_var(htmlentities($_POST['rating_safety_aide_update']),FILTER_SANITIZE_STRING);
    $rating_safety_next_step = filter_var(htmlentities($_POST['rating_safety_next_step']),FILTER_SANITIZE_STRING);

    if(empty($_POST['individualsupportplanid'])) {
        $query_insert_vendor = "INSERT INTO `individual_support_plan` (`support_contact_category`, `support_contact`, `support_contact_gender`, `support_contact_school`, `support_contact_grade`, `support_contact_diagnosis`, `support_contact_date_of_birth`, `support_contact_other_supports`, `dayprimary_contact_category`, `dayprimary_contact`, `daytl_contact_category`, `daytl_contact`, `daykey_contact_category`, `daykey_contact`, `resiprimary_contact_category`, `resiprimary_contact`, `resitl_contact_category`, `resitl_contact`, `resikey_contact_category`, `resikey_contact`, `guardianprimary_contact_category`, `guardianprimary_contact`, `guardiansecondary_contact_category`, `guardiansecondary_contact`, `guardianalt_contact_category`, `guardianalt_contact`, `eme_contact_category`, `eme_contact`, `isp_start_date`, `isp_review_date`, `isp_end_date`, `isp_quality`, `isp_goals`, `isp_needs`, `isp_strategies`, `isp_objectives`, `isp_sis`, `isp_detail_responsible_contact_category`, `isp_detail_responsible_contact`, `isp_updates`, `isp_notes`, `daycoordinator_contact_category`, `daycoordinator_contact`, `daycoordinator_contact_hours`, `daycoordinator_contact_phone`, `daycoordinator_contact_email`, `daysl_contact_category`, `daysl_contact`, `daysl_contact_hours`, `daysl_contact_phone`, `daysl_contact_email`, `dayot_contact_category`, `dayot_contact`, `dayot_contact_hours`, `dayot_contact_phone`, `dayot_contact_email`, `daypp_contact_category`, `daypp_contact`, `daypp_contact_hours`, `daypp_contact_phone`, `daypp_contact_email`, `dayaide_contact_category`, `dayaide_contact`, `dayaide_contact_hours`, `dayaide_contact_phone`, `dayaide_contact_email`, `dayfscd_contact_category`, `dayfscd_contact`, `dayfscd_contact_hours`, `dayfscd_contact_phone`, `dayfscd_contact_email`, `goal1_date`, `goal1_outcomes`, `goal2_date`, `goal2_outcomes`, `goal3_date`, `goal3_outcomes`, `goal4_date`, `goal4_outcomes`, `longterm_goal1_notes`, `rating_behaviour_objective`, `rating_behaviour_child`, `rating_behaviour_child_date`, `rating_behaviour_child_rating`, `rating_behaviour_family`, `rating_behaviour_family_date`, `rating_behaviour_family_rating`, `rating_behaviour_targeted`, `rating_behaviour_targeted_date`, `rating_behaviour_targeted_rating`, `rating_behaviour_strategies_individual`, `rating_behaviour_strategies_family`, `rating_behaviour_review_date`, `rating_behaviour_parent_update`, `rating_behaviour_therapist_update`, `rating_behaviour_aide_update`, `rating_behaviour_next_step`, `rating_comm_objective`, `rating_comm_child`, `rating_comm_child_date`, `rating_comm_child_rating`, `rating_comm_family`, `rating_comm_family_date`, `rating_comm_family_rating`, `rating_comm_targeted`, `rating_comm_targeted_date`, `rating_comm_targeted_rating`, `rating_comm_strategies_individual`, `rating_comm_strategies_family`, `rating_comm_review_date`, `rating_comm_parent_update`, `rating_comm_therapist_update`, `rating_comm_aide_update`, `rating_comm_next_step`, `rating_physical_objective`, `rating_physical_child`, `rating_physical_child_date`, `rating_physical_child_rating`, `rating_physical_family`, `rating_physical_family_date`, `rating_physical_family_rating`, `rating_physical_targeted`, `rating_physical_targeted_date`, `rating_physical_targeted_rating`, `rating_physical_strategies_individual`, `rating_physical_strategies_family`, `rating_physical_review_date`, `rating_physical_parent_update`, `rating_physical_therapist_update`, `rating_physical_aide_update`, `rating_physical_next_step`, `rating_cognitive_objective`, `rating_cognitive_child`, `rating_cognitive_child_date`, `rating_cognitive_child_rating`, `rating_cognitive_family`, `rating_cognitive_family_date`, `rating_cognitive_family_rating`, `rating_cognitive_targeted`, `rating_cognitive_targeted_date`, `rating_cognitive_targeted_rating`, `rating_cognitive_strategies_individual`, `rating_cognitive_strategies_family`, `rating_cognitive_review_date`, `rating_cognitive_parent_update`, `rating_cognitive_therapist_update`, `rating_cognitive_aide_update`, `rating_cognitive_next_step`, `rating_safety_objective`, `rating_safety_child`, `rating_safety_child_date`, `rating_safety_child_rating`, `rating_safety_family`, `rating_safety_family_date`, `rating_safety_family_rating`, `rating_safety_targeted`, `rating_safety_targeted_date`, `rating_safety_targeted_rating`, `rating_safety_strategies_individual`, `rating_safety_strategies_family`, `rating_safety_review_date`, `rating_safety_parent_update`, `rating_safety_therapist_update`, `rating_safety_aide_update`, `rating_safety_next_step`) VALUES ('$support_contact_category', '$support_contact', '$support_contact_gender', '$support_contact_school', '$support_contact_grade', '$support_contact_diagnosis', '$support_contact_date_of_birth', '$support_contact_other_supports', '$dayprimary_contact_category', '$dayprimary_contact', '$daytl_contact_category', '$daytl_contact', '$daykey_contact_category', '$daykey_contact', '$resiprimary_contact_category', '$resiprimary_contact', '$resitl_contact_category', '$resitl_contact', '$resikey_contact_category', '$resikey_contact', '$guardianprimary_contact_category', '$guardianprimary_contact', '$guardiansecondary_contact_category', '$guardiansecondary_contact', '$guardianalt_contact_category', '$guardianalt_contact', '$eme_contact_category', '$eme_contact', '$isp_start_date', '$isp_review_date', '$isp_end_date', '$isp_quality', '$isp_goals', '$isp_needs', '$isp_strategies', '$isp_objectives', '$isp_sis', '$isp_detail_responsible_contact_category', '$isp_detail_responsible_contact', '$isp_updates', '$isp_notes', 'daycoordinator_contact_category', '$daycoordinator_contact', '$daycoordinator_contact_hours', '$daycoordinator_contact_phone', '$daycoordinator_contact_email', 'daysl_contact_category', '$daysl_contact', '$daysl_contact_hours', '$daysl_contact_phone', '$daysl_contact_email', 'dayot_contact_category', '$dayot_contact', '$dayot_contact_hours', '$dayot_contact_phone', '$dayot_contact_email', 'daypp_contact_category', '$daypp_contact', '$daypp_contact_hours', '$daypp_contact_phone', '$daypp_contact_email', 'dayaide_contact_category', '$dayaide_contact', '$dayaide_contact_hours', '$dayaide_contact_phone', '$dayaide_contact_email', 'dayfscd_contact_category', '$dayfscd_contact', '$dayfscd_contact_hours', '$dayfscd_contact_phone', '$dayfscd_contact_email', 'goal1_date', '$goal1_outcomes', '$goal2_date', '$goal2_outcomes', '$goal3_date', '$goal3_outcomes', '$goal4_date', '$goal4_outcomes', '$longterm_goal1_notes', '$rating_behaviour_objective', '$rating_behaviour_child', '$rating_behaviour_child_date', '$rating_behaviour_child_rating', '$rating_behaviour_family', '$rating_behaviour_family_date', '$rating_behaviour_family_rating', '$rating_behaviour_targeted', '$rating_behaviour_targeted_date', '$rating_behaviour_targeted_rating', '$rating_behaviour_strategies_individual', '$rating_behaviour_strategies_family', '$rating_behaviour_review_date', '$rating_behaviour_parent_update', '$rating_behaviour_therapist_update', '$rating_behaviour_aide_update', '$rating_behaviour_next_step', '$rating_comm_objective', '$rating_comm_child', '$rating_comm_child_date', '$rating_comm_child_rating', '$rating_comm_family', '$rating_comm_family_date', '$rating_comm_family_rating', '$rating_comm_targeted', '$rating_comm_targeted_date', '$rating_comm_targeted_rating', '$rating_comm_strategies_individual', '$rating_comm_strategies_family', '$rating_comm_review_date', '$rating_comm_parent_update', '$rating_comm_therapist_update', '$rating_comm_aide_update', '$rating_comm_next_step', '$rating_physical_objective', '$rating_physical_child', '$rating_physical_child_date', '$rating_physical_child_rating', '$rating_physical_family', '$rating_physical_family_date', '$rating_physical_family_rating', '$rating_physical_targeted', '$rating_physical_targeted_date', '$rating_physical_targeted_rating', '$rating_physical_strategies_individual', '$rating_physical_strategies_family', '$rating_physical_review_date', '$rating_physical_parent_update', '$rating_physical_therapist_update', '$rating_physical_aide_update', '$rating_physical_next_step', '$rating_cognitive_objective', '$rating_cognitive_child', '$rating_cognitive_child_date', '$rating_cognitive_child_rating', '$rating_cognitive_family', '$rating_cognitive_family_date', '$rating_cognitive_family_rating', '$rating_cognitive_targeted', '$rating_cognitive_targeted_date', '$rating_cognitive_targeted_rating', '$rating_cognitive_strategies_individual', '$rating_cognitive_strategies_family', '$rating_cognitive_review_date', '$rating_cognitive_parent_update', '$rating_cognitive_therapist_update', '$rating_cognitive_aide_update', '$rating_cognitive_next_step', '$rating_safety_objective', '$rating_safety_child', '$rating_safety_child_date', '$rating_safety_child_rating', '$rating_safety_family', '$rating_safety_family_date', '$rating_safety_family_rating', '$rating_safety_targeted', '$rating_safety_targeted_date', '$rating_safety_targeted_rating', '$rating_safety_strategies_individual', '$rating_safety_strategies_family', '$rating_safety_review_date', '$rating_safety_parent_update', '$rating_safety_therapist_update', '$rating_safety_aide_update', '$rating_safety_next_step')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $individualsupportplanid = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $individualsupportplanid = $_POST['individualsupportplanid'];
        $query_update_vendor = "UPDATE `individual_support_plan` SET `support_contact_category` = '$support_contact_category', `support_contact` = '$support_contact', `support_contact_gender` = '$support_contact_gender', `support_contact_school` = '$support_contact_school', `support_contact_grade` = '$support_contact_grade', `support_contact_diagnosis` = '$support_contact_diagnosis', `support_contact_date_of_birth` = '$support_contact_date_of_birth', `support_contact_other_supports` = '$support_contact_other_supports', `dayprimary_contact_category` = '$dayprimary_contact_category', `dayprimary_contact` = '$dayprimary_contact', `daytl_contact_category` = '$daytl_contact_category', `daytl_contact` = '$daytl_contact', `daykey_contact_category` = '$daykey_contact_category', `daykey_contact` = '$daykey_contact', `resiprimary_contact_category` = '$resiprimary_contact_category', `resiprimary_contact` = '$resiprimary_contact', `resitl_contact_category` = '$resitl_contact_category', `resitl_contact` = '$resitl_contact', `resikey_contact_category` = '$resikey_contact_category', `resikey_contact` = '$resikey_contact', `guardianprimary_contact_category` = '$guardianprimary_contact_category', `guardianprimary_contact` = '$guardianprimary_contact', `guardiansecondary_contact_category` = '$guardiansecondary_contact_category', `guardiansecondary_contact` = '$guardiansecondary_contact', `guardianalt_contact_category` = '$guardianalt_contact_category', `guardianalt_contact` = '$guardianalt_contact', `eme_contact_category` = '$eme_contact_category', `eme_contact` = '$eme_contact', `isp_start_date` = '$isp_start_date', `isp_review_date` = '$isp_review_date', `isp_end_date` = '$isp_end_date', `isp_quality` = '$isp_quality', `isp_goals` = '$isp_goals', `isp_needs` = '$isp_needs', `isp_strategies` = '$isp_strategies', `isp_objectives` = '$isp_objectives', `isp_sis` = '$isp_sis', `isp_detail_responsible_contact_category` = '$isp_detail_responsible_contact_category', `isp_detail_responsible_contact` = '$isp_detail_responsible_contact', `isp_updates` = '$isp_updates', `isp_notes` = '$isp_notes', `daycoordinator_contact_category` = '$daycoordinator_contact_category', `daycoordinator_contact` = '$daycoordinator_contact', `daycoordinator_contact_hours` = '$daycoordinator_contact_hours', `daycoordinator_contact_phone` = '$daycoordinator_contact_phone', `daycoordinator_contact_email` = '$daycoordinator_contact_email', `daysl_contact_category` = '$daysl_contact_category', `daysl_contact` = '$daysl_contact', `daysl_contact_hours` = '$daysl_contact_hours', `daysl_contact_phone` = '$daysl_contact_phone', `daysl_contact_email` = '$daysl_contact_email', `dayot_contact_category` = '$dayot_contact_category', `dayot_contact` = '$dayot_contact', `dayot_contact_hours` = '$dayot_contact_hours', `dayot_contact_phone` = '$dayot_contact_phone', `dayot_contact_email` = '$dayot_contact_email', `daypp_contact_category` = '$daypp_contact_category', `daypp_contact` = '$daypp_contact', `daypp_contact_hours` = '$daypp_contact_hours', `daypp_contact_phone` = '$daypp_contact_phone', `daypp_contact_email` = '$daypp_contact_email', `dayaide_contact_category` = '$dayaide_contact_category', `dayaide_contact` = '$dayaide_contact', `dayaide_contact_hours` = '$dayaide_contact_hours', `dayaide_contact_phone` = '$dayaide_contact_phone', `dayaide_contact_email` = '$dayaide_contact_email', `dayfscd_contact_category` = '$dayfscd_contact_category', `dayfscd_contact` = '$dayfscd_contact', `dayfscd_contact_hours` = '$dayfscd_contact_hours', `dayfscd_contact_phone` = '$dayfscd_contact_phone', `dayfscd_contact_email` = '$dayfscd_contact_email', `goal1_date` = '$goal1_date', `goal1_outcomes` = '$goal1_outcomes', `goal2_date` = '$goal2_date', `goal2_outcomes` = '$goal2_outcomes', `goal3_date` = '$goal3_date', `goal3_outcomes` = '$goal3_outcomes', `goal4_date` = '$goal4_date', `goal4_outcomes` = '$goal4_outcomes', `longterm_goal1_notes` = '$longterm_goal1_notes', `rating_behaviour_objective` = '$rating_behaviour_objective', `rating_behaviour_child` = '$rating_behaviour_child', `rating_behaviour_child_date` = '$rating_behaviour_child_date', `rating_behaviour_child_rating` = '$rating_behaviour_child', `rating_behaviour_family` = '$rating_behaviour_family', `rating_behaviour_family_date` = '$rating_behaviour_family_date', `rating_behaviour_family_rating` = '$rating_behaviour_family_rating', `rating_behaviour_targeted` = '$rating_behaviour_targeted', `rating_behaviour_targeted_date` = '$rating_behaviour_targeted_date', `rating_behaviour_targeted_rating` = '$rating_behaviour_targeted_rating', `rating_behaviour_strategies_individual` = '$rating_behaviour_strategies_individual', `rating_behaviour_strategies_family` = '$rating_behaviour_strategies_family', `rating_behaviour_review_date` = '$rating_behaviour_review_date', `rating_behaviour_parent_update` = '$rating_behaviour_parent_update', `rating_behaviour_therapist_update` = '$rating_behaviour_therapist_update', `rating_behaviour_aide_update` = '$rating_behaviour_aide_update', `rating_behaviour_next_step` = '$rating_behaviour_next_step', `rating_comm_objective` = '$rating_comm_objective', `rating_comm_child` = '$rating_comm_child', `rating_comm_child_date` = '$rating_comm_child_date', `rating_comm_child_rating` = '$rating_comm_child', `rating_comm_family` = '$rating_comm_family', `rating_comm_family_date` = '$rating_comm_family_date', `rating_comm_family_rating` = '$rating_comm_family_rating', `rating_comm_targeted` = '$rating_comm_targeted', `rating_comm_targeted_date` = '$rating_comm_targeted_date', `rating_comm_targeted_rating` = '$rating_comm_targeted_rating', `rating_comm_strategies_individual` = '$rating_comm_strategies_individual', `rating_comm_strategies_family` = '$rating_comm_strategies_family', `rating_comm_review_date` = '$rating_comm_review_date', `rating_comm_parent_update` = '$rating_comm_parent_update', `rating_comm_therapist_update` = '$rating_comm_therapist_update', `rating_comm_aide_update` = '$rating_comm_aide_update', `rating_comm_next_step` = '$rating_comm_next_step', `rating_physical_objective` = '$rating_physical_objective', `rating_physical_child` = '$rating_physical_child', `rating_physical_child_date` = '$rating_physical_child_date', `rating_physical_child_rating` = '$rating_physical_child', `rating_physical_family` = '$rating_physical_family', `rating_physical_family_date` = '$rating_physical_family_date', `rating_physical_family_rating` = '$rating_physical_family_rating', `rating_physical_targeted` = '$rating_physical_targeted', `rating_physical_targeted_date` = '$rating_physical_targeted_date', `rating_physical_targeted_rating` = '$rating_physical_targeted_rating', `rating_physical_strategies_individual` = '$rating_physical_strategies_individual', `rating_physical_strategies_family` = '$rating_physical_strategies_family', `rating_physical_review_date` = '$rating_physical_review_date', `rating_physical_parent_update` = '$rating_physical_parent_update', `rating_physical_therapist_update` = '$rating_physical_therapist_update', `rating_physical_aide_update` = '$rating_physical_aide_update', `rating_physical_next_step` = '$rating_physical_next_step', `rating_cognitive_objective` = '$rating_cognitive_objective', `rating_cognitive_child` = '$rating_cognitive_child', `rating_cognitive_child_date` = '$rating_cognitive_child_date', `rating_cognitive_child_rating` = '$rating_cognitive_child', `rating_cognitive_family` = '$rating_cognitive_family', `rating_cognitive_family_date` = '$rating_cognitive_family_date', `rating_cognitive_family_rating` = '$rating_cognitive_family_rating', `rating_cognitive_targeted` = '$rating_cognitive_targeted', `rating_cognitive_targeted_date` = '$rating_cognitive_targeted_date', `rating_cognitive_targeted_rating` = '$rating_cognitive_targeted_rating', `rating_cognitive_strategies_individual` = '$rating_cognitive_strategies_individual', `rating_cognitive_strategies_family` = '$rating_cognitive_strategies_family', `rating_cognitive_review_date` = '$rating_cognitive_review_date', `rating_cognitive_parent_update` = '$rating_cognitive_parent_update', `rating_cognitive_therapist_update` = '$rating_cognitive_therapist_update', `rating_cognitive_aide_update` = '$rating_cognitive_aide_update', `rating_cognitive_next_step` = '$rating_cognitive_next_step', `rating_safety_objective` = '$rating_safety_objective', `rating_safety_child` = '$rating_safety_child', `rating_safety_child_date` = '$rating_safety_child_date', `rating_safety_child_rating` = '$rating_safety_child', `rating_safety_family` = '$rating_safety_family', `rating_safety_family_date` = '$rating_safety_family_date', `rating_safety_family_rating` = '$rating_safety_family_rating', `rating_safety_targeted` = '$rating_safety_targeted', `rating_safety_targeted_date` = '$rating_safety_targeted_date', `rating_safety_targeted_rating` = '$rating_safety_targeted_rating', `rating_safety_strategies_individual` = '$rating_safety_strategies_individual', `rating_safety_strategies_family` = '$rating_safety_strategies_family', `rating_safety_review_date` = '$rating_safety_review_date', `rating_safety_parent_update` = '$rating_safety_parent_update', `rating_safety_therapist_update` = '$rating_safety_therapist_update', `rating_safety_aide_update` = '$rating_safety_aide_update', `rating_safety_next_step` = '$rating_safety_next_step' WHERE `individualsupportplanid` = '$individualsupportplanid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    if (!file_exists('../Individual Support Plan/download')) {
        mkdir('../Individual Support Plan/download', 0777, true);
    }

    $get_isp = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `individual_support_plan` WHERE `individualsupportplanid` = '$individualsupportplanid'"));

    $signatures_parent = explode('*#*', $get_isp['signatures_parent']);
    $signatures_parent_name = explode('*#*', $get_isp['signatures_parent_name']);
    $signatures_parent_date = explode('*#*', $get_isp['signatures_parent_date']);
    $sig_count = count($signatures_parent);
    if(empty($get_isp['signatures_parent'])) {
    	$signatures_parent = '';
    	$signatures_parent_name = '';
    	$signatures_parent_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_parent'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_parent_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_parent[] = $file_name;
			$signatures_parent_name[] = filter_var($_POST['signatures_parent_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_parent_date[] = filter_var($_POST['signatures_parent_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_parent = implode('*#*', $signatures_parent);
    $signatures_parent_name = implode('*#*', $signatures_parent_name);
    $signatures_parent_date = implode('*#*', $signatures_parent_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_parent` = '$signatures_parent', `signatures_parent_name` = '$signatures_parent_name', `signatures_parent_date` = '$signatures_parent_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_coordinator = explode('*#*', $get_isp['signatures_coordinator']);
    $signatures_coordinator_name = explode('*#*', $get_isp['signatures_coordinator_name']);
    $signatures_coordinator_date = explode('*#*', $get_isp['signatures_coordinator_date']);
    $sig_count = count($signatures_coordinator);
    if(empty($get_isp['signatures_coordinator'])) {
    	$signatures_coordinator = '';
    	$signatures_coordinator_name = '';
    	$signatures_coordinator_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_coordinator'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_coordinator_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_coordinator[] = $file_name;
			$signatures_coordinator_name[] = filter_var($_POST['signatures_coordinator_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_coordinator_date[] = filter_var($_POST['signatures_coordinator_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_coordinator = implode('*#*', $signatures_coordinator);
    $signatures_coordinator_name = implode('*#*', $signatures_coordinator_name);
    $signatures_coordinator_date = implode('*#*', $signatures_coordinator_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_coordinator` = '$signatures_coordinator', `signatures_coordinator_name` = '$signatures_coordinator_name', `signatures_coordinator_date` = '$signatures_coordinator_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_sl = explode('*#*', $get_isp['signatures_sl']);
    $signatures_sl_name = explode('*#*', $get_isp['signatures_sl_name']);
    $signatures_sl_date = explode('*#*', $get_isp['signatures_sl_date']);
    $sig_count = count($signatures_sl);
    if(empty($get_isp['signatures_sl'])) {
    	$signatures_sl = '';
    	$signatures_sl_name = '';
    	$signatures_sl_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_sl'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_sl_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_sl[] = $file_name;
			$signatures_sl_name[] = filter_var($_POST['signatures_sl_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_sl_date[] = filter_var($_POST['signatures_sl_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_sl = implode('*#*', $signatures_sl);
    $signatures_sl_name = implode('*#*', $signatures_sl_name);
    $signatures_sl_date = implode('*#*', $signatures_sl_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_sl` = '$signatures_sl', `signatures_sl_name` = '$signatures_sl_name', `signatures_sl_date` = '$signatures_sl_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_ot = explode('*#*', $get_isp['signatures_ot']);
    $signatures_ot_name = explode('*#*', $get_isp['signatures_ot_name']);
    $signatures_ot_date = explode('*#*', $get_isp['signatures_ot_date']);
    $sig_count = count($signatures_ot);
    if(empty($get_isp['signatures_ot'])) {
    	$signatures_ot = '';
    	$signatures_ot_name = '';
    	$signatures_ot_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_ot'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_ot_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_ot[] = $file_name;
			$signatures_ot_name[] = filter_var($_POST['signatures_ot_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_ot_date[] = filter_var($_POST['signatures_ot_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_ot = implode('*#*', $signatures_ot);
    $signatures_ot_name = implode('*#*', $signatures_ot_name);
    $signatures_ot_date = implode('*#*', $signatures_ot_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_ot` = '$signatures_ot', `signatures_ot_name` = '$signatures_ot_name', `signatures_ot_date` = '$signatures_ot_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_pp = explode('*#*', $get_isp['signatures_pp']);
    $signatures_pp_name = explode('*#*', $get_isp['signatures_pp_name']);
    $signatures_pp_date = explode('*#*', $get_isp['signatures_pp_date']);
    $sig_count = count($signatures_pp);
    if(empty($get_isp['signatures_pp'])) {
    	$signatures_pp = '';
    	$signatures_pp_name = '';
    	$signatures_pp_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_pp'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_pp_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_pp[] = $file_name;
			$signatures_pp_name[] = filter_var($_POST['signatures_pp_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_pp_date[] = filter_var($_POST['signatures_pp_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_pp = implode('*#*', $signatures_pp);
    $signatures_pp_name = implode('*#*', $signatures_pp_name);
    $signatures_pp_date = implode('*#*', $signatures_pp_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_pp` = '$signatures_pp', `signatures_pp_name` = '$signatures_pp_name', `signatures_pp_date` = '$signatures_pp_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_physio = explode('*#*', $get_isp['signatures_physio']);
    $signatures_physio_name = explode('*#*', $get_isp['signatures_physio_name']);
    $signatures_physio_date = explode('*#*', $get_isp['signatures_physio_date']);
    $sig_count = count($signatures_physio);
    if(empty($get_isp['signatures_physio'])) {
    	$signatures_physio = '';
    	$signatures_physio_name = '';
    	$signatures_physio_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_physio'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_physio_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_physio[] = $file_name;
			$signatures_physio_name[] = filter_var($_POST['signatures_physio_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_physio_date[] = filter_var($_POST['signatures_physio_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_physio = implode('*#*', $signatures_physio);
    $signatures_physio_name = implode('*#*', $signatures_physio_name);
    $signatures_physio_date = implode('*#*', $signatures_physio_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_physio` = '$signatures_physio', `signatures_physio_name` = '$signatures_physio_name', `signatures_physio_date` = '$signatures_physio_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    $signatures_aide = explode('*#*', $get_isp['signatures_aide']);
    $signatures_aide_name = explode('*#*', $get_isp['signatures_aide_name']);
    $signatures_aide_date = explode('*#*', $get_isp['signatures_aide_date']);
    $sig_count = count($signatures_aide);
    if(empty($get_isp['signatures_aide'])) {
    	$signatures_aide = '';
    	$signatures_aide_name = '';
    	$signatures_aide_date = '';
    	$sig_count = 0;
    }
    foreach($_POST['signatures_aide'] as $key => $signature) {
    	if(!empty($signature)) {
			$img = sigJsonToImage($signature);
			$file_name = 'signatures_aide_'.$individualsupportplanid.'_'.$sig_count.'.png';
			imagepng($img, '../Individual Support Plan/download/'.$file_name);
			$signatures_aide[] = $file_name;
			$signatures_aide_name[] = filter_var($_POST['signatures_aide_name'][$key],FILTER_SANITIZE_STRING);
			$signatures_aide_date[] = filter_var($_POST['signatures_aide_date'][$key],FILTER_SANITIZE_STRING);
			$sig_count++;
    	}
    }
    $signatures_aide = implode('*#*', $signatures_aide);
    $signatures_aide_name = implode('*#*', $signatures_aide_name);
    $signatures_aide_date = implode('*#*', $signatures_aide_date);
    mysqli_query($dbc, "UPDATE `individual_support_plan` SET `signatures_aide` = '$signatures_aide', `signatures_aide_name` = '$signatures_aide_name', `signatures_aide_date` = '$signatures_aide_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

    echo '<script type="text/javascript"> window.location.replace("'.$from_url.'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
} ?>
<script type="text/javascript">
$(document).ready(function() {
	setTimeout(function() {
		$('[id^=contact_]').not('[id^=contact_category],[id$=chosen]').each(function() {
			var select = this;
			var category = $(this).data('category');
			var contacts = $(this).data('value');
			$.ajax({
				method: 'GET',
				url: 'isp_ajax_all.php?fill=contact_category&category='+category+'&contacts='+contacts,
				success: function(response) {
					$(select).empty().append(response).trigger('change.select2');
				}
			});
		});
	}, 1000);

    $("#form1").submit(function( event ) {
        var medication_type = $("#medication_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (medication_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});
$(document).on('change', 'select.contact_category_onchange', function() { selectContactCategory(this); });
$(document).on('change', 'select.contact_onchange', function() { checkContactChange(this); });

function selectContactCategory(sel) {
	if(default_contact_list == '') {
		default_contact_list = $(sel).closest('.contact_group').find('select:not([name*=category])').html();
	}
    $.ajax({
        type: "GET",
        url: "../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category="+sel.value,
        dataType: "html",   //expect html to be returned
        success: function(response){
			$(sel).closest('.contact_group').find('select:not([name*=category])').html(response).change().trigger('change.select2');
        }
    });
}

function addAnotherGoal(link) {
    var clone = $('[name="isp_goals_name[]"]').first().clone();
    clone.val('');
    $(link).closest('.form-group').find('div.col-sm-8').append(clone);
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('individual_support_plan');
?>
<div class="container">
  <div class="row">

    <h1>Individual Service Plan</h1>
	<div class="pad-left gap-top double-gap-bottom"><a href="<?= $from_url ?>" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT individual_support_plan FROM field_config"));
        $value_config = ','.$get_field_config['individual_support_plan'].',';

        $support_contact_category = '';
        $support_contact = '';
        $dayprimary_contact_category = '';
        $dayprimary_contact = '';
        $daytl_contact_category = '';
        $daytl_contact = '';
        $daykey_contact_category = '';
        $daykey_contact = '';
        $resiprimary_contact_category = '';
        $resiprimary_contact = '';
        $resitl_contact_category = '';
        $resitl_contact = '';
        $resikey_contact_category = '';
        $resikey_contact = '';
        $guardianprimary_contact_category = '';
        $guardianprimary_contact = '';
        $guardiansecondary_contact_category = '';
        $guardiansecondary_contact = '';
        $guardianalt_contact_category = '';
        $guardianalt_contact = '';
        $eme_contact_category = '';
        $eme_contact = '';
        $isp_start_date = '';
        $isp_review_date = '';
        $isp_end_date = '';
        $isp_quality = '';
        $isp_goals = '';
        $isp_needs = '';
        $isp_strategies = '';
        $isp_objectives = '';
        $isp_sis = '';
        $isp_detail_responsible_contact_category = '';
        $isp_detail_responsible_contact = '';
        $isp_updates = '';
        $isp_notes = '';
        $acc_day_program = '';
        $acc_isp_detail = '';
        $acc_isp_notes = '';

        $support_contact_gender = '';
        $support_contact_school = '';
        $support_contact_grade = '';
        $support_contact_diagnosis = '';
        $support_contact_date_of_birth = '';
        $support_contact_other_supports = '';

        $daycoordinator_contact_category = '';
        $daycoordinator_contact = '';
        $daycoordinator_contact_hours = '';
        $daycoordinator_contact_phone = '';
        $daycoordinator_contact_email = '';

        $daysl_contact_category = '';
        $daysl_contact = '';
        $daysl_contact_hours = '';
        $daysl_contact_phone = '';
        $daysl_contact_email = '';

        $dayot_contact_category = '';
        $dayot_contact = '';
        $dayot_contact_hours = '';
        $dayot_contact_phone = '';
        $dayot_contact_email = '';

        $daypp_contact_category = '';
        $daypp_contact = '';
        $daypp_contact_hours = '';
        $daypp_contact_phone = '';
        $daypp_contact_email = '';

        $dayphysio_contact_category = '';
        $dayphysio_contact = '';
        $dayphysio_contact_hours = '';
        $dayphysio_contact_phone = '';
        $dayphysio_contact_email = '';

        $dayaide_contact_category = '';
        $dayaide_contact = '';
        $dayaide_contact_hours = '';
        $dayaide_contact_phone = '';
        $dayaide_contact_email = '';

        $dayfscd_contact_category = '';
        $dayfscd_contact = '';
        $dayfscd_contact_hours = '';
        $dayfscd_contact_phone = '';
        $dayfscd_contact_email = '';

        $goal1_date = '';
        $goal1_outcomes = '';
        $goal2_date = '';
        $goal2_outcomes = '';
        $goal3_date = '';
        $goal3_outcomes = '';
        $goal4_date = '';
        $goal4_outcomes = '';
        $longterm_goal1_notes = '';

        $rating_behaviour_objective = '';
        $rating_behaviour_child = '';
        $rating_behaviour_child_date = '';
        $rating_behaviour_child_rating = '';
        $rating_behaviour_family = '';
        $rating_behaviour_family_date = '';
        $rating_behaviour_family_rating = '';
        $rating_behaviour_targeted = '';
        $rating_behaviour_targeted_date = '';
        $rating_behaviour_targeted_rating = '';
        $rating_behaviour_strategies_individual = '';
        $rating_behaviour_strategies_family = '';
        $rating_behaviour_review_date = '';
        $rating_behaviour_parent_update = '';
        $rating_behaviour_therapist_update = '';
        $rating_behaviour_aide_update = '';
        $rating_behaviour_next_step = '';

        $rating_comm_objective = '';
        $rating_comm_child = '';
        $rating_comm_child_date = '';
        $rating_comm_child_rating = '';
        $rating_comm_family = '';
        $rating_comm_family_date = '';
        $rating_comm_family_rating = '';
        $rating_comm_targeted = '';
        $rating_comm_targeted_date = '';
        $rating_comm_targeted_rating = '';
        $rating_comm_strategies_individual = '';
        $rating_comm_strategies_family = '';
        $rating_comm_review_date = '';
        $rating_comm_parent_update = '';
        $rating_comm_therapist_update = '';
        $rating_comm_aide_update = '';
        $rating_comm_next_step = '';

        $rating_physical_objective = '';
        $rating_physical_child = '';
        $rating_physical_child_date = '';
        $rating_physical_child_rating = '';
        $rating_physical_family = '';
        $rating_physical_family_date = '';
        $rating_physical_family_rating = '';
        $rating_physical_targeted = '';
        $rating_physical_targeted_date = '';
        $rating_physical_targeted_rating = '';
        $rating_physical_strategies_individual = '';
        $rating_physical_strategies_family = '';
        $rating_physical_review_date = '';
        $rating_physical_parent_update = '';
        $rating_physical_therapist_update = '';
        $rating_physical_aide_update = '';
        $rating_physical_next_step = '';

        $rating_cognitive_objective = '';
        $rating_cognitive_child = '';
        $rating_cognitive_child_date = '';
        $rating_cognitive_child_rating = '';
        $rating_cognitive_family = '';
        $rating_cognitive_family_date = '';
        $rating_cognitive_family_rating = '';
        $rating_cognitive_targeted = '';
        $rating_cognitive_targeted_date = '';
        $rating_cognitive_targeted_rating = '';
        $rating_cognitive_strategies_individual = '';
        $rating_cognitive_strategies_family = '';
        $rating_cognitive_review_date = '';
        $rating_cognitive_parent_update = '';
        $rating_cognitive_therapist_update = '';
        $rating_cognitive_aide_update = '';
        $rating_cognitive_next_step = '';

        $rating_safety_objective = '';
        $rating_safety_child = '';
        $rating_safety_child_date = '';
        $rating_safety_child_rating = '';
        $rating_safety_family = '';
        $rating_safety_family_date = '';
        $rating_safety_family_rating = '';
        $rating_safety_targeted = '';
        $rating_safety_targeted_date = '';
        $rating_safety_targeted_rating = '';
        $rating_safety_strategies_individual = '';
        $rating_safety_strategies_family = '';
        $rating_safety_review_date = '';
        $rating_safety_parent_update = '';
        $rating_safety_therapist_update = '';
        $rating_safety_aide_update = '';
        $rating_safety_next_step = '';

        $signatures_parent = '';
        $signatures_parent_name = '';
        $signatures_parent_date = '';
        $signatures_coordinator = '';
        $signatures_coordinator_name = '';
        $signatures_coordinator_date = '';
        $signatures_sl = '';
        $signatures_sl_name = '';
        $signatures_sl_date = '';
        $signatures_ot = '';
        $signatures_ot_name = '';
        $signatures_ot_date = '';
        $signatures_pp = '';
        $signatures_pp_name = '';
        $signatures_pp_date = '';
        $signatures_physio = '';
        $signatures_physio_name = '';
        $signatures_physio_date = '';
        $signatures_aide = '';
        $signatures_aide_name = '';
        $signatures_aide_date = '';

        if($_GET['acc'] == 'day_program') {
            $acc_day_program = ' in';
        }
        if($_GET['acc'] == 'isp_detail') {
            $acc_isp_detail = ' in';
        }
        if($_GET['acc'] == 'isp_notes') {
            $acc_isp_notes = ' in';
        }

        if(!empty($_GET['individualsupportplanid'])) {

            $individualsupportplanid = $_GET['individualsupportplanid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM individual_support_plan WHERE individualsupportplanid='$individualsupportplanid'"));

            $support_contact_category = $get_contact['support_contact_category'];
            $support_contact = $get_contact['support_contact'];
            $dayprimary_contact_category = $get_contact['dayprimary_contact_category'];
            $dayprimary_contact = $get_contact['dayprimary_contact'];
            $daytl_contact_category = $get_contact['daytl_contact_category'];
            $daytl_contact = $get_contact['daytl_contact'];
            $daykey_contact_category = $get_contact['daykey_contact_category'];
            $daykey_contact = $get_contact['daykey_contact'];
            $resiprimary_contact_category = $get_contact['resiprimary_contact_category'];
            $resiprimary_contact = $get_contact['resiprimary_contact'];
            $resitl_contact_category = $get_contact['resitl_contact_category'];
            $resitl_contact = $get_contact['resitl_contact'];
            $resikey_contact_category = $get_contact['resikey_contact_category'];
            $resikey_contact = $get_contact['resikey_contact'];
            $guardianprimary_contact_category = $get_contact['guardianprimary_contact_category'];
            $guardianprimary_contact = $get_contact['guardianprimary_contact'];
            $guardiansecondary_contact_category = $get_contact['guardiansecondary_contact_category'];
            $guardiansecondary_contact = $get_contact['guardiansecondary_contact'];
            $guardianalt_contact_category = $get_contact['guardianalt_contact_category'];
            $guardianalt_contact = $get_contact['guardianalt_contact'];
            $eme_contact_category = $get_contact['eme_contact_category'];
            $eme_contact = $get_contact['eme_contact'];
            $isp_start_date = $get_contact['isp_start_date'];
            $isp_review_date = $get_contact['isp_review_date'];
            $isp_end_date = $get_contact['isp_end_date'];
            $isp_quality = $get_contact['isp_quality'];
            $isp_goals = explode('*#*', $get_contact['isp_goals']);
            $isp_needs = $get_contact['isp_needs'];
            $isp_strategies = $get_contact['isp_strategies'];
            $isp_objectives = $get_contact['isp_objectives'];
            $isp_sis = $get_contact['isp_sis'];
            $isp_detail_responsible_contact_category = $get_contact['isp_detail_responsible_contact_category'];
            $isp_detail_responsible_contact = $get_contact['isp_detail_responsible_contact'];
            $isp_updates = $get_contact['isp_updates'];
            $isp_notes = $get_contact['isp_notes'];

	        $support_contact_gender = $get_contact['support_contact_gender'];
	        $support_contact_school = $get_contact['support_contact_school'];
	        $support_contact_grade = $get_contact['support_contact_grade'];
	        $support_contact_diagnosis = $get_contact['support_contact_diagnosis'];
	        $support_contact_date_of_birth = $get_contact['support_contact_date_of_birth'];
	        $support_contact_other_supports = $get_contact['support_contact_other_supports'];

	        $daycoordinator_contact_category = $get_contact['daycoordinator_contact_category'];
	        $daycoordinator_contact = $get_contact['daycoordinator_contact'];
	        $daycoordinator_contact_hours = $get_contact['daycoordinator_contact_hours'];
	        $daycoordinator_contact_phone = $get_contact['daycoordinator_contact_phone'];
	        $daycoordinator_contact_email = $get_contact['daycoordinator_contact_email'];

	        $daysl_contact_category = $get_contact['daysl_contact_category'];
	        $daysl_contact = $get_contact['daysl_contact'];
	        $daysl_contact_hours = $get_contact['daysl_contact_hours'];
	        $daysl_contact_phone = $get_contact['daysl_contact_phone'];
	        $daysl_contact_email = $get_contact['daysl_contact_email'];

	        $dayot_contact_category = $get_contact['dayot_contact_category'];
	        $dayot_contact = $get_contact['dayot_contact'];
	        $dayot_contact_hours = $get_contact['dayot_contact_hours'];
	        $dayot_contact_phone = $get_contact['dayot_contact_phone'];
	        $dayot_contact_email = $get_contact['dayot_contact_email'];

	        $daypp_contact_category = $get_contact['daypp_contact_category'];
	        $daypp_contact = $get_contact['daypp_contact'];
	        $daypp_contact_hours = $get_contact['daypp_contact_hours'];
	        $daypp_contact_phone = $get_contact['daypp_contact_phone'];
	        $daypp_contact_email = $get_contact['daypp_contact_email'];

	        $dayphysio_contact_category = $get_contact['dayphysio_contact_category'];
	        $dayphysio_contact = $get_contact['dayphysio_contact'];
	        $dayphysio_contact_hours = $get_contact['dayphysio_contact_hours'];
	        $dayphysio_contact_phone = $get_contact['dayphysio_contact_phone'];
	        $dayphysio_contact_email = $get_contact['dayphysio_contact_email'];

	        $dayaide_contact_category = $get_contact['dayaide_contact_category'];
	        $dayaide_contact = $get_contact['dayaide_contact'];
	        $dayaide_contact_hours = $get_contact['dayaide_contact_hours'];
	        $dayaide_contact_phone = $get_contact['dayaide_contact_phone'];
	        $dayaide_contact_email = $get_contact['dayaide_contact_email'];

	        $dayfscd_contact_category = $get_contact['dayfscd_contact_category'];
	        $dayfscd_contact = $get_contact['dayfscd_contact'];
	        $dayfscd_contact_hours = $get_contact['dayfscd_contact_hours'];
	        $dayfscd_contact_phone = $get_contact['dayfscd_contact_phone'];
	        $dayfscd_contact_email = $get_contact['dayfscd_contact_email'];

	        $goal1_date = $get_contact['goal1_date'];
	        $goal1_outcomes = $get_contact['goal1_outcomes'];
	        $goal2_date = $get_contact['goal2_date'];
	        $goal2_outcomes = $get_contact['goal2_outcomes'];
	        $goal3_date = $get_contact['goal3_date'];
	        $goal3_outcomes = $get_contact['goal3_outcomes'];
	        $goal4_date = $get_contact['goal4_date'];
	        $goal4_outcomes = $get_contact['goal4_outcomes'];
	        $longterm_goal1_notes = $get_contact['longterm_goal1_notes'];

	        $rating_behaviour_objective = $get_contact['rating_behaviour_objective'];
	        $rating_behaviour_child = $get_contact['rating_behaviour_child'];
	        $rating_behaviour_child_date = $get_contact['rating_behaviour_child_date'];
	        $rating_behaviour_child_rating = $get_contact['rating_behaviour_child_rating'];
	        $rating_behaviour_family = $get_contact['rating_behaviour_family'];
	        $rating_behaviour_family_date = $get_contact['rating_behaviour_family_date'];
	        $rating_behaviour_family_rating = $get_contact['rating_behaviour_family_rating'];
	        $rating_behaviour_targeted = $get_contact['rating_behaviour_targeted'];
	        $rating_behaviour_targeted_date = $get_contact['rating_behaviour_targeted_date'];
	        $rating_behaviour_targeted_rating = $get_contact['rating_behaviour_targeted_rating'];
	        $rating_behaviour_strategies_individual = $get_contact['rating_behaviour_strategies_individual'];
	        $rating_behaviour_strategies_family = $get_contact['rating_behaviour_strategies_family'];
	        $rating_behaviour_review_date = $get_contact['rating_behaviour_review_date'];
	        $rating_behaviour_parent_update = $get_contact['rating_behaviour_parent_update'];
	        $rating_behaviour_therapist_update = $get_contact['rating_behaviour_therapist_update'];
	        $rating_behaviour_aide_update = $get_contact['rating_behaviour_aide_update'];
	        $rating_behaviour_next_step = $get_contact['rating_behaviour_next_step'];

	        $rating_comm_objective = $get_contact['rating_comm_objective'];
	        $rating_comm_child = $get_contact['rating_comm_child'];
	        $rating_comm_child_date = $get_contact['rating_comm_child_date'];
	        $rating_comm_child_rating = $get_contact['rating_comm_child_rating'];
	        $rating_comm_family = $get_contact['rating_comm_family'];
	        $rating_comm_family_date = $get_contact['rating_comm_family_date'];
	        $rating_comm_family_rating = $get_contact['rating_comm_family_rating'];
	        $rating_comm_targeted = $get_contact['rating_comm_targeted'];
	        $rating_comm_targeted_date = $get_contact['rating_comm_targeted_date'];
	        $rating_comm_targeted_rating = $get_contact['rating_comm_targeted_rating'];
	        $rating_comm_strategies_individual = $get_contact['rating_comm_strategies_individual'];
	        $rating_comm_strategies_family = $get_contact['rating_comm_strategies_family'];
	        $rating_comm_review_date = $get_contact['rating_comm_review_date'];
	        $rating_comm_parent_update = $get_contact['rating_comm_parent_update'];
	        $rating_comm_therapist_update = $get_contact['rating_comm_therapist_update'];
	        $rating_comm_aide_update = $get_contact['rating_comm_aide_update'];
	        $rating_comm_next_step = $get_contact['rating_comm_next_step'];

	        $rating_physical_objective = $get_contact['rating_physical_objective'];
	        $rating_physical_child = $get_contact['rating_physical_child'];
	        $rating_physical_child_date = $get_contact['rating_physical_child_date'];
	        $rating_physical_child_rating = $get_contact['rating_physical_child_rating'];
	        $rating_physical_family = $get_contact['rating_physical_family'];
	        $rating_physical_family_date = $get_contact['rating_physical_family_date'];
	        $rating_physical_family_rating = $get_contact['rating_physical_family_rating'];
	        $rating_physical_targeted = $get_contact['rating_physical_targeted'];
	        $rating_physical_targeted_date = $get_contact['rating_physical_targeted_date'];
	        $rating_physical_targeted_rating = $get_contact['rating_physical_targeted_rating'];
	        $rating_physical_strategies_individual = $get_contact['rating_physical_strategies_individual'];
	        $rating_physical_strategies_family = $get_contact['rating_physical_strategies_family'];
	        $rating_physical_review_date = $get_contact['rating_physical_review_date'];
	        $rating_physical_parent_update = $get_contact['rating_physical_parent_update'];
	        $rating_physical_therapist_update = $get_contact['rating_physical_therapist_update'];
	        $rating_physical_aide_update = $get_contact['rating_physical_aide_update'];
	        $rating_physical_next_step = $get_contact['rating_physical_next_step'];

	        $rating_cognitive_objective = $get_contact['rating_cognitive_objective'];
	        $rating_cognitive_child = $get_contact['rating_cognitive_child'];
	        $rating_cognitive_child_date = $get_contact['rating_cognitive_child_date'];
	        $rating_cognitive_child_rating = $get_contact['rating_cognitive_child_rating'];
	        $rating_cognitive_family = $get_contact['rating_cognitive_family'];
	        $rating_cognitive_family_date = $get_contact['rating_cognitive_family_date'];
	        $rating_cognitive_family_rating = $get_contact['rating_cognitive_family_rating'];
	        $rating_cognitive_targeted = $get_contact['rating_cognitive_targeted'];
	        $rating_cognitive_targeted_date = $get_contact['rating_cognitive_targeted_date'];
	        $rating_cognitive_targeted_rating = $get_contact['rating_cognitive_targeted_rating'];
	        $rating_cognitive_strategies_individual = $get_contact['rating_cognitive_strategies_individual'];
	        $rating_cognitive_strategies_family = $get_contact['rating_cognitive_strategies_family'];
	        $rating_cognitive_review_date = $get_contact['rating_cognitive_review_date'];
	        $rating_cognitive_parent_update = $get_contact['rating_cognitive_parent_update'];
	        $rating_cognitive_therapist_update = $get_contact['rating_cognitive_therapist_update'];
	        $rating_cognitive_aide_update = $get_contact['rating_cognitive_aide_update'];
	        $rating_cognitive_next_step = $get_contact['ratin_cognitive_next_step'];

	        $rating_safety_objective = $get_contact['rating_safety_objective'];
	        $rating_safety_child = $get_contact['rating_safety_child'];
	        $rating_safety_child_date = $get_contact['rating_safety_child_date'];
	        $rating_safety_child_rating = $get_contact['rating_safety_child_rating'];
	        $rating_safety_family = $get_contact['rating_safety_family'];
	        $rating_safety_family_date = $get_contact['rating_safety_family_date'];
	        $rating_safety_family_rating = $get_contact['rating_safety_family_rating'];
	        $rating_safety_targeted = $get_contact['rating_safety_targeted'];
	        $rating_safety_targeted_date = $get_contact['rating_safety_targeted_date'];
	        $rating_safety_targeted_rating = $get_contact['rating_safety_targeted_rating'];
	        $rating_safety_strategies_individual = $get_contact['rating_safety_strategies_individual'];
	        $rating_safety_strategies_family = $get_contact['rating_safety_strategies_family'];
	        $rating_safety_review_date = $get_contact['rating_safety_review_date'];
	        $rating_safety_parent_update = $get_contact['rating_safety_parent_update'];
	        $rating_safety_therapist_update = $get_contact['rating_safety_therapist_update'];
	        $rating_safety_aide_update = $get_contact['rating_safety_aide_update'];
	        $rating_safety_next_step = $get_contact['rating_safety_next_step'];

	        $signatures_parent = $get_contact['signatures_parent'];
	        $signatures_parent_name = $get_contact['signatures_parent_name'];
	        $signatures_parent_date = $get_contact['signatures_parent_date'];
	        $signatures_coordinator = $get_contact['signatures_coordinator'];
	        $signatures_coordinator_name = $get_contact['signatures_coordinator_name'];
	        $signatures_coordinator_date = $get_contact['signatures_coordinator_date'];
	        $signatures_sl = $get_contact['signatures_sl'];
	        $signatures_sl_name = $get_contact['signatures_sl_name'];
	        $signatures_sl_date = $get_contact['signatures_sl_date'];
	        $signatures_ot = $get_contact['signatures_ot'];
	        $signatures_ot_name = $get_contact['signatures_ot_name'];
	        $signatures_ot_date = $get_contact['signatures_ot_date'];
	        $signatures_pp = $get_contact['signatures_pp'];
	        $signatures_pp_name = $get_contact['signatures_pp_name'];
	        $signatures_pp_date = $get_contact['signatures_pp_date'];
	        $signatures_physio = $get_contact['signatures_physio'];
	        $signatures_physio_name = $get_contact['signatures_physio_name'];
	        $signatures_physio_date = $get_contact['signatures_physio_date'];
	        $signatures_aide = $get_contact['signatures_aide'];
	        $signatures_aide_name = $get_contact['signatures_aide_name'];
	        $signatures_aide_date = $get_contact['signatures_aide_date'];


        ?>
        <input type="hidden" id="individualsupportplanid" name="individualsupportplanid" value="<?php echo $individualsupportplanid ?>" />
        <?php   }      ?>

<script>
var default_contact_list = '';
function contact_clone(btn) {
	var contact = $(btn).closest('.contact_group').clone();
	contact.find('select,input').val('');

	if(default_contact_list != '') {
		contact.find('select:not([name*=category])').html(default_contact_list)
	}
	resetChosen(contact.find("select"));

	var group = $(btn).closest('.contact_group');
	while(group.next('.contact_group').length > 0) {
		group = group.next('.contact_group');
	}
	group.after(contact);
}
function contact_remove(btn) {
	if($(btn).closest('.contact_group').next('h3').length == 1 && $(btn).closest('.contact_group').prev('h3').length == 1) {
		contact_clone(btn);
	}
	$(btn).closest('.contact_group').remove();
}
function addSignature(div) {
	var block = $('.'+div).last();
	var clone = $(block).clone();
	clone.find('.form-control').val('');
    clone.find('.datepicker').attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '1920:2025',
		dateFormat: 'yy-mm-dd',
    });
	block.after(clone);

    var options = {
      drawOnly : true,
      validateFields : false
    };
    $('.sigPad').signaturePad(options);
    $('#linear').signaturePad({drawOnly:true, lineTop:200});
    $('#smoothed').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:200});
    $('#smoothed-variableStrokeWidth').signaturePad({drawOnly:true, drawBezierCurves:true, variableStrokeWidth:true, lineTop:200});
}
function removeSignature(div, btn) {
	if($('.'+div).length <= 1) {
		addSignature(div);
	}
	$(btn).closest($('.'+div)).remove();
}
</script>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse1" >
                        Service Individual<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body contact_group">

                    <?php
                    echo contact_category_call($dbc, 'contact_category_0', 'support_contact_category', $support_contact_category); ?>

                    <?php echo contact_call($dbc, 'contact_0', 'support_contact', $support_contact, '',$support_contact_category); ?>

			        <?php if (strpos($value_config, ',Service Individual Gender,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">Gender</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_gender" class="form-control" value="<?= $support_contact_gender ?>">
			        		</div>
			        	</div>
			        <?php } ?>
			        <?php if (strpos($value_config, ',Service Individual School,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">School</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_school" class="form-control" value="<?= $support_contact_school ?>">
			        		</div>
			        	</div>
			        <?php } ?>
			        <?php if (strpos($value_config, ',Service Individual Grade/Class,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">Grade/Class</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_grade" class="form-control" value="<?= $support_contact_grade ?>">
			        		</div>
			        	</div>
			        <?php } ?>
			        <?php if (strpos($value_config, ',Service Individual Diagnosis,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">Diagnosis</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_diagnosis" class="form-control" value="<?= $support_contact_diagnosis ?>">
			        		</div>
			        	</div>
			        <?php } ?>
			        <?php if (strpos($value_config, ',Service Individual Date of Birth,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">Date of Birth</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_date_of_birth" class="form-control datepicker" value="<?= $support_contact_date_of_birth ?>">
			        		</div>
			        	</div>
			        <?php } ?>
			        <?php if (strpos($value_config, ',Service Individual Other Supports,') !== FALSE) { ?>
			        	<div class="form-group">
			        		<label class="col-sm-4 control-label">Other Supports</label>
			        		<div class="col-sm-8">
			        			<input type="text" name="support_contact_other_supports" class="form-control" value="<?= $support_contact_other_supports ?>">
			        		</div>
			        	</div>
			        <?php } ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ',Day Program Support Team,') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse2" >
	                        Day Program Support Team<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse2" class="panel-collapse collapse <?php echo $acc_day_program; ?>">
	                <div class="panel-body">

	                <?php if (strpos($value_config, ',Day Program Support Team Primary Contact,') !== FALSE) { ?>
	    				<h3>Primary Contact</h3>
	    				<?php foreach(explode(',',$dayprimary_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_1', 'dayprimary_contact_category[]', explode(',',$dayprimary_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_1', 'dayprimary_contact[]', $multicontactid, '',explode(',',$dayprimary_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Lead,') !== FALSE) { ?>
	    				<h3>Team Lead</h3>
	    				<?php foreach(explode(',',$daytl_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_2', 'daytl_contact_category[]', explode(',',$daytl_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_2', 'daytl_contact[]', $multicontactid, '',explode(',',$daytl_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Key Supports,') !== FALSE) { ?>
	                    <h3>Key Supports</h3>

	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_3', 'daykey_contact_category', $daykey_contact_category); ?>

	    						<?php echo contact_call($dbc, 'contact_3', 'daykey_contact[]', $daykey_contact, '',$daykey_contact_category); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Coordinator,') !== FALSE) { ?>
	                    <h3>Coordinator</h3>
	    				<?php foreach(explode(',',$daycoordinator_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_4', 'daycoordinator_contact_category[]', explode(',',$daycoordinator_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_4', 'daycoordinator_contact[]', $multicontactid, '',explode(',',$daycoordinator_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daycoordinator_contact_hours[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daycoordinator_contact_phone[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daycoordinator_contact_email[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Speech-Language Pathologist,') !== FALSE) { ?>
	                    <h3>Speech-Language Pathologist</h3>
	    				<?php foreach(explode(',',$daysl_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_5', 'daysl_contact_category[]', explode(',',$daysl_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_5', 'daysl_contact[]', $multicontactid, '',explode(',',$daysl_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daysl_contact_hours[]" class="form-control" value="<?= explode('*#*', $daysl_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daysl_contact_phone[]" class="form-control" value="<?= explode('*#*', $daysl_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daysl_contact_email[]" class="form-control" value="<?= explode('*#*', $daysl_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>

	                <?php if (strpos($value_config, ',Day Program Support Team Speech-Language Pathologist,') !== FALSE) { ?>
	                    <h3>Speech-Language Pathologist</h3>
	    				<?php foreach(explode(',',$dayot_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_6', 'dayot_contact_category[]', explode(',',$dayot_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_6', 'dayot_contact[]', $multicontactid, '',explode(',',$dayot_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayot_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayot_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayot_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayot_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayot_contact_email[]" class="form-control" value="<?= explode('*#*', $dayot_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Provisional Psychologist,') !== FALSE) { ?>
	                    <h3>Provisional Psychologist</h3>
	    				<?php foreach(explode(',',$daypp_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_7', 'daypp_contact_category[]', explode(',',$daypp_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_7', 'daypp_contact[]', $multicontactid, '',explode(',',$daypp_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daypp_contact_hours[]" class="form-control" value="<?= explode('*#*', $daypp_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daypp_contact_phone[]" class="form-control" value="<?= explode('*#*', $daypp_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="daypp_contact_email[]" class="form-control" value="<?= explode('*#*', $daypp_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team Aides,') !== FALSE) { ?>
	                    <h3>Aide(s)</h3>
	    				<?php foreach(explode(',',$dayaide_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_8', 'dayaide_contact_category[]', explode(',',$dayaide_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_8', 'dayaide_contact[]', $multicontactid, '',explode(',',$dayaide_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayaide_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayaide_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayaide_contact_email[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Day Program Support Team FSCD Worker,') !== FALSE) { ?>
	                    <h3>FSCD Worker</h3>
	    				<?php foreach(explode(',',$dayfscd_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_9', 'dayfscd_contact_category[]', explode(',',$dayfscd_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_9', 'dayfscd_contact[]', $multicontactid, '',explode(',',$dayfscd_contact_category)[$i]); ?>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Hours</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayfscd_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_hours)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Phone #</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayfscd_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_phone)[$i] ?>">
	    							</div>
	    						</div>
	    						<div class="form-group">
	    							<label class="col-sm-4 control-label">Email Address</label>
	    							<div class="col-sm-8">
	    								<input type="text" name="dayfscd_contact_email[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_email)[$i] ?>">
	    							</div>
	    						</div>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
    					<?php } ?>
	                <?php } ?>

                    </div>
	            </div>
	        </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Residential Support Team,') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3" >
	                        Residential Support Team<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse3" class="panel-collapse collapse">
	                <div class="panel-body">

	                <?php if (strpos($value_config, ',Residential Support Team Primary Contact,') !== FALSE) { ?>
	    				<h3>Primary Contact</h3>
	    				<?php foreach(explode(',',$resiprimary_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_4', 'resiprimary_contact_category[]', explode(',',$resiprimary_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_4', 'resiprimary_contact[]', $multicontactid,'',explode(',',$resiprimary_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Residential Support Team Lead,') !== FALSE) { ?>
	    				<h3>Team Lead</h3>
	    				<?php foreach(explode(',',$resitl_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_5', 'resitl_contact_category[]', explode(',',$resitl_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_5', 'resitl_contact[]', $multicontactid,'',explode(',',$resitl_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Residential Support Team Key Supports,') !== FALSE) { ?>
	                    <h3>Key Supports</h3>

	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_6', 'resikey_contact_category', $resikey_contact_category); ?>
	    						<?php echo contact_call($dbc, 'contact_6', 'resikey_contact[]', $resikey_contact, '',$resikey_contact_category); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	                <?php } ?>

                    </div>
	            </div>
	        </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Guardian,') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse35" >
	                        Guardian<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse35" class="panel-collapse collapse">
	                <div class="panel-body">

	                <?php if (strpos($value_config, ',Guardian Primary Contact,') !== FALSE) { ?>
	    				<h3>Primary Contact</h3>
	    				<?php foreach(explode(',',$guardianprimary_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_7', 'guardianprimary_contact_category[]', explode(',',$guardianprimary_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_7', 'guardianprimary_contact[]', $multicontactid,'',explode(',',$guardianprimary_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Guardian Secondary Contact,') !== FALSE) { ?>
	    				<h3>Secondary Contact</h3>
	    				<?php foreach(explode(',',$guardiansecondary_contact) as $i => $multicontactid) { ?>
	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_8', 'guardiansecondary_contact_category[]', explode(',',$guardiansecondary_contact_category)[$i]); ?>

	    						<?php echo contact_call($dbc, 'contact_8', 'guardiansecondary_contact[]', $multicontactid,'',explode(',',$guardiansecondary_contact_category)[$i]); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	    				<?php } ?>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Guardian Alternates,') !== FALSE) { ?>
	                    <h3>Alternates</h3>

	    					<div class="contact_group">
	    						<?php echo contact_category_call($dbc, 'contact_category_9', 'guardianalt_contact_category', $guardianalt_contact_category); ?>

	    						<?php echo contact_call($dbc, 'contact_9', 'guardianalt_contact[]', $guardianalt_contact, '',$guardianalt_contact_category); ?>
	    						<span class="pull-right">
	    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
	    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
	    						</span>
	    						<div class="clearfix"></div>
	    					</div>
	                <?php } ?>

                    </div>
	            </div>
	        </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Family Support Goals,') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_goals" >
                            Family Support Goals<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_goals" class="panel-collapse collapse">
                    <div class="panel-body">

	                <?php if (strpos($value_config, ',Family Support Goals Goal 1,') !== FALSE) { ?>
	                	<h3>Goal #1: Connect family to appropriate community resources and build on existing supports.</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date Discussed:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="goal1_date" class="form-control datepicker" value="<?= $goal1_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Outcomes:</label>
	                		<div class="col-sm-8">
		                        <textarea name="goal1_outcomes" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($goal1_outcomes); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Family Support Goals Goal 2,') !== FALSE) { ?>
	                	<h3>Goal #2: Provide information about safety for children:
	                		<ol type="a">
	                			<li>Options for carrying identification</li>
	                			<li>Vulnerable persons registry</li>
	                			<li>Environmental adaptations</li>
	                		</ol>
                		</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date Discussed:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="goal2_date" class="form-control datepicker" value="<?= $goal2_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Outcomes:</label>
	                		<div class="col-sm-8">
		                        <textarea name="goal2_outcomes" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($goal2_outcomes); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Family Support Goals Goal 3,') !== FALSE) { ?>
	                	<h3>Goal #3: Create a one-page profile that can easily be shared with others involved in the child's care. Ensure parent's know how to update and use this tool.</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date Discussed:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="goal3_date" class="form-control datepicker" value="<?= $goal3_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Outcomes:</label>
	                		<div class="col-sm-8">
		                        <textarea name="goal3_outcomes" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($goal3_outcomes); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Family Support Goals Goal 4,') !== FALSE) { ?>
	                	<h3>Goal #4: Provide information about:
	                		<ol type="a">
	                			<li>Typical development</li>
	                			<li>How the child's diagnosis impacts his/her development</li>
	                			<li>What parents can expect across the lifespan</li>
	                		</ol>
	                	</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date Discussed:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="goal4_date" class="form-control datepicker" value="<?= $goal4_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Outcomes:</label>
	                		<div class="col-sm-8">
		                        <textarea name="goal4_outcomes" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($goal4_outcomes); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Family Support Goals Long Term Goal 1,') !== FALSE) { ?>
	                	<h3>Long Term Goal #1: In order to meet this goal that has been identified by the family, the following objectives need to be achieved</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Notes:</label>
	                		<div class="col-sm-8">
		                        <textarea name="longterm_goal1_notes" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($longterm_goal1_notes); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>

                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Emergency Contacts,') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse4" >
                            Emergency Contacts<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse4" class="panel-collapse collapse">
                    <div class="panel-body">

    					<div class="contact_group">
    						<?php echo contact_category_call($dbc, 'contact_category_10', 'eme_contact_category', $eme_contact_category); ?>

    						<?php echo contact_call($dbc, 'contact_10', 'eme_contact[]', $eme_contact, '',$eme_contact_category); ?>
    						<span class="pull-right">
    							<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
    							<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
    						</span>
    						<div class="clearfix"></div>
    					</div>

                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Parent Rating,') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_parentrating" >
                            Parent Rating<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_parentrating" class="panel-collapse collapse">
                    <div class="panel-body">

	                <?php if (strpos($value_config, ',Parent Rating Note,') !== FALSE) { ?>
	                	<h3>NOTE: Parent rating refers to parents satisfaction with their own knowledge and skills in this area. Based on a scale of 1 to 5, 1 being not satisfied at all (no knowledge or skills), 5 being very satisfied (self-reported to be knowledgeable enough and able to use skills in their environments).</h3>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Parent Rating Behaviour,') !== FALSE) { ?>
	                	<h3>Behaviour</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Objective #:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_objective" class="form-control" value="<?= $rating_behaviour_objective ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Child Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_behaviour_child" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_child); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_child_date" class="form-control datepicker" value="<?= $rating_behaviour_child_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_child_rating" class="form-control" value="<?= $rating_behaviour_child_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Family Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_behaviour_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_family); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_family_date" class="form-control datepicker" value="<?= $rating_behaviour_family_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_family_rating" class="form-control" value="<?= $rating_behaviour_family_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Targeted Outcomes:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_behaviour_targeted" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_targeted); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_targeted_date" class="form-control datepicker" value="<?= $rating_behaviour_targeted_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_targeted_rating" class="form-control" value="<?= $rating_behaviour_targeted_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the individual:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_strategies_individual" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_strategies_individual); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the family:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_strategies_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_strategies_family); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Review Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_behaviour_review_date" class="form-control datepicker" value="<?= $rating_behaviour_review_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Parent Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_parent_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_parent_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Therapist Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_therapist_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_therapist_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Aide Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_aide_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_aide_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Next Step:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_behaviour_next_step" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_behaviour_next_step); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Parent Rating Communication & Social Skills,') !== FALSE) { ?>
	                	<h3>Communication & Social Skills</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Objective #:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_objective" class="form-control" value="<?= $rating_comm_objective ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Child Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_comm_child" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_child); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_child_date" class="form-control datepicker" value="<?= $rating_comm_child_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_child_rating" class="form-control" value="<?= $rating_comm_child_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Family Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_comm_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_family); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_family_date" class="form-control datepicker" value="<?= $rating_comm_family_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_family_rating" class="form-control" value="<?= $rating_comm_family_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Targeted Outcomes:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_comm_targeted" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_targeted); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_targeted_date" class="form-control datepicker" value="<?= $rating_comm_targeted_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_targeted_rating" class="form-control" value="<?= $rating_comm_targeted_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the individual:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_strategies_individual" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_strategies_individual); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the family:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_strategies_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_strategies_family); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Review Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_comm_review_date" class="form-control datepicker" value="<?= $rating_comm_review_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Parent Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_parent_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_parent_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Therapist Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_therapist_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_therapist_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Aide Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_aide_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_aide_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Next Step:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_comm_next_step" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_comm_next_step); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Parent Rating Physical Abilities,') !== FALSE) { ?>
	                	<h3>Physical Abilities</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Objective #:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_objective" class="form-control" value="<?= $rating_physical_objective ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Child Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_physical_child" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_child); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_child_date" class="form-control datepicker" value="<?= $rating_physical_child_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_child_rating" class="form-control" value="<?= $rating_physical_child_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Family Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_physical_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_family); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_family_date" class="form-control datepicker" value="<?= $rating_physical_family_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_family_rating" class="form-control" value="<?= $rating_physical_family_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Targeted Outcomes:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_physical_targeted" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_targeted); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_targeted_date" class="form-control datepicker" value="<?= $rating_physical_targeted_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_targeted_rating" class="form-control" value="<?= $rating_physical_targeted_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the individual:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_strategies_individual" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_strategies_individual); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the family:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_strategies_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_strategies_family); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Review Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_physical_review_date" class="form-control datepicker" value="<?= $rating_physical_review_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Parent Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_parent_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_parent_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Therapist Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_therapist_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_therapist_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Aide Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_aide_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_aide_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Next Step:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_physical_next_step" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_physical_next_step); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Parent Rating Cognitive Abilities,') !== FALSE) { ?>
	                	<h3>Cognitive Abilities</h3>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Objective #:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_objective" class="form-control" value="<?= $rating_cognitive_objective ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Child Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_cognitive_child" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_child); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_child_date" class="form-control datepicker" value="<?= $rating_cognitive_child_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_child_rating" class="form-control" value="<?= $rating_cognitive_child_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Family Baseline:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_cognitive_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_family); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_family_date" class="form-control datepicker" value="<?= $rating_cognitive_family_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_family_rating" class="form-control" value="<?= $rating_cognitive_family_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
		                	<label class="col-sm-4 control-label">Targeted Outcomes:</label>
		                	<div class="col-sm-8">
		                        <textarea name="rating_cognitive_targeted" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_targeted); ?></textarea>
		                	</div>
		                </div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_targeted_date" class="form-control datepicker" value="<?= $rating_cognitive_targeted_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Rating:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_targeted_rating" class="form-control" value="<?= $rating_cognitive_targeted_rating ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the individual:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_strategies_individual" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_strategies_individual); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Strategies to support the family:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_strategies_family" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_strategies_family); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Review Date:</label>
	                		<div class="col-sm-8">
	                			<input type="text" name="rating_cognitive_review_date" class="form-control datepicker" value="<?= $rating_cognitive_review_date ?>">
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Parent Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_parent_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_parent_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Therapist Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_therapist_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_therapist_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Aide Update:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_aide_update" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_aide_update); ?></textarea>
	                		</div>
	                	</div>
	                	<div class="form-group">
	                		<label class="col-sm-4 control-label">Next Step:</label>
	                		<div class="col-sm-8">
		                        <textarea name="rating_cognitive_next_step" rows="5" cols="50" class="form-control"><?php echo html_entity_decode($rating_cognitive_next_step); ?></textarea>
	                		</div>
	                	</div>
	                <?php } ?>

                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Dates & Timelines,') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse5" >
	                        Dates & Timelines<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse5" class="panel-collapse collapse">
	                <div class="panel-body">

	                    <?php if (strpos($value_config, ',ISP Start Date,') !== FALSE) { ?>
	                        <div class="form-group clearfix">
	                            <label for="first_name" class="col-sm-4 control-label text-right">ISP Start Date:</label>
	                            <div class="col-sm-8">
	                                <input name="isp_start_date" value="<?php echo $isp_start_date; ?>" type="text" class="datepicker">
	                            </div>
	                        </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',ISP Review Date,') !== FALSE) { ?>
	                        <div class="form-group clearfix">
	                            <label for="first_name" class="col-sm-4 control-label text-right">ISP Review Date:</label>
	                            <div class="col-sm-8">
	                                <input name="isp_review_date" value="<?php echo $isp_review_date; ?>" type="text" class="datepicker">
	                            </div>
	                        </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',ISP End Date,') !== FALSE) { ?>
	                        <div class="form-group clearfix">
	                            <label for="first_name" class="col-sm-4 control-label text-right">ISP End Date:</label>
	                            <div class="col-sm-8">
	                                <input name="isp_end_date" value="<?php echo $isp_end_date; ?>" type="text" class="datepicker">
	                            </div>
	                        </div>
	                    <?php } ?>

	                </div>
	            </div>
	        </div>
        <?php } ?>

        <?php if (strpos($value_config, ',ISP Details,') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse6" >
	                        ISP Details<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse6" class="panel-collapse collapse <?php echo $acc_isp_detail; ?>">
	                <div class="panel-body">

	                    <?php if (strpos($value_config, ',Quality of Life Outcomes,') !== FALSE) { ?>
	                       <div class="form-group" id="isp_quality_name">
	                        <label for="travel_task" class="col-sm-4 control-label">Quality of Life Outcomes:</label>
	                        <div class="col-sm-8">
	                            <input name="isp_quality_name" type="text" class="form-control" value="<?= $isp_quality ?>" />
	                        </div>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Goals,') !== FALSE) { ?>
	                       <div class="form-group" id="isp_goals_name">
	                        <label for="travel_task" class="col-sm-4 control-label">Goals:</label>
	                        <div class="col-sm-8">
	                            <?php if(!empty($isp_goals)) {
	                                foreach ($isp_goals as $isp_goal) { ?>
	                                    <input name="isp_goals_name[]" type="text" class="form-control" value="<?= $isp_goal ?>" />
	                                <?php }
	                            } else { ?>
	                                <input name="isp_goals_name[]" type="text" class="form-control" />
	                            <?php } ?>
	                        </div>
	                        <button id="add_another_goal" onclick="addAnotherGoal(this); return false;" class="btn brand-btn mobile-block pull-right">Add Another Goal</button>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Assessed Service Needs,') !== FALSE) { ?>
	                      <div class="form-group">
	                        <label for="first_name[]" class="col-sm-4 control-label">Assessed Service Needs:</label>
	                        <div class="col-sm-8">
	                          <textarea name="isp_needs" rows="5" cols="50" class="form-control"><?php echo $isp_needs; ?></textarea>
	                        </div>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Support Strategies,') !== FALSE) { ?>
	                      <div class="form-group">
	                        <label for="first_name[]" class="col-sm-4 control-label">Support Strategies:</label>
	                        <div class="col-sm-8">
	                          <textarea name="isp_strategies" rows="5" cols="50" class="form-control"><?php echo $isp_strategies; ?></textarea>
	                        </div>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Support Objectives,') !== FALSE) { ?>
	                      <div class="form-group">
	                        <label for="first_name[]" class="col-sm-4 control-label">Support Objectives:</label>
	                        <div class="col-sm-8">
	                          <textarea name="isp_objectives" rows="5" cols="50" class="form-control"><?php echo $isp_objectives; ?></textarea>
	                        </div>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',SIS Activity Areas,') !== FALSE) { ?>
	                       <div class="form-group" id="isp_sis_name">
	                        <label for="travel_task" class="col-sm-4 control-label">SIS Activity Areas/Items:</label>
	                        <div class="col-sm-8">
	                            <input name="isp_sis_name" type="text" class="form-control" value="<?= $isp_sis ?>" />
	                        </div>
	                      </div>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Who is Responsible,') !== FALSE) { ?>
	                        <h4>Who is Responsible</h4>
	                        <?php echo contact_category_call($dbc, 'contact_category_15', 'isp_detail_responsible_contact_category', $isp_detail_responsible_contact_category); ?>
	                        <?php echo contact_call($dbc, 'contact_15', 'isp_detail_responsible_contact[]', $isp_detail_responsible_contact, 'multiple',$isp_detail_responsible_contact_category); ?>
	                    <?php } ?>

	                    <?php if (strpos($value_config, ',Updates,') !== FALSE) { ?>
	                      <div class="form-group">
	                        <label for="first_name[]" class="col-sm-4 control-label">Updates:</label>
	                        <div class="col-sm-8">
	                          <textarea name="isp_updates" rows="5" cols="50" class="form-control"><?php echo $isp_updates; ?></textarea>
	                        </div>
	                      </div>
	                    <?php } ?>

	                </div>
	            </div>
	        </div>
        <?php } ?>

        <?php if (strpos($value_config, ',ISP Notes,') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse7" >
                            ISP Notes<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse7" class="panel-collapse collapse <?php echo $acc_isp_notes; ?>">
                    <div class="panel-body">

                      <div class="form-group">
                        <label for="first_name[]" class="col-sm-4 control-label">ISP Notes:</label>
                        <div class="col-sm-8">
                          <textarea name="isp_notes" rows="5" cols="50" class="form-control"><?php echo $isp_notes; ?></textarea>
                        </div>
                      </div>

                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ',Signatures,') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_signatures" >
                            Signatures<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_signatures" class="panel-collapse collapse">
                    <div class="panel-body">

	                <?php if (strpos($value_config, ',Signatures Parents,') !== FALSE) { ?>
	                	<h3>Parents</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_parent)) {
		                			$signatures_parent = explode('*#*', $signatures_parent);
		                			for ($sig_i = 0; $sig_i < count($signatures_parent); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_parent[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_parent_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_parent_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_parent">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_parent[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_parent_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_parent_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_parent');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_parent', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Coordinator,') !== FALSE) { ?>
	                	<h3>Coordinator</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_coordinator)) {
		                			$signatures_coordinator = explode('*#*', $signatures_coordinator);
		                			for ($sig_i = 0; $sig_i < count($signatures_coordinator); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_coordinator[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_coordinator_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_coordinator_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_coordinator">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_coordinator[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_coordinator_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_coordinator_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_coordinator');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_coordinator', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Speech-Language,') !== FALSE) { ?>
	                	<h3>Speech-Language</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_sl)) {
		                			$signatures_sl = explode('*#*', $signatures_sl);
		                			for ($sig_i = 0; $sig_i < count($signatures_sl); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_sl[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_sl_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_sl_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_sl">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_sl[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_sl_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_sl_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_sl');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_sl', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Occupational Therapist,') !== FALSE) { ?>
	                	<h3>Occupational Therapist</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_ot)) {
		                			$signatures_ot = explode('*#*', $signatures_ot);
		                			for ($sig_i = 0; $sig_i < count($signatures_ot); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_ot[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_ot_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_ot_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_ot">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_ot[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_ot_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_ot_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_ot');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_ot', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Provisional Psychologist,') !== FALSE) { ?>
	                	<h3>Provisional Psychologist</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_pp)) {
		                			$signatures_pp = explode('*#*', $signatures_pp);
		                			for ($sig_i = 0; $sig_i < count($signatures_pp); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_pp[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_pp_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_pp_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_pp">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_pp[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_pp_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_pp_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_pp');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_pp', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Physiotherapist,') !== FALSE) { ?>
	                	<h3>Physiotherapist</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_physio)) {
		                			$signatures_physio = explode('*#*', $signatures_physio);
		                			for ($sig_i = 0; $sig_i < count($signatures_physio); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_physio[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_physio_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_physio_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_physio">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_physio[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_physio_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_physio_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_physio');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_physio', this);">
							</div>
		                </div>
	                <?php } ?>
	                <?php if (strpos($value_config, ',Signatures Aides,') !== FALSE) { ?>
	                	<h3>Aide(s)</h3>
	                	<div class="form-group">
							<div class="col-sm-8 col-sm-offset-4">
		                		<?php if(!empty($signatures_aide)) {
		                			$signatures_aide = explode('*#*', $signatures_aide);
		                			for ($sig_i = 0; $sig_i < count($signatures_aide); $sig_i++) { ?>
		                				<img src="../Individual Support Plan/download/<?= $signatures_aide[$sig_i] ?>"><br>
		                				Name: <?= explode('*#*', $signatures_aide_name)[$sig_i] ?><br>
		                				Date: <?= explode('*#*', $signatures_aide_date)[$sig_i] ?><br><br>
			                		<?php }
		                		} ?>
		                	</div>
	                	</div>
	                	<div class="form-group signatures_aide">
	                		<label class="col-sm-4 control-label">Signature:</label>
	                		<div class="col-sm-8">
			                	<?php $output_name = 'signatures_aide[]';
			                	include('../phpsign/sign_multiple.php'); ?>
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Name:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_aide_name[]" class="form-control" value="">
			                </div>
			                <div class="clearfix"></div>
			                <label class="col-sm-4 control-label">Date:</label>
			                <div class="col-sm-8">
			                	<input type="text" name="signatures_aide_date[]" class="form-control datepicker" value="">
			                </div>
							<div class="clearfix"></div>
							<div class="col-sm-12 pull-right">
								<img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addSignature('signatures_aide');">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature('signatures_aide', this);">
							</div>
		                </div>
	                <?php } ?>

                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

        <div class="form-group">
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

        <div class="form-group">
            <div class="col-sm-6"><a href="<?= $from_url ?>" class="btn brand-btn btn-lg">Back</a></div>
			<div class="col-sm-6"><button type="submit" name="add_medication" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button></div>
			<div class="clearfix"></div>
        </div>


		<script>
		function checkContactChange(sel) {
			if(sel.value == 'NEW_CONTACT') {
				$(sel).closest('.form-group').find('input').show().focus();
			} else {
				$(sel).closest('.form-group').find('input').hide();
			}
		}
		</script>

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>

<?php function contact_category_call($dbc, $select_id, $select_name, $contact_category_value) {
    global $contact_tabs; ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control contact_category_onchange" width="380">
              <option value=""></option>
              <?php $each_tab = explode(',', $contact_tabs);print_r($each_tab);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
			        echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
$all_contacts = [];
function contact_call($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact) { ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" data-value="<?= $contact_value ?>" data-category="<?= $from_contact ?>" class="chosen-select-deselect form-control contact_onchange" width="380">
              <option value="NEW_CONTACT">Add New Contact</option>
            </select>
			<input type="text" name="<?= str_replace('[]','',$select_name) ?>_new_contact<?= preg_replace('/[^\[\]]/','',$select_name) ?>" class="form-control" style="display:none;">
        </div>
    </div>
<?php } ?>