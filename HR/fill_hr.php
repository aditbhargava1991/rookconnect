<?php $hr = $hrid = filter_var($_GET['hr'],FILTER_SANITIZE_STRING);
$get_hr = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `hr`.*, IFNULL(NULLIF(`hr`.`pdf_logo`,''),`defaults`.`pdf_logo`) `pdf_logo`, IFNULL(NULLIF(`hr`.`pdf_header`,''),`defaults`.`pdf_header`) `pdf_header`, IFNULL(NULLIF(`hr`.`pdf_footer`,''),`defaults`.`pdf_footer`) `pdf_footer`, IFNULL(NULLIF(`hr`.`email_subject`,''),`defaults`.`email_subject`) `email_subject`, IFNULL(NULLIF(`hr`.`email_message`,''),`defaults`.`email_message`) `email_message`, IFNULL(NULLIF(`hr`.`completed_recipient`,''),`defaults`.`completed_recipient`) `completed_recipient`, IFNULL(NULLIF(`hr`.`completed_subject`,''),`defaults`.`completed_subject`) `completed_subject`, IFNULL(NULLIF(`hr`.`completed_message`,''),`defaults`.`completed_message`) `completed_message`, IFNULL(NULLIF(`hr`.`approval_subject`,''),`defaults`.`approval_subject`) `approval_subject`, IFNULL(NULLIF(`hr`.`approval_message`,''),`defaults`.`approval_message`) `approval_message`, IFNULL(NULLIF(`hr`.`rejected_subject`,''),`defaults`.`rejected_subject`) `rejected_subject`, IFNULL(NULLIF(`hr`.`rejected_message`,''),`defaults`.`rejected_message`) `rejected_message`
	FROM `hr` LEFT JOIN `hr` `defaults` ON `defaults`.`form`='default_hr_form_settings' WHERE `hr`.`hrid`='$hr'"));
