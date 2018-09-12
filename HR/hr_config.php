<?php if(!isset($get_hr)) {
	include_once('../include.php');
	checkAuthorised('hr');
	ob_clean();
	$hrid = filter_var($_GET['hrid'],FILTER_SANITIZE_STRING);
	$field_config = explode(',',get_config($dbc, 'hr_fields'));
	if(!($hrid > 0)) {
		$form = filter_var($_POST['form_name'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `hr` (`form`) VALUES ('$form')");
		$hrid = mysqli_insert_id($dbc);
	}
	$get_hr = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid`='$hrid'"));
	$fields = explode(',',$get_hr['fields']);
	$categories = [];
	foreach(explode(',',get_config($dbc, 'hr_tabs')) as $cat) {
		$categories[config_safe_str($cat)] = $cat;
	}
} ?>
<script type="text/javascript">
function displayRecurringDueDate(chk) {
	if($(chk).is(':checked')) {
		$(chk).closest('.recurring_block').find('.recurring_due_date').show();
	} else {
		$(chk).closest('.recurring_block').find('.recurring_due_date').hide();
	}
}
</script>
<input type="hidden" name="deleted" value="0">
<div class="form-group">
	<label class="col-sm-4 control-label">Category:</label>
	<div class="col-sm-8">
		<select name="category" data-placeholder="Select a Category" data-table="hr" data-id="<?= $hrid ?>" data-id-field="hrid" class="chosen-select-deselect" <?= isset($get_hr) ? 'onchange="changeCategory(this.value);"' : '' ?>><option></option>
			<?php foreach($categories as $cat_id => $category) {
				if($cat_id != 'favourites') { ?>
					<option <?= $category == $get_hr['category'] ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
				<?php }
			} ?>
		</select>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Pinned:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="favourite" value="ALL" <?= $get_hr['favourite'] == 'ALL' ? 'checked' : '' ?>> Yes</label>
		<label class="form-checkbox"><input type="radio" name="favourite" value="<?= $get_hr['favourite'] == 'ALL' ? '' : $get_hr['favourite'] ?>" <?= $get_hr['favourite'] == 'ALL' ? '' : 'checked' ?>> No</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Section:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Section" name="heading_number" class="chosen-select-deselect" <?= in_array('Sub Section Heading',$field_config) ? 'onchange="changeSection(this.value);"' : '' ?>><option></option>
			<?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number` FROM (SELECT `heading_number` FROM `hr` UNION SELECT `heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`heading_number`, 100, 0) IN (SELECT MAX(LPAD(`heading_number`, 100, 0)) FROM (SELECT `heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_hr['category']."' UNION SELECT `heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_hr['category']."') `numbers`) GROUP BY `heading_number`"))['heading_number'] + 5;
			for($i = 1; $i <= $heading_count; $i++) {
				$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `hr` WHERE `heading_number`='$i' AND `category`='".$get_hr['category']."' AND `deleted`=0 UNION SELECT `heading` FROM `manuals` WHERE `heading_number`='$i' AND `category`='".$get_hr['category']."' AND `deleted`=0"))['heading']; ?>
				<option <?= $get_hr['heading_number'] == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i.' '.$heading ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Heading:</label>
	<div class="col-sm-8">
		<input class="form-control" name="heading" value="<?= $get_hr['heading'] ?>">
	</div>
</div>
<?php if(in_array('Sub Section Heading',$field_config)) {
	$heading_number = $get_hr['heading_number'] == '' ? '1' : $get_hr['heading_number']; ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Sub Section:</label>
		<div class="col-sm-8">
			<select data-placeholder="Select Section" name="sub_heading_number" class="chosen-select-deselect" <?= in_array('Third Tier Heading',$field_config) ? 'onchange="changeSubSection(this.value);"' : '' ?>><option></option>
				<?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number`, `sub_heading_number` FROM (SELECT `heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`sub_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`sub_heading_number`, 100, 0)) FROM (SELECT `sub_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_hr['category']."' AND `heading_number`='$heading_number' UNION SELECT `sub_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_hr['category']."' AND `heading_number`='$heading_number') `numbers`) GROUP BY `sub_heading_number`"));
				$heading_count = substr($heading_count['sub_heading_number'],strlen($heading_count['heading_number']) + 1) + 5;
				for($i = 1; $i <= $heading_count; $i++) {
					$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `hr` WHERE `sub_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_hr['category']."' AND `deleted`=0 UNION SELECT `sub_heading` FROM `manuals` WHERE `sub_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_hr['category']."' AND `deleted`=0"))['sub_heading']; ?>
					<option <?= $get_hr['sub_heading_number'] === $heading_number.'.'.$i ? 'selected' : '' ?> value="<?= $heading_number.'.'.$i ?>"><?= $heading_number.'.'.$i.' '.$heading ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Heading:</label>
		<div class="col-sm-8">
			<input class="form-control" name="sub_heading" value="<?= $get_hr['sub_heading'] ?>">
		</div>
	</div>
<?php } ?>
<?php if(in_array('Third Tier Heading',$field_config)) {
	$heading_number = $get_hr['sub_heading_number'] == '' ? '1.1' : $get_hr['sub_heading_number'];  ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Third Tier Section:</label>
		<div class="col-sm-8">
			<select data-placeholder="Select Section" name="third_heading_number" class="chosen-select-deselect"><option></option>
				<?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading_number`, `sub_heading_number` FROM (SELECT `third_heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `third_heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`third_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`third_heading_number`, 100, 0)) FROM (SELECT `third_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_hr['category']."' AND `heading_number`='$heading_number' UNION SELECT `third_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_hr['category']."' AND `sub_heading_number`='$heading_number') `numbers`) GROUP BY `third_heading_number`"));
				$heading_count = substr($heading_count['third_heading_number'],strlen($heading_count['sub_heading_number']) + 1) + 5;
				for($i = 1; $i <= $heading_count; $i++) {
					$heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading` FROM `hr` WHERE `third_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_hr['category']."' AND `deleted`=0 UNION SELECT `third_heading` FROM `manuals` WHERE `third_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_hr['category']."' AND `deleted`=0"))['third_heading']; ?>
					<option <?= $get_hr['third_heading_number'] === $heading_number.'.'.$i ? 'selected' : '' ?> value="<?= $heading_number.'.'.$i ?>"><?= $heading_number.'.'.$i.' '.$heading ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Heading:</label>
		<div class="col-sm-8">
			<input class="form-control" name="third_heading" value="<?= $get_hr['third_heading'] ?>">
		</div>
	</div>
<?php } ?>

<div class="form-group">
	<label class="col-sm-4 control-label">HR Fields:</label>
	<div class="col-sm-8">
		<?php if(in_array('First Name',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('First Name',$fields) ? 'checked' : '' ?> value="First Name"> First Name</label><?php } ?>
		<?php if(in_array('Last Name',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Last Name',$fields) ? 'checked' : '' ?> value="Last Name"> Last Name</label><?php } ?>
		<?php if(in_array('Birth Date',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Birth Date',$fields) ? 'checked' : '' ?> value="Birth Date"> Birth Date</label><?php } ?>
		<?php if(in_array('Employee Number',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Employee Number',$fields) ? 'checked' : '' ?> value="Employee Number"> Employee Number</label><?php } ?>
		<?php if(in_array('Address including Postal Code',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Address including Postal Code',$fields) ? 'checked' : '' ?> value="Address including Postal Code"> Address including Postal Code</label><?php } ?>
		<?php if(in_array('Signature box',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Signature box',$fields) ? 'checked' : '' ?> value="Signature box"> Signature box</label><?php } ?>
		<?php if(in_array('Comments',$field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Comments',$fields) ? 'checked' : '' ?> value="Comments"> Comments</label><?php } ?>
	</div>
</div>

<?php if (in_array('Detail',$field_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Detail:</label>
		<div class="col-sm-8">
			<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $get_hr['hr_description']; ?></textarea>
		</div>
	</div>
<?php } ?>

<?php $uploads = mysqli_query($dbc, "SELECT `uploadid`, `upload`,`type` FROM `hr_upload` WHERE `hrid`='$hrid'");
if(mysqli_num_rows($uploads) > 0) {
	echo "<ul>";
	while($upload = mysqli_fetch_assoc($uploads)) { ?>
		<li><?php switch($upload['type']) {
			case 'document':
				echo 'Document: ';
				echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&hr_uploadid='.$upload['uploadid'].'&type=document" onclick="return confirm(\'Are you sure?\')">Delete</a>';
				break;
			case 'link':
				echo 'Link: ';
				echo '<a href="'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&hr_uploadid='.$upload['uploadid'].'&type=link" onclick="return confirm(\'Are you sure?\')">Delete</a>';
				break;
			case 'video':
				echo 'Video: ';
				echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&hr_uploadid='.$upload['uploadid'].'&type=video" onclick="return confirm(\'Are you sure?\')">Delete</a>';
				break;
		} ?></li>
	<?php }
	echo "</ul>";
} ?>
<?php if (in_array('Document',$field_config)) { ?>
	<div class="form-group doc_group">
		<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span> Add Document(s):</label>
		<div class="col-sm-7">
			<input type="file" name="hr_document[]" class="form-control">
		</div>
		<div class="col-sm-1">
			<img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.doc_group').last().after($(this).closest('.doc_group').clone()); $('.doc_group').last().find('input').val('').focus();">
		</div>
	</div>
<?php } ?>

<?php if (in_array('Link',$field_config)) { ?>
	<div class="form-group link_group">
		<label class="col-sm-4 control-label">Add Link(s):<br><em>(e.g. - https://www.google.com)</em></label>
		<div class="col-sm-7">
			<input type="text" name="hr_link[]" class="form-control">
		</div>
		<div class="col-sm-1">
			<img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.link_group').last().after($(this).closest('.link_group').clone()); $('.link_group').last().find('input').val('').focus();">
		</div>
	</div>
<?php } ?>

<?php if (in_array('Videos',$field_config)) { ?>
	<div class="form-group video_group">
		<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span> Add Video(s):</label>
		<div class="col-sm-7">
			<input type="file" name="hr_video[]" class="form-control">
		</div>
		<div class="col-sm-1">
			<img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.video_group').last().after($(this).closest('.video_group').clone()); $('.video_group').last().find('input').val('').focus();">
		</div>
	</div>
<?php } ?>

<h4>Form Fields</h4>
<div class="form_fields">
	<?php $fields = ','.implode(',',$fields).',';
	switch($get_hr['form']) {
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
	} ?>
</div>

<?php if (in_array('Staff',$field_config)) { ?>
	<div class="form-group clearfix completion_date">
		<label class="col-sm-4 control-label text-right">Assign Staff:</label>
		<div class="col-sm-8">
			<select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
				<?php
				foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0")) as $row) {
					if (!empty(trim($get_hr['assign_staff'],','))) { ?>
						<option <?= (strpos(','.$$get_hr['assign_staff'].',', ','.$row['contactid'].',') !== FALSE) ? 'selected' : '' ?> value="<?= $row['contactid']; ?>"><?= $row['first_name'].' '.$row['last_name']; ?></option><?php
					} else { ?>
						<option selected value="<?= $row['contactid']; ?>"><?= $row['first_name'].' '.$row['last_name']; ?></option><?php
					}
				} ?>
			</select>
		</div>
	</div>
<?php } ?>

<?php if (in_array('Review Deadline',$field_config)) { ?>
	<div class="form-group clearfix">
		<label class="col-sm-4 control-label text-right">Review Deadline:</label>
		<div class="col-sm-8">
			<input name="deadline" type="text" class="form-control datepicker" value="<?= $get_hr['deadline'] ?>"></p>
		</div>
	</div>
<?php } ?>

<?php if (in_array('Recurring Due Dates',$field_config)) { ?>
	<h4>Recurring Due Dates</h4>
	<div class="recurring_block">
		<div class="form-group clearfix">
			<label class="col-sm-4 control-label text-right">Recurring Due Dates:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="checkbox" name="recurring_due_date" value="1" <?= !empty($get_hr['recurring_due_date']) ? 'checked' : '' ?> onchange="displayRecurringDueDate(this);"> Enable</label>
			</div>
		</div>
		<div class="recurring_due_date" <?= !empty($get_hr['recurring_due_date']) ? '' : 'style="display:none;"' ?>>
			<div class="form-group clearfix">
				<label class="col-sm-4 control-label text-right">Recurring Due Date Interval:</label>
				<div class="col-sm-8">
					<input type="number" name="recurring_due_date_interval" value="<?= $get_hr['recurring_due_date_interval'] ?>" <?= !empty($get_hr['recurring_due_date']) ? 'min="1"' : '' ?> class="form-control">
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4 control-label text-right">Recurring Due Date Type:</label>
				<div class="col-sm-8">
					<select name="recurring_due_date_type" class="chosen-select-deselect form-control">
						<option></option>
						<option value="days" <?= $get_hr['recurring_due_date_type'] == 'days' ? 'selected' : ''?>>Days</option>
						<option value="weeks" <?= $get_hr['recurring_due_date_type'] == 'weeks' ? 'selected' : ''?>>Weeks</option>
						<option value="months" <?= $get_hr['recurring_due_date_type'] == 'months' ? 'selected' : ''?>>Months</option>
						<option value="years" <?= $get_hr['recurring_due_date_type'] == 'years' ? 'selected' : ''?>>Years</option>
					</select>
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4 control-label text-right">Create Reminder On Recurring Date:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" name="recurring_due_date_reminder" value="1" <?= !empty($get_hr['recurring_due_date_reminder']) ? 'checked' : '' ?>> Enable</label>
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4 control-label text-right">Send Email On Recurring Date:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" name="recurring_due_date_email" value="1" <?= !empty($get_hr['recurring_due_date_email']) ? 'checked' : '' ?>> Enable</label>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<!-- Configure Email -->
<?php if (in_array('Configure Email',$field_config)) { ?>
	<h4>Email on Assignment of Form</h4>
	<div class="form-group clearfix">
		<label for="email_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
		<div class="col-sm-8"><input class="form-control" name="email_subject" type="text" value="<?= $get_hr['email_subject'] ?>"></div>
	</div>
	<div class="form-group clearfix">
		<label for="email_message" class="col-sm-4 control-label text-right">Email Message:</label>
		<div class="col-sm-8"><textarea name="email_message"><?= html_entity_decode($get_hr['email_message']) ?></textarea></div>
	</div>
	<h4>Email on Submission</h4>
	<div class="form-group clearfix">
		<label for="completed_recipient" class="col-sm-4 control-label text-right">Email Recipient:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect form-control" data-placeholder="Select Staff" name="completed_recipient"><option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != ''")) as $contact) { ?>
					<option <?= $contact['contactid'] == $get_hr['completed_recipient'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'].' ('.$contact['email_address'].')' ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group clearfix">
		<label for="completed_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
		<div class="col-sm-8"><input class="form-control" name="completed_subject" type="text" value="<?= $get_hr['completed_subject'] ?>"></div>
	</div>
	<div class="form-group clearfix">
		<label for="completed_message" class="col-sm-4 control-label text-right">Email Message:</label>
		<div class="col-sm-8"><textarea name="completed_message"><?= html_entity_decode($get_hr['completed_message']) ?></textarea></div>
	</div>
	<h4>Email on Approval</h4>
	<div class="form-group clearfix">
		<label for="approval_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
		<div class="col-sm-8"><input class="form-control" name="approval_subject" type="text" value="<?= $get_hr['approval_subject'] ?>"></div>
	</div>
	<div class="form-group clearfix">
		<label for="approval_message" class="col-sm-4 control-label text-right">Email Message:</label>
		<div class="col-sm-8"><textarea name="approval_message"><?= html_entity_decode($get_hr['approval_message']) ?></textarea></div>
	</div>
	<h4>Email on Rejection</h4>
	<div class="form-group clearfix">
		<label for="rejected_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
		<div class="col-sm-8"><input class="form-control" name="rejected_subject" type="text" value="<?= $get_hr['rejected_subject'] ?>"></div>
	</div>
	<div class="form-group clearfix">
		<label for="rejected_message" class="col-sm-4 control-label text-right">Email Message:</label>
		<div class="col-sm-8"><textarea name="rejected_message"><?= html_entity_decode($get_hr['rejected_message']) ?></textarea></div>
	</div>
<?php } ?>

<!-- Permissions by Position -->
<?php if (in_array('Permissions by Position',$field_config)) { ?>
	<div class="form-group clearfix">
		<label for="permissions_position" class="col-sm-4 control-label text-right">Permissions by Position:</label>
		<div class="col-sm-8">
		<select data-placeholder="Choose Positions..." id="permissions_position" name="permissions_position[]" class="chosen-select-deselect form-control" width="380" multiple>
			<?php
				$query = "SELECT DISTINCT `position` FROM `contacts` WHERE `deleted` = 0 ORDER BY `position`";
				$result = mysqli_query($dbc, $query);

				while ($row = mysqli_fetch_array($result)) {
					echo '<option value="'.$row['position'].'" '.(strpos(','.$$get_hr['permissions_position'].',', ','.$row['position'].',') !== FALSE ? 'selected' : '').'>'.$row['position'].'</option>';
				}
			?>
		</select>
		</div>
	</div>
<?php } ?>