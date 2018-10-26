<script>
function changeForm(form) {
	$.ajax({
		url: 'hr_ajax.php?action=set_form_fields',
		method: 'POST',
		data: {
			form: form
		},
		success: function(response) {
			$('.form_fields').html(response);
		}
	});
}
function changeCategory(category) {
	$.ajax({
		url: 'hr_ajax.php?action=set_category',
		method: 'POST',
		data: {
			category: category
		},
		success: function(response) {
			$('[name=heading_number]').html(response).trigger('change.select2');
		}
	});
}
function changeSection(section) {
	$.ajax({
		url: 'hr_ajax.php?action=set_form_section',
		method: 'POST',
		data: {
			section: section,
			category: $('[name=category]').val()
		},
		success: function(response) {
			response = response.split('#*#');
			$('[name=heading]').val(response[0]);
			$('[name=sub_heading_number]').html(response[1]).trigger('change.select2');
		}
	});
}
function changeSubSection(subsection) {
	$.ajax({
		url: 'hr_ajax.php?action=set_form_subsection',
		method: 'POST',
		data: {
			subsection: subsection,
			category: $('[name=category]').val()
		},
		success: function(response) {
			response = response.split('#*#');
			$('[name=sub_heading]').val(response[0]);
			$('[name=third_heading_number]').html(response[1]).trigger('change.select2');
		}
	});
}
</script>
<?php $hrid = filter_var($_GET['hr_edit'], FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$favourite = filter_var($_POST['favourite'],FILTER_SANITIZE_STRING);
	$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
	$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
	$sub_heading_number = filter_var($_POST['sub_heading_number'],FILTER_SANITIZE_STRING);
	$sub_heading = filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);
	$third_heading_number = filter_var($_POST['third_heading_number'],FILTER_SANITIZE_STRING);
	$third_heading = filter_var($_POST['third_heading'],FILTER_SANITIZE_STRING);
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
	$assign_staff = filter_var(implode(',',$_POST['assign_staff']),FILTER_SANITIZE_STRING);
	$deadline = filter_var($_POST['deadline'],FILTER_SANITIZE_STRING);
	$fields = filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING);
	$email_subject = filter_var($_POST['email_subject'],FILTER_SANITIZE_STRING);
	$email_message = filter_var(htmlentities($_POST['email_message']),FILTER_SANITIZE_STRING);
	$completed_recipient = filter_var($_POST['completed_recipient'],FILTER_SANITIZE_STRING);
	$approval_subject = filter_var($_POST['approval_subject'],FILTER_SANITIZE_STRING);
	$approval_message = filter_var(htmlentities($_POST['approval_message']),FILTER_SANITIZE_STRING);
	$rejected_subject = filter_var($_POST['rejected_subject'],FILTER_SANITIZE_STRING);
	$rejected_message = filter_var(htmlentities($_POST['rejected_message']),FILTER_SANITIZE_STRING);
	$recurring_due_date = filter_var($_POST['recurring_due_date'],FILTER_SANITIZE_STRING);
	$recurring_due_date_interval = filter_var($_POST['recurring_due_date_interval'],FILTER_SANITIZE_STRING);
	$recurring_due_date_type = filter_var($_POST['recurring_due_date_type'],FILTER_SANITIZE_STRING);
	$recurring_due_date_reminder = filter_var($_POST['recurring_due_date_reminder'],FILTER_SANITIZE_STRING);
	$recurring_due_date_email = filter_var($_POST['recurring_due_date_email'],FILTER_SANITIZE_STRING);
	$form_name = filter_var($_POST['form'],FILTER_SANITIZE_STRING);
	if($_POST['form'] > 0) {
		$user_form_id = filter_var($_POST['form'],FILTER_SANITIZE_STRING);
	} else {
		$user_form_id = 0;
	}

	if($hrid > 0) {
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


		mysqli_query($dbc, "UPDATE `hr` SET `form`='$form_name', `category`='$category', `favourite`='$favourite', `heading_number`='$heading_number', `heading`='$heading', `sub_heading_number`='$sub_heading_number', `sub_heading`='$sub_heading', `third_heading_number`='$third_heading_number', `third_heading`='$third_heading', `hr_description`='$description', `assign_staff`='$assign_staff', `deadline`='$deadline', `fields`='$fields', `email_subject`='$email_subject', `email_message`='$email_message', `completed_recipient`='$completed_recipient', `approval_subject`='$approval_subject', `approval_message`='$approval_message', `rejected_subject`='$rejected_subject', `rejected_message`='$rejected_message', `user_form_id`='$user_form_id', `recurring_due_date` = '$recurring_due_date', `recurring_due_date_interval` = '$recurring_due_date_interval', `recurring_due_date_type` = '$recurring_due_date_type', `recurring_due_date_reminder` = '$recurring_due_date_reminder', `recurring_due_date_email` = '$recurring_due_date_email' WHERE `hrid`='$hrid'");

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

	} else {
		mysqli_query($dbc, "INSERT INTO `hr` (`form`, `category`, `favourite`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `hr_description`, `assign_staff`, `deadline`, `fields`, `email_subject`, `email_message`, `completed_recipient`, `approval_subject`, `approval_message`, `rejected_subject`, `rejected_message`, `user_form_id`, `recurring_due_date`, `recurring_due_date_interval`, `recurring_due_date_type`, `recurring_due_date_reminder`, `recurring_due_date_email`)
			VALUES ('$form_name', '$category', '$favourite', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$third_heading_number', '$third_heading', '$description', ',$assign_staff,', '$deadline', '$fields', '$email_subject', '$email_message', '$completed_recipient', '$approval_subject', '$approval_message', '$rejected_subject', '$rejected_message', '$user_form_id', '$recurring_due_date', '$recurring_due_date_interval', '$recurring_due_date_type', '$recurring_due_date_reminder', '$recurring_due_date_email')");
		$hrid = mysqli_insert_id($dbc);
		$before_change = '';
	  $history = "HR entry added. <br />";
	  add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
	foreach($_FILES['hr_document']['name'] as $i => $file) {
		if($file != '') {
			$filename = file_safe_str($file['name']);
			if(!file_exists('download')) {
				mkdir('download');
			}
			move_uploaded_file($_FILES['hr_document']['tmp_name'][$i],'download/'.$filename);
			mysqli_query($dbc, "INSERT INTO `hr_upload` (`hrid`,`type`,`upload`) VALUES ('$hrid','document','$filename')");
			$before_change = '';
		  $history = "HR upload entry added. <br />";
		  add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_POST['hr_link'] as $i => $link) {
		if(!empty($link)) {
			$link = filter_var($link,FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `hr_upload` (`hrid`,`type`,`upload`) VALUES ('$hrid','link','$link')");
			$before_change = '';
		  $history = "HR upload entry added. <br />";
		  add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_FILES['hr_video']['name'] as $i => $file) {
		if(!empty($file)) {
			$filename = file_safe_str($file);
			if(!file_exists('download')) {
				mkdir('download');
			}
			move_uploaded_file($_FILES['hr_video']['tmp_name'][$i],'download/'.$filename);
			mysqli_query($dbc, "INSERT INTO `hr_upload` (`hrid`,`type`,`upload`) VALUES ('$hrid','video','$filename')");
			$before_change = '';
		  $history = "HR upload entry added. <br />";
		  add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_POST['assign_staff'] as $staff) {
		if($staff > 0) {
			mysqli_query($dbc, "INSERT INTO `hr_attendance` (`hrid`, `assign_staffid`) SELECT '$hrid', '$staff' FROM (SELECT COUNT(*) `rows` FROM `hr_attendance` WHERE `hrid`='$hrid' AND `assign_staffid`='$staff' AND `done`=0) `num` WHERE `num`.`rows`=0");
			$before_change = '';
		  $history = "HR attendance entry added. <br />";
		  add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	if($_POST['submit'] == 'email') {
		$heading = $third_heading != '' ? $third_heading_number.' '.$third_heading : ($sub_heading != '' ? $sub_heading_number.' '.$sub_heading : $heading_number.' '.$heading);
		$subject = str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$email_subject);
		$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$email_message).'<p>Click <a href="'.WEBSITE_URL.'/HR/index.php?hr='.$hrid.'">here</a> to complete the form.</p>');
		foreach($_POST['assign_staff'] as $staff) {
			if($staff > 0) {
				$to = get_email($dbc, $staff);
				try {
					send_email('', $to, '', '', $subject, $body, '');
				} catch(Exception $e) { }
			}
		}
	}
	$back_url = '?tile_name=".$tile."';
	if(isset($_GET['back_url'])) {
		$back_url = urldecode($_GET['back_url']);
	}
	echo "<script> window.location.replace('".$back_url."'); </script>";
}
$field_config = explode(',',get_config($dbc, 'hr_fields'));
$get_hr = [];
if($hrid > 0) {
	$get_hr = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid`='$hrid'"));
}
$fields = explode(',',$get_hr['fields']); ?>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen'>
		<form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
            <div class="standard-body-title">
                <h3><?= $hrid > 0 ? 'Edit' : 'Create' ?> Form</h3>
            </div>
            <div class="standard-body-content pad-10">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Form:</label>
                    <div class="col-sm-8">
                        <select name="form" data-placeholder="Select a Form" data-table="hr" data-id="<?= $hrid ?>" data-id-field="hrid" class="chosen-select-deselect" onchange="changeForm(this.value);"><option></option>
                            <option <?php if ($get_hr['form'] == "Employee Information Form") { echo " selected"; } ?> value="Employee Information Form">Employee Information Form</option>
                            <option <?php if ($get_hr['form'] == "Employee Driver Information Form") { echo " selected"; } ?> value="Employee Driver Information Form">Employee Driver Information Form</option>
                            <option <?php if ($get_hr['form'] == "Time Off Request") { echo " selected"; } ?> value="Time Off Request">Time Off Request</option>
                            <option <?php if ($get_hr['form'] == "Confidential Information") { echo " selected"; } ?> value="Confidential Information">Confidential Information</option>
                            <option <?php if ($get_hr['form'] == "Work Hours Policy") { echo " selected"; } ?> value="Work Hours Policy">Work Hours Policy</option>
                            <option <?php if ($get_hr['form'] == "Direct Deposit Information") { echo " selected"; } ?> value="Direct Deposit Information">Direct Deposit Information</option>
                            <option <?php if ($get_hr['form'] == "Employee Substance Abuse Policy") { echo " selected"; } ?> value="Employee Substance Abuse Policy">Employee Substance Abuse Policy</option>
                            <option <?php if ($get_hr['form'] == "Employee Right to Refuse Unsafe Work") { echo " selected"; } ?> value="Employee Right to Refuse Unsafe Work">Employee Right to Refuse Unsafe Work</option>
                            <option <?php if ($get_hr['form'] == "Shop Yard and Office Orientation") { echo " selected"; } ?> value="Shop Yard and Office Orientation">Shop Yard and Office Orientation</option>
                            <option <?php if ($get_hr['form'] == "Copy of Drivers Licence and Safety Tickets") { echo " selected"; } ?> value="Copy of Drivers Licence and Safety Tickets">Copy of Driver's Licence and Safety Tickets</option>
                            <option <?php if ($get_hr['form'] == "PPE Requirements") { echo " selected"; } ?> value="PPE Requirements">PPE Requirements</option>
                            <option <?php if ($get_hr['form'] == "Verbal Training in Emergency Response Plan") { echo " selected"; } ?> value="Verbal Training in Emergency Response Plan">Verbal Training in Emergency Response Plan</option>
                            <option <?php if ($get_hr['form'] == "Eligibility for General Holidays and General Holiday Pay") { echo " selected"; } ?> value="Eligibility for General Holidays and General Holiday Pay">Eligibility for General Holidays and General Holiday Pay</option>
                            <option <?php if ($get_hr['form'] == "Maternity Leave and Parental Leave") { echo " selected"; } ?> value="Maternity Leave and Parental Leave">Maternity Leave and Parental Leave</option>
                            <option <?php if ($get_hr['form'] == "Employment Verification Letter") { echo " selected"; } ?> value="Employment Verification Letter">Employment Verification Letter</option>
                            <option <?php if ($get_hr['form'] == "Background Check Authorization") { echo " selected"; } ?> value="Background Check Authorization">Background Check Authorization</option>
                            <option <?php if ($get_hr['form'] == "Disclosure of Outside Clients") { echo " selected"; } ?> value="Disclosure of Outside Clients">Disclosure of Outside Clients</option>
                            <option <?php if ($get_hr['form'] == "Employment Agreement") { echo " selected"; } ?> value="Employment Agreement">Employment Agreement</option>
                            <option <?php if ($get_hr['form'] == "Independent Contractor Agreement") { echo " selected"; } ?> value="Independent Contractor Agreement">Independent Contractor Agreement</option>
                            <option <?php if ($get_hr['form'] == "Letter of Offer") { echo " selected"; } ?> value="Letter of Offer">Letter of Offer</option>
                            <option <?php if ($get_hr['form'] == "Employee Non-Disclosure Agreement") { echo " selected"; } ?> value="Employee Non-Disclosure Agreement">Employee Non-Disclosure Agreement</option>
                            <option <?php if ($get_hr['form'] == "Employee Self Evaluation") { echo " selected"; } ?> value="Employee Self Evaluation">Employee Self Evaluation</option>
                            <option <?php if ($get_hr['form'] == "HR Complaint") { echo " selected"; } ?> value="HR Complaint">HR Complaint</option>
                            <option <?php if ($get_hr['form'] == "Exit Interview") { echo " selected"; } ?> value="Exit Interview">Exit Interview</option>
                            <option <?php if ($get_hr['form'] == "Employee Expense Reimbursement") { echo " selected"; } ?> value="Employee Expense Reimbursement">Employee Expense Reimbursement</option>
                            <option <?php if ($get_hr['form'] == "Absence Report") { echo " selected"; } ?> value="Absence Report">Absence Report</option>
                            <option <?php if ($get_hr['form'] == "Employee Accident Report Form") { echo " selected"; } ?> value="Employee Accident Report Form">Employee Accident Report Form</option>
                            <option <?php if ($get_hr['form'] == "Trucking Information") { echo " selected"; } ?> value="Trucking Information">Trucking Information</option>
                            <option <?php if ($get_hr['form'] == "Contractor Orientation") { echo " selected"; } ?> value="Contractor Orientation">Contractor Orientation</option>
                            <option <?php if ($get_hr['form'] == "Contract Welder Inspection Checklist") { echo " selected"; } ?> value="Contract Welder Inspection Checklist">Contract Welder Inspection Checklist</option>
                            <option <?php if ($get_hr['form'] == "Contractor Pay Agreement") { echo " selected"; } ?> value="Contractor Pay Agreement">Contractor Pay Agreement</option>
                            <option <?php if ($get_hr['form'] == "Employee Holiday Request Form") { echo " selected"; } ?> value="Employee Holiday Request Form">Employee Holiday Request Form</option>
                            <option <?php if ($get_hr['form'] == "Employee Coaching Form") { echo " selected"; } ?> value="Employee Coaching Form">Employee Coaching Form</option>
                            <option <?php if ($get_hr['form'] == "2016 Alberta Personal Tax Credits Return") { echo " selected"; } ?> value="2016 Alberta Personal Tax Credits Return">2016 Alberta Personal Tax Credits Return TD1AB</option>
                            <option <?php if ($get_hr['form'] == "2016 Personal Tax Credits Return") { echo " selected"; } ?> value="2016 Personal Tax Credits Return">2016 Alberta Personal Tax Credits Return TD1</option>
                            <option <?php if ($get_hr['form'] == "Driver Abstract Statement of Intent") { echo " selected"; } ?> value="Driver Abstract Statement of Intent">Driver Abstract Statement of Intent</option>
                            <option <?php if ($get_hr['form'] == "PERSONAL PROTECTIVE EQUIPMENT POLICY") { echo " selected"; } ?> value="PERSONAL PROTECTIVE EQUIPMENT POLICY">PERSONAL PROTECTIVE EQUIPMENT POLICY</option>
                            <option <?php if ($get_hr['form'] == "DRIVER CONSENT FORM") { echo " selected"; } ?> value="DRIVER CONSENT FORM">DRIVER CONSENT FORM</option>
                            <option <?php if ($get_hr['form'] == "Policy and Procedure Notice of Understanding and Intent") { echo " selected"; } ?> value="Policy and Procedure Notice of Understanding and Intent">Policy and Procedure Notice of Understanding and Intent</option>
                            <option <?php if ($get_hr['form'] == "Employee Personal and Emergency Information") { echo " selected"; } ?> value="Employee Personal and Emergency Information">Employee Personal and Emergency Information</option>
                            <option <?php if ($get_hr['form'] == "Employment Agreement Evergreen") { echo " selected"; } ?> value="Employment Agreement Evergreen">Employment Agreement Evergreen</option>
                            <option <?php if ($get_hr['form'] == "Police Information Check") { echo " selected"; } ?> value="Police Information Check">Police Information Check</option>
                            <option <?php if ($get_hr['form'] == "Manual") { echo " selected"; } ?> value="Manual">Manual</option>
                            <?php $query = mysqli_query ($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,hr,%'");;
                            while ($row = mysqli_fetch_array($query)) { ?>
                                <option <?php if ($get_hr['user_form_id'] == $row['form_id']) { echo " selected" ; } ?> value="<?php echo $row['form_id']; ?>"><?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php include('hr_config.php'); ?>
                <button class="pull-right btn brand-btn" name="submit" value="">Submit</button>
                <button class="pull-right btn brand-btn" name="submit" value="email">Send Email</button>
                <div class="clearfix"></div>
            </div>
		</form>
	</div>
</div>
