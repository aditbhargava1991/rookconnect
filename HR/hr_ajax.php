<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'settings_tabs') {
	foreach($_POST['types'] as $i => $type) {
		$old_type = $_POST['old_types'][$i];
		if(!empty($old_type) && $old_type != $type) {

			$before_change = capture_before_change($dbc, 'hr', 'category', 'category', $old_type);

			mysqli_query($dbc, "UPDATE `hr` SET `category` = '$type' WHERE `category` = '$old_type'");

			$history = capture_after_change('category', $type);
	    add_update_history($dbc, 'hr_history', $history, '', $before_change);

			$before_change = capture_before_change($dbc, 'manuals', 'category', 'category', $type);

			mysqli_query($dbc, "UPDATE `manuals` SET `category` = '$type' WHERE `category` = '$old_type'");

			$history = capture_after_change('category', $type);
	    add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	set_config($dbc, 'hr_tabs', filter_var(implode(',',$_POST['types']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'hr_tiles', filter_var(implode(',',$_POST['tiles']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'hr_include_profile', filter_var($_POST['hr_include_profile'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'settings_request_update') {
	set_config($dbc, 'hr_request_update_security', filter_var(implode(',',$_POST['hr_request_update_security']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'hr_request_update_staff', filter_var(implode(',',$_POST['hr_request_update_staff']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'hr_include_request_update', filter_var($_POST['hr_include_request_update'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'settings_fields') {
	set_config($dbc, 'hr_fields', filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'mark_favourite') {
	$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
	$user = filter_var($_GET['user'],FILTER_SANITIZE_STRING);
	if($_GET['item'] == 'hr') {
		$before_change = capture_before_change($dbc, 'hr', 'favourite', 'hrid', $id);

		mysqli_query($dbc, "UPDATE `hr` SET `favourite`=TRIM(BOTH ',' FROM REPLACE(IF(CONCAT(',',`favourite`,',') LIKE '%,$user,%',REPLACE(CONCAT(',',`favourite`,','),',$user,',','),CONCAT(`favourite`,',$user')),',,',',')) WHERE `hrid`='$id'");

		$history = "Favourite in HR Updated. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);

	} else if($_GET['item'] == 'manual') {
		$before_change = capture_before_change($dbc, 'manuals', 'favourite', 'manualtypeid', $id);

		mysqli_query($dbc, "UPDATE `manuals` SET `favourite`=TRIM(BOTH ',' FROM REPLACE(IF(CONCAT(',',`favourite`,',') LIKE '%,$user,%',REPLACE(CONCAT(',',`favourite`,','),',$user,',','),CONCAT(`favourite`,',$user')),',,',',')) WHERE `manualtypeid`='$id'");

		$history = "Favourite in Manual Updated. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
} else if($_GET['action'] == 'mark_pinned') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$users = filter_var(implode(',',$_POST['users']),FILTER_SANITIZE_STRING);
	if($_POST['item'] == 'hr') {
		$before_change = capture_before_change($dbc, 'hr', 'pinned', 'hrid', $id);

		mysqli_query($dbc, "UPDATE `hr` SET `pinned`=',$users,' WHERE `hrid`='$id'");
		echo "UPDATE `hr` SET `pinned`=',$users,' WHERE `hrid`='$id'";

		$history = capture_after_change('pinned', $users);
		add_update_history($dbc, 'hr_history', $history, '', $before_change);

	} else if($_POST['item'] == 'manual') {
		$before_change = capture_before_change($dbc, 'manuals', 'favourite', 'manualtypeid', $id);

		mysqli_query($dbc, "UPDATE `manuals` SET `favourite`=',$users,' WHERE `manualtypeid`='$id'");
		echo "UPDATE `manuals` SET `favourite`=',$users,' WHERE `manualtypeid`='$id'";

		$history = capture_after_change('favourite', $users);
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
} else if($_GET['action'] == 'set_form_fields') {
	$fields = '';
	switch($_POST['form']) {
		case 'Employee Information Form':
			include('employee_information_form/field_config_employee_information_form.php');
			break;
		case '2016 Alberta Personal Tax Credits Return':
			include('2016_alberta_personal/field_config_2016_alberta_personal.php');
			break;
		case '2016 Personal Tax Credits Return':
			include('tax_credit_1/field_config_2016_alberta_personal.php');
			break;
		case 'PERSONAL PROTECTIVE EQUIPMENT POLICY':
			include('personal_protective_equipment_policy/field_config_personal_protective_equipment_policy.php');
			break;
		case 'Employee Driver Information Form':
			include('employee_driver_information_form/field_config_employee_driver_information_form.php');
			break;
		case 'Time Off Request':
			include('time_off_request/field_config_time_off_request.php');
			break;
		case 'Confidential Information':
			include('confidential_information/field_config_confidential_information.php');
			break;
		case 'Work Hours Policy':
			include('work_hours_policy/field_config_work_hours_policy.php');
			break;
		case 'Direct Deposit Information':
			include('direct_deposit_information/field_config_direct_deposit_information.php');
			break;
		case 'Employee Personal and Emergency Information':
			include('employee_personal_and_emergency_information/field_config_employee_personal_and_emergency_information.php');
			break;
		case 'Employee Substance Abuse Policy':
			include('employee_substance_abuse_policy/field_config_employee_substance_abuse_policy.php');
			break;
		case 'Employee Right to Refuse Unsafe Work':
			include('employee_right_to_refuse_unsafe_work/field_config_employee_right_to_refuse_unsafe_work.php');
			break;
		case 'Policy and Procedure Notice of Understanding and Intent':
			include('policy_and_procedure_notice_of_understanding_and_intent/field_config_policy_and_procedure_notice_of_understanding_and_intent.php');
			break;
		case 'Shop Yard and Office Orientation':
			include('employee_shop_yard_office_orientation/field_config_employee_shop_yard_office_orientation.php');
			break;
		case 'Copy of Drivers Licence and Safety Tickets':
			include('copy_of_drivers_licence_safety_tickets/field_config_copy_of_drivers_licence_safety_tickets.php');
			break;
		case 'PPE Requirements':
			include('ppe_requirements/field_config_ppe_requirements.php');
			break;
		case 'Verbal Training in Emergency Response Plan':
			include('verbal_training_in_emergency_response_plan/field_config_verbal_training_in_emergency_response_plan.php');
			break;
		case 'Eligibility for General Holidays and General Holiday Pay':
			include('eligibility_for_general_holidays_general_holiday_pay/field_config_eligibility_for_general_holidays_general_holiday_pay.php');
			break;
		case 'Maternity Leave and Parental Leave':
			include('maternity_leave_parental_leave/field_config_maternity_leave_parental_leave.php');
			break;
		case 'Employment Verification Letter':
			include('employment_verification_letter/field_config_employment_verification_letter.php');
			break;
		case 'Background Check Authorization':
			include('background_check_authorization/field_config_background_check_authorization.php');
			break;
		case 'Disclosure of Outside Clients':
			include('disclosure_of_outside_clients/field_config_disclosure_of_outside_clients.php');
			break;
		case 'Employment Agreement':
			include('employment_agreement/field_config_employment_agreement.php');
			break;
		case 'Independent Contractor Agreement':
			include('independent_contractor_agreement/field_config_independent_contractor_agreement.php');
			break;
		case 'Letter of Offer':
			include('letter_of_offer/field_config_letter_of_offer.php');
			break;
		case 'Employee Non-Disclosure Agreement':
			include('employee_nondisclosure_agreement/field_config_employee_nondisclosure_agreement.php');
			break;
		case 'Employee Self Evaluation':
			include('employee_self_evaluation/field_config_employee_self_evaluation.php');
			break;
		case 'HR Complaint':
			include('hr_complaint/field_config_hr_complaint.php');
			break;
		case 'Exit Interview':
			include('exit_interview/field_config_exit_interview.php');
			break;
		case 'Employee Expense Reimbursement':
			include('employee_expense_reimbursement/field_config_employee_expense_reimbursement.php');
			break;
		case 'Absence Report':
			include('absence_report/field_config_absence_report.php');
			break;
		case 'Employee Accident Report Form':
			include('employee_accident_report_form/field_config_employee_accident_report_form.php');
			break;
		case 'Trucking Information':
			include('trucking_information/field_config_trucking_information.php');
			break;
		case 'Contractor Orientation':
			include('contractor_orientation/field_config_contractor_orientation.php');
			break;
		case 'Contract Welder Inspection Checklist':
			include('contract_welder_inspection_checklist/field_config_contract_welder_inspection_checklist.php');
			break;
		case 'Contractor Pay Agreement':
			include('contractor_pay_agreement/field_config_contractor_pay_agreement.php');
			break;
		case 'Employee Holiday Request Form':
			include('employee_holiday_request_form/field_config_employee_holiday_request_form.php');
			break;
		case 'Employee Coaching Form':
			include('employee_coaching_form/field_config_employee_coaching_form.php');
			break;
		case 'Police Information Check':
			include('police_information_check/field_config_police_information_check.php');
			break;
	}
} else if($_GET['action'] == 'set_form_section') {
	$section = filter_var($_POST['section'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `hr` WHERE `heading_number`='$section' AND `category`='$category' AND `deleted`=0"))['heading'];
	echo $heading.'#*#<option></option>';
	$heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number`, `sub_heading_number` FROM (SELECT `heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`sub_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`sub_heading_number`, 100, 0)) FROM (SELECT `sub_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$category."' AND `heading_number`='$section' UNION SELECT `sub_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$category."' AND `heading_number`='$section') `numbers`) GROUP BY `sub_heading_number`"));
	$heading_count = substr($heading_count['sub_heading_number'],strlen($heading_count['heading_number']) + 1) + 5;
	for($i = 1; $i <= $heading_count; $i++) {
		$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `hr` WHERE `sub_heading_number`='".$section.'.'.$i."' AND `category`='".$category."' AND `deleted`=0 UNION SELECT `sub_heading` FROM `manuals` WHERE `sub_heading_number`='".$section.'.'.$i."' AND `category`='".$category."' AND `deleted`=0"))['sub_heading']; ?>
		<option value="<?= $section.'.'.$i ?>"><?= $section.'.'.$i.' '.$heading ?></option>
	<?php }
} else if($_GET['action'] == 'set_form_subsection') {
	$subsection = filter_var($_POST['subsection'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	if($subsection == '') {
		echo "#*#<option></option>";
	} else {
		$sub_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `hr` WHERE `sub_heading_number`='$subsection' AND `category`='$category' AND `deleted`=0"))['sub_heading'];
		echo $sub_heading.'#*#<option></option>';
		$heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading_number`, `sub_heading_number` FROM (SELECT `third_heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `third_heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`third_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`third_heading_number`, 100, 0)) FROM (SELECT `third_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$category."' AND `heading_number`='$subsection' UNION SELECT `third_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$category."' AND `sub_heading_number`='$subsection') `numbers`) GROUP BY `third_heading_number`"));
		$heading_count = substr($heading_count['third_heading_number'],strlen($heading_count['sub_heading_number']) + 1) + 5;
		for($i = 1; $i <= $heading_count; $i++) {
			$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading` FROM `hr` WHERE `third_heading_number`='".$subsection.'.'.$i."' AND `category`='".$category."' AND `deleted`=0 UNION SELECT `third_heading` FROM `manuals` WHERE `third_heading_number`='".$subsection.'.'.$i."' AND `category`='".$category."' AND `deleted`=0"))['sub_heading']; ?>
			<option value="<?= $subsection.'.'.$i ?>"><?= $subsection.'.'.$i.' '.$heading ?></option>
		<?php }
	}
} else if($_GET['action'] == 'set_category') {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	echo '<option></option>';
	$heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number` FROM (SELECT `heading_number` FROM `hr` UNION SELECT `heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`heading_number`, 100, 0) IN (SELECT MAX(LPAD(`heading_number`, 100, 0)) FROM `manuals` WHERE `deleted`=0) GROUP BY `heading_number`"))['heading_number'] + 5;
	for($i = 1; $i <= $heading_count; $i++) {
		$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM (SELECT `heading`, `heading_number`, `category` FROM `hr` WHERE `deleted`=0 UNION SELECT `heading`, `heading_number`, `category` FROM `manuals` WHERE `deleted`=0) `hr_manuals` WHERE `heading_number`='".$i."' AND `category`='".$category."'"))['heading']; ?>
		<option value="<?= $i ?>"><?= $i.' '.$heading ?></option>
	<?php }
} else if($_GET['action'] == 'set_manual_section') {
	$section = filter_var($_POST['section'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `manuals` WHERE `heading_number`='$section' AND `category`='$category' AND `deleted`=0"))['heading'];
	echo $heading.'#*#<option></option>';
	$heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number`, `sub_heading_number` FROM (SELECT `heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`sub_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`sub_heading_number`, 100, 0)) FROM `manuals` WHERE `heading_number`='$section') GROUP BY `sub_heading_number`"));
	$heading_count = substr($heading_count['sub_heading_number'],strlen($heading_count['heading_number']) + 1) + 5;
	for($i = 1; $i <= $heading_count; $i++) {
		$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `manuals` WHERE `sub_heading_number`='".$section.'.'.$i."' AND `category`='".$category."' AND `deleted`=0"))['sub_heading']; ?>
		<option value="<?= $section.'.'.$i ?>"><?= $section.'.'.$i.' '.$heading ?></option>
	<?php }
} else if($_GET['action'] == 'set_manual_subsection') {
	$subsection = filter_var($_POST['subsection'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	if($subsection == '') {
		echo "#*#<option></option>";
	} else {
		$sub_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `manuals` WHERE `sub_heading_number`='$subsection' AND `category`='$category' AND `deleted`=0"))['sub_heading'];
		echo $sub_heading.'#*#<option></option>';
		$heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading_number`, `sub_heading_number` FROM (SELECT `third_heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `third_heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`third_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`third_heading_number`, 100, 0)) FROM (SELECT `third_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$category."' AND `heading_number`='$heading_number' UNION SELECT `third_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$category."' AND `sub_heading_number`='$subsection') `numbers`) GROUP BY `third_heading_number`"));
		$heading_count = substr($heading_count['third_heading_number'],strlen($heading_count['sub_heading_number']) + 1) + 5;
		for($i = 1; $i <= $heading_count; $i++) {
			$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading` FROM `manuals` WHERE `third_heading_number`='".$subsection.'.'.$i."' AND `category`='".$category."' AND `deleted`=0"))['sub_heading']; ?>
			<option value="<?= $subsection.'.'.$i ?>"><?= $subsection.'.'.$i.' '.$heading ?></option>
		<?php }
	}
} else if($_GET['action'] == 'settings_config') {
	set_config($dbc, filter_var($_POST['name'],FILTER_SANITIZE_STRING), htmlentities(filter_var($_POST['value'],FILTER_SANITIZE_STRING)));
} else if($_GET['action'] == 'form_layout') {
	$hrid = filter_var($_POST['hrid'],FILTER_SANITIZE_STRING);
	$form = filter_var($_POST['form'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	if(!($hrid > 0)) {
		mysqli_query($dbc, "INSERT INTO `hr` (`hrid`, `form`) SELECT '$hrid', '$form' FROM (SELECT COUNT(*) `rows` FROM `hr` WHERE `hrid`='$hrid') `num` WHERE `num`.`rows`=0");
		$before_change = '';
		$history = "HR entry added. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
		if(mysqli_insert_id($dbc) > 0) {
			echo mysqli_insert_id($dbc);
			$hrid = mysqli_insert_id($dbc);
		}
	}
	if(!empty($_POST['user_form_id'])) {

		$before_change = capture_before_change($dbc, 'hr', 'user_form_id', 'hrid', $hrid);

		mysqli_query($dbc, "UPDATE `hr` SET `user_form_id` = '".$_POST['user_form_id']."' WHERE `hrid` = '$hrid'");

		$history = capture_after_change('user_form_id', $_POST['user_form_id']);
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
	if($field != '') {
		$before_change = capture_before_change($dbc, 'hr', $field, 'hrid', $hrid);

		mysqli_query($dbc, "UPDATE `hr` SET `$field`='$value' WHERE `hrid`='$hrid'");

		$history = capture_after_change($field, $value);
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
} else if($_GET['action'] == 'hr_upload') {
	$hrid = filter_var($_POST['hrid'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$basename = $_FILES['file']['name'];
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
	$i = 0;
	while(file_exists('download/'.$filename)) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);

	$before_change = capture_before_change($dbc, 'hr', $name, 'hrid', $hrid);

	mysqli_query($dbc, "UPDATE `hr` SET `$name`='$filename' WHERE `hrid`='$hrid'");

	$history = capture_after_change($name, $filename);
	add_update_history($dbc, 'hr_history', $history, '', $before_change);

	echo $filename;
} else if($_GET['action'] == 'pr_settings') {
	set_config($dbc, 'performance_review_fields', filter_var(implode(',', $_POST['pr_fields'])),FILTER_SANITIZE_STRING);
	set_config($dbc, 'performance_review_positions', filter_var(implode(',', $_POST['pr_positions'])),FILTER_SANITIZE_STRING);
	$pr_forms = json_decode($_POST['pr_forms']);
	foreach($pr_forms as $pr_form) {
		$pr_form = json_decode(json_encode($pr_form),true);
		$user_form_id = filter_var($pr_form['user_form_id'],FILTER_SANITIZE_STRING);
		$enabled = filter_var($pr_form['enabled'],FILTER_SANITIZE_STRING);
		$limit_staff = filter_var($pr_form['limit_staff'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `field_config_performance_reviews` (`user_form_id`) SELECT '$user_form_id' FROM (SELECT COUNT(*) rows FROM `field_config_performance_reviews` WHERE `user_form_id` = '$user_form_id') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `field_config_performance_reviews` SET `enabled` = '$enabled', `limit_staff` = '$limit_staff' WHERE `user_form_id` = '$user_form_id'");
	}
} else if($_GET['action'] == 'archive') {
	$id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $date_of_archival = date('Y-m-d');
	switch($_POST['type']) {
		case 'hr':
			$before_change = capture_before_change($dbc, 'hr', 'deleted', 'hrid', $id);
			$before_change .= capture_before_change($dbc, 'hr', 'date_of_archival', 'hrid', $id);
			mysqli_query($dbc, "UPDATE `hr` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `hrid`='$id'");
			echo "UPDATE `hr` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `hrid`='$id'";
			$history = capture_after_change('deleted', 1);
			$history .= capture_after_change('date_of_archival', $date_of_archival);
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
			break;
		case 'manual':
			$before_change = capture_before_change($dbc, 'manuals', 'deleted', 'manualtypeid', $id);
			$before_change .= capture_before_change($dbc, 'manuals', 'date_of_archival', 'manualtypeid', $id);
			mysqli_query($dbc, "UPDATE `manuals` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `manualtypeid`='$id'");
			echo "UPDATE `manuals` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `manualtypeid`='$id'";
			$history = capture_after_change('deleted', 1);
			$history .= capture_after_change('date_of_archival', $date_of_archival);
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
			break;
	}
}