$get_hr['completed_recipient'] = $get_hr['completed_recipient'] > 0 ? get_email($dbc, $get_hr['completed_recipient']) : '';
if(isset($_POST['submit'])) {
	if($hr > 0) {
		$staffid = $_SESSION['contactid'];
		// Insert a row if it isn't already there
		$query_insert_row = "INSERT INTO `hr_staff` (`hrid`, `staffid`) SELECT '$hr', '$staffid' FROM (SELECT COUNT(*) rows FROM `hr_staff` WHERE `hrid`='$hr' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
		$before_change = '';
		$history = "Manuals upload entry added. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
		mysqli_query($dbc, $query_insert_row);
		$done = 1; // $_POST[''] != '' ? 1 : 0;
			$before_change = capture_before_change($dbc, 'hr_staff', 'done', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);
			$before_change .= capture_before_change($dbc, 'hr_staff', 'today_date', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);

	    $query_update_ticket = "UPDATE `hr_staff` SET `done` = '$done', `today_date` = '$today_date' WHERE `hrid` = '$hr' AND staffid='$staffid' AND done=0";
	    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

			$history = capture_after_change('done', $done);
			$history .= capture_after_change('today_date', $today_date);
			add_update_history($dbc, 'hr_history', $history, '', $before_change);

	    //Update reminders to done
	    mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$staffid' AND `src_table` = 'hr' AND `src_tableid` = '$hr'");
	}

	include_once('../phpsign/signature-to-image.php');
	include_once('../tcpdf/tcpdf.php');
	$form_name_save = $_POST['form_name'];
	if($_POST['form_id'] > 0) {
        include ('user_forms.php');
    } else if($form_name_save == 'Employee Information Form') {
		include ('employee_information_form/save_employee_information_form.php');
	} else if($form_name_save == 'Employee Driver Information Form') {
		include ('employee_driver_information_form/save_employee_driver_information_form.php');
	} else if($form_name_save == 'Time Off Request') {
		include ('time_off_request/save_time_off_request.php');
	} else if($form_name_save == 'Confidential Information') {
		include ('confidential_information/save_confidential_information.php');
	} else if($form_name_save == 'Work Hours Policy') {
		include ('work_hours_policy/save_work_hours_policy.php');
	} else if($form_name_save == 'Direct Deposit Information') {
		include ('direct_deposit_information/save_direct_deposit_information.php');
	} else if($form_name_save == 'Employee Substance Abuse Policy') {
		include ('employee_substance_abuse_policy/save_employee_substance_abuse_policy.php');
	} else if($form_name_save == 'Employee Right to Refuse Unsafe Work') {
		include ('employee_right_to_refuse_unsafe_work/save_employee_right_to_refuse_unsafe_work.php');
	} else if($form_name_save == 'Shop Yard and Office Orientation') {
		include ('employee_shop_yard_office_orientation/save_employee_shop_yard_office_orientation.php');
	} else if($form_name_save == "Copy of Drivers Licence and Safety Tickets") {
		include ('copy_of_drivers_licence_safety_tickets/save_copy_of_drivers_licence_safety_tickets.php');
	} else if($form_name_save == 'PPE Requirements') {
		include ('ppe_requirements/save_ppe_requirements.php');
	} else if($form_name_save == 'Verbal Training in Emergency Response Plan') {
		include ('verbal_training_in_emergency_response_plan/save_verbal_training_in_emergency_response_plan.php');
	} else if($form_name_save == 'Eligibility for General Holidays and General Holiday Pay') {
		include ('eligibility_for_general_holidays_general_holiday_pay/save_eligibility_for_general_holidays_general_holiday_pay.php');
	} else if($form_name_save == 'Maternity Leave and Parental Leave') {
		include ('maternity_leave_parental_leave/save_maternity_leave_parental_leave.php');
	} else if($form_name_save == 'Employment Verification Letter') {
		include ('employment_verification_letter/save_employment_verification_letter.php');
	} else if($form_name_save == 'Background Check Authorization') {
		include ('background_check_authorization/save_background_check_authorization.php');
	} else if($form_name_save == 'Disclosure of Outside Clients') {
		include ('disclosure_of_outside_clients/save_disclosure_of_outside_clients.php');
	} else if($form_name_save == 'Employment Agreement') {
		include ('employment_agreement/save_employment_agreement.php');
	} else if($form_name_save == 'Independent Contractor Agreement') {
		include ('independent_contractor_agreement/save_independent_contractor_agreement.php');
	} else if($form_name_save == 'Letter of Offer') {
		include ('letter_of_offer/save_letter_of_offer.php');
	} else if($form_name_save == 'Employee Non-Disclosure Agreement') {
		include ('employee_nondisclosure_agreement/save_employee_nondisclosure_agreement.php');
	} else if($form_name_save == 'Employee Self Evaluation') {
		include ('employee_self_evaluation/save_employee_self_evaluation.php');
	} else if($form_name_save == 'HR Complaint') {
		include ('hr_complaint/save_hr_complaint.php');
	} else if($form_name_save == 'Exit Interview') {
		include ('exit_interview/save_exit_interview.php');
	} else if($form_name_save == 'Employee Expense Reimbursement') {
		include ('employee_expense_reimbursement/save_employee_expense_reimbursement.php');
	} else if($form_name_save == 'Absence Report') {
		include ('absence_report/save_absence_report.php');
	} else if($form_name_save == 'Employee Accident Report Form') {
		include ('employee_accident_report_form/save_employee_accident_report_form.php');
	} else if($form_name_save == 'Trucking Information') {
		include ('trucking_information/save_trucking_information.php');
	} else if($form_name_save == 'Contractor Orientation') {
		include ('contractor_orientation/save_contractor_orientation.php');
	} else if($form_name_save == 'Contract Welder Inspection Checklist') {
		include ('contract_welder_inspection_checklist/save_contract_welder_inspection_checklist.php');
	} else if($form_name_save == 'Contractor Pay Agreement') {
		include ('contractor_pay_agreement/save_contractor_pay_agreement.php');
	} else if($form_name_save == 'Employee Holiday Request Form') {
		include ('employee_holiday_request_form/save_employee_holiday_request_form.php');
	} else if($form_name_save == 'Employee Coaching Form') {
		include ('employee_coaching_form/save_employee_coaching_form.php');
	} else if($form_name_save == '2016 Alberta Personal Tax Credits Return') {
		include ('2016_alberta_personal/save_2016_alberta_personal.php');
	} else if($form_name_save == '2016 Personal Tax Credits Return') {
		include ('tax_credit_1/save_2016_alberta_personal.php');
	} else if($form_name_save == 'Driver Abstract Statement of Intent') {
		include ('driver_abstract_statement_of_intent/save_driver_abstract_statement_of_intent.php');
	} else if($form_name_save == 'PERSONAL PROTECTIVE EQUIPMENT POLICY') {
		include ('personal_protective_equipment_policy/save_personal_protective_equipment_policy.php');
	} else if($form_name_save == 'DRIVER CONSENT FORM') {
		include ('driver_consent_form/save_driver_consent_form.php');
	} else if($form_name_save == 'Policy and Procedure Notice of Understanding and Intent') {
		include ('policy_and_procedure_notice_of_understanding_and_intent/save_policy_and_procedure_notice_of_understanding_and_intent.php');
	} else if($form_name_save == 'Employee Personal and Emergency Information') {
		include ('employee_personal_and_emergency_information/save_employee_personal_and_emergency_information.php');
	} else if($form_name_save == 'Employment Agreement Evergreen') {
		include ('employment_agreement_evergreen/save_employment_agreement.php');
	} else if($form_name_save == 'Police Information Check') {
		include ('police_information_check/save_police_information_check.php');
	}
	if($_SESSION['status'] == 2) {
		$contactid = $_SESSION['contactid'];
		$cert_types = explode('#*#',get_config($dbc,'probation_certificates'));
		$incomplete = false;
		foreach($cert_types as $cert_type) {
			if($incomplete && mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `contactid`='$contactid' AND `certificate_type`='$cert_type")->num_rows() == 0) {
				$incomplete = true;
			}
		}
		$hr_forms = explode('#*#',get_config($dbc,'probation_forms'));
		foreach($hr_forms as $id) {
			if($incomplete && mysqli_query($dbc, "SELECT * FROM `hr_attendance` WHERE `done`=1 AND `assign_staffid`='$contactid' AND `hrid`='$id")->num_rows() == 0) {
				$incomplete = true;
			}
		}
		if(!$incomplete && in_array($hrid,$hr_forms)) {
			$recipient = get_config($dbc, 'probation_email');
			if($recipient != '') {
				$staff = get_contact($dbc, $contactid);
				try {
					send_email('', $recipient, '', '', "$staff has completed all necessary forms and certificates to complete probation", "This is to notify you that $staff has completed the necessary certificates and forms to not be on probation.<br />
					<br />
					Please <a href='".WEBSITE_URL."/Staff/staff.php'>log in</a> to the software and review the forms and certificate for $staff.");
				} catch (Exception $e) { }
			}
		}
	}
}
$form_config = ','.$get_hr['fields'].','; ?>
<?php if($user_form_layout != 'Sidebar') { ?>
<div class='scale-to-fill has-main-screen no-pad'>
	<div class='main-screen form-horizontal'>
<?php } ?>
		<form class="form-horizontal" action="" method="POST">
			<input type="hidden" name="form_name" value="<?= $get_hr['form'] ?>">
			<input type="hidden" name="form_id" value="<?= $get_hr['user_form_id'] ?>">
            <div class="standard-body-title">
                <h3>
                    <?= $get_hr['category'] != '' ? '<b>'.$get_hr['category'].'</b><br />' : '' ?>
                    <?= $get_hr['third_heading'] != '' ? $get_hr['third_heading_number'].' '.$get_hr['third_heading'] : ($get_hr['sub_heading'] != '' ? $get_hr['sub_heading_number'].' '.$get_hr['sub_heading'] : $get_hr['heading_number'].' '.$get_hr['heading']) ?>
                    <a href="../blank_loading_page.php" class="pull-right"><img src="../img/icons/cancel.png" class="inline-img no-toggle small" title="Cancel"></a>
                    <a href="?hrid_pdf=<?= $get_hr['hrid'] ?>" class="pull-right" target="_blank"><img class="inline-img no-toggle small" title="Download PDF" src="../img/pdf.png"></a><div class="clearfix"></div>
                </h3>
                <div class="clearfix"></div>
            </div>
            <div class="standard-body-content pad-horiz-2 pad-vertical">
                <div class="clearfix"></div>
                <div class="col-sm-12"><?= !empty($get_hr['pdf_logo']) && file_exists('download/'.$get_hr['pdf_logo']) ? '<img src="download/'.$get_hr['pdf_logo'].'" style="height: auto; max-height: 10em; max-width: 10em; width: auto;" class="pad-vertical">' : '' ?><div class="pull-right"><?= html_entity_decode($get_hr['pdf_header']) ?></div></div>
                <?php if($get_hr['hr_description'] != '') { ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Details:</label>
                        <div class="col-sm-8">
                            <?= html_entity_decode($get_hr['hr_description']) ?>
                        </div>
                    </div>
                <?php } ?>

                <?php $uploads = mysqli_query($dbc, "SELECT `uploadid`, `upload`,`type` FROM `hr_upload` WHERE `hrid`='$hr'");
                if(mysqli_num_rows($uploads) > 0) {
                    echo '<div class="form-group">
                        <label class="col-sm-4 control-label">Attachments:</label>
                        <div class="col-sm-8">
                            <ul>';
                                while($upload = mysqli_fetch_assoc($uploads)) { ?>
                                    <li><?php switch($upload['type']) {
                                        case 'document':
                                            echo 'Document: ';
                                            echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$hr.'&type=document" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                            break;
                                        case 'link':
                                            echo 'Link: ';
                                            echo '<a href="'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$hr.'&type=link" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                            break;
                                        case 'video':
                                            echo 'Video: ';
                                            echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$hr.'&type=video" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                            break;
                                    } ?></li>
                                <?php }
                            echo "</div>
                        </div>
                    </ul>";
                } ?>

                <?php $tab = '%';
                $form = $get_hr['form'];
                if ($get_hr['user_form_id'] > 0) {
                    $user_form_id = $get_hr['user_form_id'];
                    include ('user_forms.php');
                } else if ($get_hr['form'] == 'Employee Information Form') {
                    include ('employee_information_form/employee_information_form.php');
                } else if ($get_hr['form'] == 'Employee Driver Information Form') {
                    include ('employee_driver_information_form/employee_driver_information_form.php');
                } else if ($get_hr['form'] == 'Time Off Request') {
                    include ('time_off_request/time_off_request.php');
                } else if ($get_hr['form'] == 'Confidential Information') {
                    include ('confidential_information/confidential_information.php');
                } else if ($get_hr['form'] == 'Work Hours Policy') {
                    include ('work_hours_policy/work_hours_policy.php');
                } else if ($get_hr['form'] == 'Direct Deposit Information') {
                    include ('direct_deposit_information/direct_deposit_information.php');
                } else if ($get_hr['form'] == 'Employee Substance Abuse Policy') {
                    include ('employee_substance_abuse_policy/employee_substance_abuse_policy.php');
                } else if ($get_hr['form'] == 'Employee Right to Refuse Unsafe Work') {
                    include ('employee_right_to_refuse_unsafe_work/employee_right_to_refuse_unsafe_work.php');
                } else if ($get_hr['form'] == 'Shop Yard and Office Orientation') {
                    include ('employee_shop_yard_office_orientation/employee_shop_yard_office_orientation.php');
                } else if ($get_hr['form'] == "Copy of Drivers Licence and Safety Tickets") {
                    include ('copy_of_drivers_licence_safety_tickets/copy_of_drivers_licence_safety_tickets.php');
                } else if ($get_hr['form'] == 'PPE Requirements') {
                    include ('ppe_requirements/ppe_requirements.php');
                } else if ($get_hr['form'] == 'Verbal Training in Emergency Response Plan') {
                    include ('verbal_training_in_emergency_response_plan/verbal_training_in_emergency_response_plan.php');
                } else if ($get_hr['form'] == 'Eligibility for General Holidays and General Holiday Pay') {
                    include ('eligibility_for_general_holidays_general_holiday_pay/eligibility_for_general_holidays_general_holiday_pay.php');
                } else if ($get_hr['form'] == 'Maternity Leave and Parental Leave') {
                    include ('maternity_leave_parental_leave/maternity_leave_parental_leave.php');
                } else if ($get_hr['form'] == 'Employment Verification Letter') {
                    include ('employment_verification_letter/employment_verification_letter.php');
                } else if ($get_hr['form'] == 'Background Check Authorization') {
                    include ('background_check_authorization/background_check_authorization.php');
                } else if ($get_hr['form'] == 'Disclosure of Outside Clients') {
                    include ('disclosure_of_outside_clients/disclosure_of_outside_clients.php');
                } else if ($get_hr['form'] == 'Employment Agreement') {
                    include ('employment_agreement/employment_agreement.php');
                } else if ($get_hr['form'] == 'Independent Contractor Agreement') {
                    include ('independent_contractor_agreement/independent_contractor_agreement.php');
                } else if ($get_hr['form'] == 'Letter of Offer') {
                    include ('letter_of_offer/letter_of_offer.php');
                } else if ($get_hr['form'] == 'Employee Non-Disclosure Agreement') {
                    include ('employee_nondisclosure_agreement/employee_nondisclosure_agreement.php');
                } else if ($get_hr['form'] == 'Employee Self Evaluation') {
                    include ('employee_self_evaluation/employee_self_evaluation.php');
                } else if ($get_hr['form'] == 'HR Complaint') {
                    include ('hr_complaint/hr_complaint.php');
                } else if ($get_hr['form'] == 'Exit Interview') {
                    include ('exit_interview/exit_interview.php');
                } else if ($get_hr['form'] == 'Employee Expense Reimbursement') {
                    include ('employee_expense_reimbursement/employee_expense_reimbursement.php');
                } else if ($get_hr['form'] == 'Absence Report') {
                    include ('absence_report/absence_report.php');
                } else if ($get_hr['form'] == 'Employee Accident Report Form') {
                    include ('employee_accident_report_form/employee_accident_report_form.php');
                } else if ($get_hr['form'] == 'Trucking Information') {
                    include ('trucking_information/trucking_information.php');
                } else if ($get_hr['form'] == 'Contractor Orientation') {
                    include ('contractor_orientation/contractor_orientation.php');
                } else if ($get_hr['form'] == 'Contract Welder Inspection Checklist') {
                    include ('contract_welder_inspection_checklist/contract_welder_inspection_checklist.php');
                } else if ($get_hr['form'] == 'Contractor Pay Agreement') {
                    include ('contractor_pay_agreement/contractor_pay_agreement.php');
                } else if ($get_hr['form'] == 'Employee Holiday Request Form') {
                    include ('employee_holiday_request_form/employee_holiday_request_form.php');
                } else if ($get_hr['form'] == 'Employee Coaching Form') {
                    include ('employee_coaching_form/employee_coaching_form.php');
                } else if ($get_hr['form'] == '2016 Alberta Personal Tax Credits Return') {
                    include ('2016_alberta_personal/2016_alberta_personal.php');
                } else if ($get_hr['form'] == '2016 Personal Tax Credits Return') {
                    include ('tax_credit_1/2016_alberta_personal.php');
                } else if ($get_hr['form'] == 'Driver Abstract Statement of Intent') {
                    include ('driver_abstract_statement_of_intent/driver_abstract_statement_of_intent.php');
                } else if ($get_hr['form'] == 'PERSONAL PROTECTIVE EQUIPMENT POLICY') {
                    include ('personal_protective_equipment_policy/personal_protective_equipment_policy.php');
                } else if ($get_hr['form'] == 'DRIVER CONSENT FORM') {
                    include ('driver_consent_form/driver_consent_form.php');
                } else if ($get_hr['form'] == 'Policy and Procedure Notice of Understanding and Intent') {
                    include ('policy_and_procedure_notice_of_understanding_and_intent/policy_and_procedure_notice_of_understanding_and_intent.php');
                } else if ($get_hr['form'] == 'Employee Personal and Emergency Information') {
                    include ('employee_personal_and_emergency_information/employee_personal_and_emergency_information.php');
                } else if ($get_hr['form'] == 'Employment Agreement Evergreen') {
                    include ('employment_agreement_evergreen/employment_agreement.php');
                } else if ($get_hr['form'] == 'Police Information Check') {
                    include ('police_information_check/police_information_check.php');
                } ?>

                <?php if (strpos($form_config, ','."Comments".',') !== FALSE) { ?>
                    <div class="form-group">
                        <label for="first_name[]" class="col-sm-4 control-label">Comments:</label>
                        <div class="col-sm-8">
                            <textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
                        </div>
                    </div>
                <?php } ?>

                <?php if (strpos($form_config, ','."Signature box".',') !== FALSE) { ?>
                    <div class="form-group">
                        <label for="first_name[]" class="col-sm-4 control-label">Signature:</label>
                        <div class="col-sm-8">
                            <?php include ('../phpsign/sign.php'); ?>
                        </div>
                    </div>
                <?php } ?>
                <button name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
                <div class="clearfix"></div>
                <?= html_entity_decode($get_hr['pdf_footer']) ?>
                <div class="clearfix"></div>
            </div>
		</form>
	</div>
</div>
