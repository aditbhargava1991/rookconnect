<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

$from_url = 'hr.php?tab=Toolbox';
if (!empty($_GET['from'])) {
    $from_url = $_GET['from'];
}

if (isset($_POST['add_manual'])) {
    include ('save_manual.php');
}

if((!empty($_GET['hrid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $hrid = $_GET['hrid'];
    $category = get_hr($dbc, $hrid, 'category');
    $tab = get_hr($dbc, $hrid, 'tab');
        $date_of_archival = date('Y-m-d');

    // $query = mysqli_query($dbc,"DELETE FROM hr WHERE hrid='$hrid'");
    $query = mysqli_query($dbc,"UPDATE hr SET deleted=1, `date_of_archival` = '$date_of_archival' WHERE hrid='$hrid'");
    echo '<script type="text/javascript"> window.location.replace("hr.php?tab='.$tab.'&category='.$category.'"); </script>';
}

if((empty($_GET['hrid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete') && $_GET['hr_uploadid'] > 0) {
    $uploadid = $_GET['hr_uploadid'];
    $hrid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `hrid` FROM `hr_upload` WHERE `uploadid` = '$uploadid'"))['hrid'];
    $query = mysqli_query($dbc,"DELETE FROM hr_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    // $hrid = $_GET['hrid'];
    echo '<script type="text/javascript">  window.location.replace("index.php?hr_edit='.$hrid.'"); </script>';
}
if((empty($_GET['hrid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete') && $_GET['manual_uploadid'] > 0) {
    $uploadid = $_GET['manual_uploadid'];
    $manualid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `manualtypeid` FROM `manuals_upload` WHERE `uploadid` = '$uploadid'"))['manualtypeid'];
    $query = mysqli_query($dbc,"DELETE FROM manuals_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    // $hrid = $_GET['hrid'];
    echo '<script type="text/javascript">  window.location.replace("index.php?manual_edit='.$manualid.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $hrid = $_POST['hrid'];

    $type = $_POST['type'];

    if($comment != '') {
        if($type == 'policy_procedures') {
            $column = 'manual_policy_pro_email';
        }
        if($type == 'operations_manual') {
            $column = 'manual_operations_email';
        }
        if($type == 'emp_handbook') {
            $column = 'manual_emp_handbook_email';
        }
        if($type == 'guide') {
            $column = 'manual_guide_email';
        }
        if($type == 'hr') {
            $column = 'manual_hr_email';
        }

        $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	hrid='$hrid'"));

        //Mail
        $to = get_config($dbc, $column);
        $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
        $subject = 'Manual Read by '.$user;

        $message = "Topic (Sub Tab) : ".$get_manual['category'].'<br>';
        $message .= "Section Heading : ".$get_manual['heading'].'<br>';
        $message .= "Sub Section Heading : ".$get_manual['sub_heading'].'<br>';
        $message .= "Comment<br/><br/>".$_POST['comment'];
        send_email('', $to, '', '', $subject, $message, '');

        //Mail
    }

    $staffid = $_SESSION['contactid'];
    $today_date = date('Y-m-d H:i:s');
	// Insert a row if it isn't already there
	$query_insert_row = "INSERT INTO `hr_staff` (`hrid`, `staffid`) SELECT '$hrid', '$staffid' FROM (SELECT COUNT(*) rows FROM `hr_staff` WHERE `hrid`='$hrid' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
  $before_change = '';
  $history = "HR Staff entry added. <br />";
  add_update_history($dbc, 'hr_history', $history, '', $before_change);
	mysqli_query($dbc, $query_insert_row);

    $before_change = capture_before_change($dbc, 'hr_staff', 'done', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);
    $before_change .= capture_before_change($dbc, 'hr_staff', 'today_date', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);

    $query_update_ticket = "UPDATE `hr_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `hrid` = '$hrid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

    $history = capture_after_change('done', 1);
    $history .= capture_after_change('today_date', $today_date);
    add_update_history($dbc, 'hr_history', $history, '', $before_change);

    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?category='.$get_manual['category'].'"); </script>';
}

if (isset($_POST['field_level_hazard'])) {
    $field_level_hazard = $_POST['field_level_hazard'];
    $hrid = $_POST['hrid'];

    $staffid = $_SESSION['contactid'];
    $today_date = date('Y-m-d H:i:s');
	// Insert a row if it isn't already there
	$query_insert_row = "INSERT INTO `hr_staff` (`hrid`, `staffid`) SELECT '$hrid', '$staffid' FROM (SELECT COUNT(*) rows FROM `hr_staff` WHERE `hrid`='$hrid' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
  $before_change = '';
  $history = "HR Staff entry added. <br />";
  add_update_history($dbc, 'hr_history', $history, '', $before_change);
	mysqli_query($dbc, $query_insert_row);
    $before_change = capture_before_change($dbc, 'hr_staff', 'done', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);
    $before_change .= capture_before_change($dbc, 'hr_staff', 'today_date', 'hrid', $hrid, 'staffid', $staffid, 'done', 0);

    $query_update_ticket = "UPDATE `hr_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `hrid` = '$hrid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

    $history = capture_after_change('done', 1);
    $history .= capture_after_change('today_date', $today_date);
    add_update_history($dbc, 'hr_history', $history, '', $before_change);

    //Update reminders to done
    mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$staffid' AND `src_table` = 'hr' AND `src_tableid` = '$hrid'");

    $form_name_save = get_hr($dbc, $hrid, 'form');

    if(isset($_POST['form_id'])) {
        include ('user_forms.php');
    } else {
        if($form_name_save == 'Employee Information Form') {
            include ('employee_information_form/save_employee_information_form.php');
        }
        if($form_name_save == 'Employee Driver Information Form') {
            include ('employee_driver_information_form/save_employee_driver_information_form.php');
        }
        if($form_name_save == 'Time Off Request') {
            include ('time_off_request/save_time_off_request.php');
        }
        if($form_name_save == 'Confidential Information') {
            include ('confidential_information/save_confidential_information.php');
        }
        if($form_name_save == 'Work Hours Policy') {
            include ('work_hours_policy/save_work_hours_policy.php');
        }
        if($form_name_save == 'Direct Deposit Information') {
            include ('direct_deposit_information/save_direct_deposit_information.php');
        }
        if($form_name_save == 'Employee Substance Abuse Policy') {
            include ('employee_substance_abuse_policy/save_employee_substance_abuse_policy.php');
        }
        if($form_name_save == 'Employee Right to Refuse Unsafe Work') {
            include ('employee_right_to_refuse_unsafe_work/save_employee_right_to_refuse_unsafe_work.php');
        }
        if($form_name_save == 'Shop Yard and Office Orientation') {
            include ('employee_shop_yard_office_orientation/save_employee_shop_yard_office_orientation.php');
        }
        if($form_name_save == "Copy of Drivers Licence and Safety Tickets") {
            include ('copy_of_drivers_licence_safety_tickets/save_copy_of_drivers_licence_safety_tickets.php');
        }
        if($form_name_save == 'PPE Requirements') {
            include ('ppe_requirements/save_ppe_requirements.php');
        }
        if($form_name_save == 'Verbal Training in Emergency Response Plan') {
            include ('verbal_training_in_emergency_response_plan/save_verbal_training_in_emergency_response_plan.php');
        }
        if($form_name_save == 'Eligibility for General Holidays and General Holiday Pay') {
            include ('eligibility_for_general_holidays_general_holiday_pay/save_eligibility_for_general_holidays_general_holiday_pay.php');
        }
        if($form_name_save == 'Maternity Leave and Parental Leave') {
            include ('maternity_leave_parental_leave/save_maternity_leave_parental_leave.php');
        }
        if($form_name_save == 'Employment Verification Letter') {
            include ('employment_verification_letter/save_employment_verification_letter.php');
        }
        if($form_name_save == 'Background Check Authorization') {
            include ('background_check_authorization/save_background_check_authorization.php');
        }
        if($form_name_save == 'Disclosure of Outside Clients') {
            include ('disclosure_of_outside_clients/save_disclosure_of_outside_clients.php');
        }
        if($form_name_save == 'Employment Agreement') {
            include ('employment_agreement/save_employment_agreement.php');
        }
        if($form_name_save == 'Independent Contractor Agreement') {
            include ('independent_contractor_agreement/save_independent_contractor_agreement.php');
        }
        if($form_name_save == 'Letter of Offer') {
            include ('letter_of_offer/save_letter_of_offer.php');
        }
        if($form_name_save == 'Employee Non-Disclosure Agreement') {
            include ('employee_nondisclosure_agreement/save_employee_nondisclosure_agreement.php');
        }
        if($form_name_save == 'Employee Self Evaluation') {
            include ('employee_self_evaluation/save_employee_self_evaluation.php');
        }
        if($form_name_save == 'HR Complaint') {
            include ('hr_complaint/save_hr_complaint.php');
        }
        if($form_name_save == 'Exit Interview') {
            include ('exit_interview/save_exit_interview.php');
        }
        if($form_name_save == 'Employee Expense Reimbursement') {
            include ('employee_expense_reimbursement/save_employee_expense_reimbursement.php');
        }
        if($form_name_save == 'Absence Report') {
            include ('absence_report/save_absence_report.php');
        }
        if($form_name_save == 'Employee Accident Report Form') {
            include ('employee_accident_report_form/save_employee_accident_report_form.php');
        }
        if($form_name_save == 'Trucking Information') {
            include ('trucking_information/save_trucking_information.php');
        }
        if($form_name_save == 'Contractor Orientation') {
            include ('contractor_orientation/save_contractor_orientation.php');
        }
        if($form_name_save == 'Contract Welder Inspection Checklist') {
            include ('contract_welder_inspection_checklist/save_contract_welder_inspection_checklist.php');
        }
        if($form_name_save == 'Contractor Pay Agreement') {
            include ('contractor_pay_agreement/save_contractor_pay_agreement.php');
        }
        if($form_name_save == 'Employee Holiday Request Form') {
            include ('employee_holiday_request_form/save_employee_holiday_request_form.php');
        }
        if($form_name_save == 'Employee Coaching Form') {
            include ('employee_coaching_form/save_employee_coaching_form.php');
        }
    	if ($form_name_save == '2016 Alberta Personal Tax Credits Return') {
            include ('2016_alberta_personal/save_2016_alberta_personal.php');
    	}
    	if ($form_name_save == '2016 Personal Tax Credits Return') {
    		include ('tax_credit_1/save_2016_alberta_personal.php');
    	}
    	if ($form_name_save == 'Driver Abstract Statement of Intent') {
    		include ('driver_abstract_statement_of_intent/save_driver_abstract_statement_of_intent.php');
    	}
    	if ($form_name_save == 'PERSONAL PROTECTIVE EQUIPMENT POLICY') {
    		include ('personal_protective_equipment_policy/save_personal_protective_equipment_policy.php');
    	}
    	if ($form_name_save == 'DRIVER CONSENT FORM') {
    		include ('driver_consent_form/save_driver_consent_form.php');
    	}
    	if ($form_name_save == 'Policy and Procedure Notice of Understanding and Intent') {
    		include ('policy_and_procedure_notice_of_understanding_and_intent/save_policy_and_procedure_notice_of_understanding_and_intent.php');
    	}
    	if ($form_name_save == 'Employee Personal and Emergency Information') {
    		include ('employee_personal_and_emergency_information/save_employee_personal_and_emergency_information.php');
    	}
    	if ($form_name_save == 'Employment Agreement Evergreen') {
    		include ('employment_agreement_evergreen/save_employment_agreement.php');
    	}
        if ($form_name_save == 'Police Information Check') {
            include ('police_information_check/save_police_information_check.php');
        }
    }
}
?>
<script type="text/javascript">
$(document).ready(function() {

	$("#tab_field").change(function() {
        window.location = 'add_manual.php?type=hr&tab='+this.value;
	});

	$("#form_name").change(function() {
        var tab = $("#tab_field").val();
        window.location = 'add_manual.php?type=hr&tab='+tab+'&form='+this.value;
	});

    $("#category").change(function() {
        if($("#category option:selected").text() == 'New Topic (Sub Tab)') {
                $( "#new_category" ).show();
        } else {
            $( "#new_category" ).hide();
        }
    });

    $("#heading").change(function() {
        if($("#heading option:selected").text() == 'New Heading') {
                $( "#new_heading" ).show();
        } else {
            $( "#new_heading" ).hide();
        }
    });

    $("#heading_number").change(function() {
        if($("#heading_number option:selected").text() == 'New Heading Number') {
                $("#new_heading_number").show();
        } else {
            $( "#new_heading_number" ).hide();
        }
    });

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

    $('#add_row_link').on( 'click', function () {
        var clone = $('.additional_link').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_link");
        $('#add_here_new_link').append(clone);
        return false;
    });

    $('#add_row_videos').on( 'click', function () {
        var clone = $('.additional_videos').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_videos");
        $('#add_here_new_videos').append(clone);
        return false;
    });

} );
function selectSection(sel) {
    var category = $('#category').val();
	var heading_number = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "manual_ajax_all.php?fill=section&heading_number="+heading_number+"&category="+category,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#heading").val(response);
			$("#heading").trigger("change.select2");
		}
	});
	if(heading_number > 0) {
		var current_sub = $('#sub_heading_value').val();
		var max_sub = $('#max_subsection').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "manual_ajax_all.php?fill=sub_heading_number",
			data: { heading_number: heading_number, sub_heading: current_sub, category: category, max_section: max_sub },
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#sub_heading_number").empty().html(response).trigger("change.select2");
			}
		});
	}
	else {
		$("#sub_heading_number").empty().trigger("change.select2");
	}
}
function selectSubSection(sel) {
    var category = $('#category').val();
	var sub_heading_number = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "manual_ajax_all.php?fill=subsection&sub_heading_number="+sub_heading_number+"&category="+category,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#sub_heading").val(response);
		}
	});
	if(sub_heading_number > 0) {
		var current_sub = $('#third_heading_value').val();
		var max_sub = $('#max_subsection').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "manual_ajax_all.php?fill=third_heading_number",
			data: { sub_heading_number: sub_heading_number, third_heading: current_sub, category: category, max_section: max_sub },
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#third_heading_number").empty().html(response).trigger("change.select2");
			}
		});
	}
	else {
		$("#third_heading_number").empty().trigger("change.select2");
	}
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('hr');
?>
<div class="container">
  <div class="row">
	<h1>HR</h1>
    <div class="gap-top double-gap-bottom"><a href="<?= $from_url ?>" class="btn config-btn">Back to Dashboard</a></div>

	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $hr_tabs = get_config($dbc, 'hr_tabs');
        $category = '';
        $heading = '';
        $sub_heading = '';
        $description = '';
        $assign_staff = '';
        $deadline = '';
		$email_subject = '';
		$email_message = '';
		$completed_recipient = '';
		$completed_subject = 'A form has been completed for you to review';
		$completed_message = htmlentities('<p>A HR form has been submitted and needs to be reviewed. Please log in to the software to view it.</p>');
		$approval_subject = 'Your HR Form has been approved';
		$approval_message = htmlentities('<p>Your HR Form has been approved.</p>');
		$rejected_subject = 'Your HR Form has been rejected';
		$rejected_message = htmlentities('<p>Your HR Form has been rejected.</p>');
        $action = '';
        $heading_number = '';
        $sub_heading_number = '';
        $third_heading_number = '';
        $third_heading = '';
        $form_name = '';

        $tab = $_GET['tab'];
        $form = $_GET['form'];
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_hr WHERE tab='$tab' AND form='$form'"));
        $value_config = ','.$get_field_config['fields'].',';
        $max_section = $get_field_config['max_section'];
        $max_subsection = $get_field_config['max_subsection'];
        $max_thirdsection = $get_field_config['max_thirdsection'];
        $user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `form_id` FROM `user_forms` WHERE `form_id` = '$form'"))['form_id'];

        if(!empty($_GET['hrid'])) {

            $hrid = $_GET['hrid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM hr WHERE hrid='$hrid'"));

            $tab = $get_contact['tab'];
            $form = $get_contact['form'];
            $heading_number = $get_contact['heading_number'];
            $sub_heading_number = $get_contact['sub_heading_number'];
            $category = $get_contact['category'];
            $heading = $get_contact['heading'];
            $sub_heading = $get_contact['sub_heading'];
            $description = $get_contact['description'];
            $assign_staff = $get_contact['assign_staff'];
            $deadline = $get_contact['deadline'];
			$email_subject = $get_contact['email_subject'];
			$email_message = $get_contact['email_message'];
			$completed_recipient = $get_contact['completed_recipient'];
			$completed_subject = ($get_contact['completed_subject'] == '' ? $completed_subject : $get_contact['completed_subject']);
			$completed_message = ($get_contact['completed_message'] == '' ? $completed_message : $get_contact['completed_message']);
			$approval_subject = $get_contact['approval_subject'];
			$approval_message = $get_contact['approval_message'];
			$rejected_subject = $get_contact['rejected_subject'];
			$rejected_message = $get_contact['rejected_message'];
            $third_heading_number = $get_contact['third_heading_number'];
            $third_heading = $get_contact['third_heading'];
            $form_name = $get_contact['form_name'];
            $permissions_position = $get_contact['permissions_position'];
            $action = $_GET['action'];

            $user_form_id = $get_contact['user_form_id'];

            if ($user_form_id > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_hr WHERE tab='$tab' AND form='$user_form_id'"));
            } else {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_hr WHERE tab='$tab' AND form='$form'"));
            }
            $value_config = ','.$get_field_config['fields'].',';
            $max_section = $get_field_config['max_section'];
            $max_subsection = $get_field_config['max_subsection'];
            $max_thirdsection = $get_field_config['max_thirdsection'];
            $hr_description = $get_field_config['hr_description'];
            $config_extra_fields = explode('**FFM**',$get_field_config['config_extra_fields']);
            $document = $get_field_config['document'];
        ?>

        <input type="hidden" id="hrid" name="hrid" value="<?php echo $hrid ?>" />
        <?php   }
        ?>
        <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
        <input type="hidden" id="max_subsection" name="max_subsection" value="<?php echo $max_subsection ?>" />
        <input type="hidden" id="sub_heading_value" name="sub_heading_value" value="<?php echo $sub_heading_number ?>" />
        <input type="hidden" id="third_heading_value" name="third_heading_value" value="<?php echo $third_heading_number ?>" />
        <?php if ( $sub_heading ) { ?><h2 class="pad-bottom"><?php echo $sub_heading; ?></h2><?php } ?>

        <?php
            $category_hr = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM hr WHERE deleted=0 AND tab='$tab' LIMIT 1"));
            $manual_category = $category_hr['category'];
            if($manual_category == '') {
               $manual_category = 0;
            }
        ?>

        <?php if($action != 'view') { ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Tab:
			</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                  foreach (explode(',', $hr_tabs) as $hr_tab) {
                    echo '<option '.($tab == $hr_tab ? 'selected' : '').' value="'.$hr_tab.'">'.$hr_tab.'</option>';
                  } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired form."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Form:
			</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Form..." id="form_name" name="form_name" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($form == "Employee Information Form") { echo " selected"; } ?> value="Employee Information Form">Employee Information Form</option>
                  <option <?php if ($form == "Employee Driver Information Form") { echo " selected"; } ?> value="Employee Driver Information Form">Employee Driver Information Form</option>
                  <option <?php if ($form == "Time Off Request") { echo " selected"; } ?> value="Time Off Request">Time Off Request</option>
                  <option <?php if ($form == "Confidential Information") { echo " selected"; } ?> value="Confidential Information">Confidential Information</option>

                  <option <?php if ($form == "Work Hours Policy") { echo " selected"; } ?> value="Work Hours Policy">Work Hours Policy</option>
                  <option <?php if ($form == "Direct Deposit Information") { echo " selected"; } ?> value="Direct Deposit Information">Direct Deposit Information</option>
                  <option <?php if ($form == "Employee Substance Abuse Policy") { echo " selected"; } ?> value="Employee Substance Abuse Policy">Employee Substance Abuse Policy</option>
                  <option <?php if ($form == "Employee Right to Refuse Unsafe Work") { echo " selected"; } ?> value="Employee Right to Refuse Unsafe Work">Employee Right to Refuse Unsafe Work</option>
                  <option <?php if ($form == "Shop Yard and Office Orientation") { echo " selected"; } ?> value="Shop Yard and Office Orientation">Shop Yard and Office Orientation</option>
                  <option <?php if ($form == "Copy of Drivers Licence and Safety Tickets") { echo " selected"; } ?> value="Copy of Drivers Licence and Safety Tickets">Copy of Driver's Licence and Safety Tickets</option>
                  <option <?php if ($form == "PPE Requirements") { echo " selected"; } ?> value="PPE Requirements">PPE Requirements</option>
                  <option <?php if ($form == "Verbal Training in Emergency Response Plan") { echo " selected"; } ?> value="Verbal Training in Emergency Response Plan">Verbal Training in Emergency Response Plan</option>
                  <option <?php if ($form == "Eligibility for General Holidays and General Holiday Pay") { echo " selected"; } ?> value="Eligibility for General Holidays and General Holiday Pay">Eligibility for General Holidays and General Holiday Pay</option>
                  <option <?php if ($form == "Maternity Leave and Parental Leave") { echo " selected"; } ?> value="Maternity Leave and Parental Leave">Maternity Leave and Parental Leave</option>
                  <option <?php if ($form == "Employment Verification Letter") { echo " selected"; } ?> value="Employment Verification Letter">Employment Verification Letter</option>
                  <option <?php if ($form == "Background Check Authorization") { echo " selected"; } ?> value="Background Check Authorization">Background Check Authorization</option>
                  <option <?php if ($form == "Disclosure of Outside Clients") { echo " selected"; } ?> value="Disclosure of Outside Clients">Disclosure of Outside Clients</option>
                  <option <?php if ($form == "Employment Agreement") { echo " selected"; } ?> value="Employment Agreement">Employment Agreement</option>
                  <option <?php if ($form == "Independent Contractor Agreement") { echo " selected"; } ?> value="Independent Contractor Agreement">Independent Contractor Agreement</option>
                  <option <?php if ($form == "Letter of Offer") { echo " selected"; } ?> value="Letter of Offer">Letter of Offer</option>
                  <option <?php if ($form == "Employee Non-Disclosure Agreement") { echo " selected"; } ?> value="Employee Non-Disclosure Agreement">Employee Non-Disclosure Agreement</option>
                  <option <?php if ($form == "Employee Self Evaluation") { echo " selected"; } ?> value="Employee Self Evaluation">Employee Self Evaluation</option>
                  <option <?php if ($form == "HR Complaint") { echo " selected"; } ?> value="HR Complaint">HR Complaint</option>
                  <option <?php if ($form == "Exit Interview") { echo " selected"; } ?> value="Exit Interview">Exit Interview</option>
                  <option <?php if ($form == "Employee Expense Reimbursement") { echo " selected"; } ?> value="Employee Expense Reimbursement">Employee Expense Reimbursement</option>
                  <option <?php if ($form == "Absence Report") { echo " selected"; } ?> value="Absence Report">Absence Report</option>
                  <option <?php if ($form == "Employee Accident Report Form") { echo " selected"; } ?> value="Employee Accident Report Form">Employee Accident Report Form</option>
                  <option <?php if ($form == "Trucking Information") { echo " selected"; } ?> value="Trucking Information">Trucking Information</option>
                  <option <?php if ($form == "Contractor Orientation") { echo " selected"; } ?> value="Contractor Orientation">Contractor Orientation</option>
                  <option <?php if ($form == "Contract Welder Inspection Checklist") { echo " selected"; } ?> value="Contract Welder Inspection Checklist">Contract Welder Inspection Checklist</option>
                  <option <?php if ($form == "Contractor Pay Agreement") { echo " selected"; } ?> value="Contractor Pay Agreement">Contractor Pay Agreement</option>
                  <option <?php if ($form == "Employee Holiday Request Form") { echo " selected"; } ?> value="Employee Holiday Request Form">Employee Holiday Request Form</option>
                  <option <?php if ($form == "Employee Coaching Form") { echo " selected"; } ?> value="Employee Coaching Form">Employee Coaching Form</option>
				  <option <?php if ($form == "2016 Alberta Personal Tax Credits Return") { echo " selected"; } ?> value="2016 Alberta Personal Tax Credits Return">2016 Alberta Personal Tax Credits Return TD1AB</option>
				  <option <?php if ($form == "2016 Personal Tax Credits Return") { echo " selected"; } ?> value="2016 Personal Tax Credits Return">2016 Personal Tax Credits Return TD1</option>
				  <option <?php if ($form == "Driver Abstract Statement of Intent") { echo " selected"; } ?> value="Driver Abstract Statement of Intent">Driver Abstract Statement of Intent</option>
				  <option <?php if ($form == "PERSONAL PROTECTIVE EQUIPMENT POLICY") { echo " selected"; } ?> value="PERSONAL PROTECTIVE EQUIPMENT POLICY">PERSONAL PROTECTIVE EQUIPMENT POLICY</option>
				  <option <?php if ($form == "Employee Personal and Emergency Information") { echo " selected"; } ?> value="Employee Personal and Emergency Information">Employee Personal and Emergency Information</option>
				  <option <?php if ($form == "DRIVER CONSENT FORM") { echo " selected"; } ?> value="DRIVER CONSENT FORM">DRIVER CONSENT FORM</option>
				  <option <?php if ($form == "Policy and Procedure Notice of Understanding and Intent") { echo " selected"; } ?> value="Policy and Procedure Notice of Understanding and Intent">Policy and Procedure Notice of Understanding and Intent</option>
				  <option <?php if ($form == "Employment Agreement Evergreen") { echo " selected"; } ?> value="Employment Agreement Evergreen">Employment Agreement Evergreen</option>
                  <option <?php if ($form == "Police Information Check") { echo " selected"; } ?> value="Police Information Check">Police Information Check</option>
                  <?php
                  $query = mysqli_query ($dbc, "SELECT * FROM `user_forms` WHERE `assigned_tile` = 'hr'");;
                  while ($row = mysqli_fetch_array($query)) { ?>
                    <option <?php if ($user_form_id == $row['form_id']) { echo " selected" ; } ?> value="<?php echo $row['form_id']; ?>"><?php echo $row['name']; ?></option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php } ?>

        <?php if($action == 'view') {
        ?>

            <?php //include ('manual_basic_field.php'); ?>

            <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Detail:</label>
                <div class="col-sm-8">
                    <?php echo html_entity_decode($description); ?>
                    <?php //echo $description; ?>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Document".',') !== FALSE) { ?>
                <?php include ('manual_document_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>
                <?php include ('manual_link_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>
                <?php include ('manual_video_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Comments:</label>
                <div class="col-sm-8">
                  <textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
                </div>
              </div>
            <?php } ?>

            <?php if ((($tab == 'Toolbox' || $tab == 'Tailgate')) && (empty($_GET['formid']))) {
                include ('add_hr_attendance.php');
            }
            if ($user_form_id > 0) {
                include ('user_forms.php');
            } else {
                if ($form == 'Employee Information Form') {
                    include ('employee_information_form/employee_information_form.php');
                }
                if ($form == 'Employee Driver Information Form') {
                    include ('employee_driver_information_form/employee_driver_information_form.php');
                }
                if ($form == 'Time Off Request') {
                    include ('time_off_request/time_off_request.php');
                }
                if ($form == 'Confidential Information') {
                    include ('confidential_information/confidential_information.php');
                }

                if ($form == 'Work Hours Policy') {
                    include ('work_hours_policy/work_hours_policy.php');
                }
                if ($form == 'Direct Deposit Information') {
                    include ('direct_deposit_information/direct_deposit_information.php');
                }
                if ($form == 'Employee Substance Abuse Policy') {
                    include ('employee_substance_abuse_policy/employee_substance_abuse_policy.php');
                }
                if ($form == 'Employee Right to Refuse Unsafe Work') {
                    include ('employee_right_to_refuse_unsafe_work/employee_right_to_refuse_unsafe_work.php');
                }
                if ($form == 'Shop Yard and Office Orientation') {
                    include ('employee_shop_yard_office_orientation/employee_shop_yard_office_orientation.php');
                }
                if ($form == "Copy of Drivers Licence and Safety Tickets") {
                    include ('copy_of_drivers_licence_safety_tickets/copy_of_drivers_licence_safety_tickets.php');
                }
                if ($form == 'PPE Requirements') {
                    include ('ppe_requirements/ppe_requirements.php');
                }
                if ($form == 'Verbal Training in Emergency Response Plan') {
                    include ('verbal_training_in_emergency_response_plan/verbal_training_in_emergency_response_plan.php');
                }
                if ($form == 'Eligibility for General Holidays and General Holiday Pay') {
                    include ('eligibility_for_general_holidays_general_holiday_pay/eligibility_for_general_holidays_general_holiday_pay.php');
                }
                if ($form == 'Maternity Leave and Parental Leave') {
                    include ('maternity_leave_parental_leave/maternity_leave_parental_leave.php');
                }
                if ($form == 'Employment Verification Letter') {
                    include ('employment_verification_letter/employment_verification_letter.php');
                }
                if ($form == 'Background Check Authorization') {
                    include ('background_check_authorization/background_check_authorization.php');
                }
                if ($form == 'Disclosure of Outside Clients') {
                    include ('disclosure_of_outside_clients/disclosure_of_outside_clients.php');
                }
                if ($form == 'Employment Agreement') {
                    include ('employment_agreement/employment_agreement.php');
                }
                if ($form == 'Independent Contractor Agreement') {
                    include ('independent_contractor_agreement/independent_contractor_agreement.php');
                }
                if ($form == 'Letter of Offer') {
                    include ('letter_of_offer/letter_of_offer.php');
                }
                if ($form == 'Employee Non-Disclosure Agreement') {
                    include ('employee_nondisclosure_agreement/employee_nondisclosure_agreement.php');
                }
                if ($form == 'Employee Self Evaluation') {
                    include ('employee_self_evaluation/employee_self_evaluation.php');
                }
                if ($form == 'HR Complaint') {
                    include ('hr_complaint/hr_complaint.php');
                }
                if ($form == 'Exit Interview') {
                    include ('exit_interview/exit_interview.php');
                }
                if ($form == 'Employee Expense Reimbursement') {
                    include ('employee_expense_reimbursement/employee_expense_reimbursement.php');
                }
                if ($form == 'Absence Report') {
                    include ('absence_report/absence_report.php');
                }
                if ($form == 'Employee Accident Report Form') {
                    include ('employee_accident_report_form/employee_accident_report_form.php');
                }
                if ($form == 'Trucking Information') {
                    include ('trucking_information/trucking_information.php');
                }
                if ($form == 'Contractor Orientation') {
                    include ('contractor_orientation/contractor_orientation.php');
                }
                if ($form == 'Contract Welder Inspection Checklist') {
                    include ('contract_welder_inspection_checklist/contract_welder_inspection_checklist.php');
                }
                if ($form == 'Contractor Pay Agreement') {
                    include ('contractor_pay_agreement/contractor_pay_agreement.php');
                }
                if ($form == 'Employee Holiday Request Form') {
                    include ('employee_holiday_request_form/employee_holiday_request_form.php');
                }
                if ($form == 'Employee Coaching Form') {
                    include ('employee_coaching_form/employee_coaching_form.php');
                }
                if ($form == '2016 Alberta Personal Tax Credits Return') {
                    include ('2016_alberta_personal/2016_alberta_personal.php');
                }
                if ($form == '2016 Personal Tax Credits Return') {
                    include ('tax_credit_1/2016_alberta_personal.php');
                }
                if ($form == 'Driver Abstract Statement of Intent') {
                    include ('driver_abstract_statement_of_intent/driver_abstract_statement_of_intent.php');
                }
                if ($form == 'PERSONAL PROTECTIVE EQUIPMENT POLICY') {
                    include ('personal_protective_equipment_policy/personal_protective_equipment_policy.php');
                }
                if ($form == 'DRIVER CONSENT FORM') {
                    include ('driver_consent_form/driver_consent_form.php');
                }
                if ($form == 'Policy and Procedure Notice of Understanding and Intent') {
                    include ('policy_and_procedure_notice_of_understanding_and_intent/policy_and_procedure_notice_of_understanding_and_intent.php');
                }
                if ($form == 'Employee Personal and Emergency Information') {
                    include ('employee_personal_and_emergency_information/employee_personal_and_emergency_information.php');
                }
                if ($form == 'Employment Agreement Evergreen') {
                    include ('employment_agreement_evergreen/employment_agreement.php');
                }
                if ($form == 'Police Information Check') {
                    include ('police_information_check/police_information_check.php');
                }
            }
            ?>

            <div class="form-group">
                <div class="col-sm-6">
                    <a href="<?= $from_url ?>" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<?php //if ($form == 'Field Level Hazard Assessment') { ?>
						<button type="submit" name="field_level_hazard" value="field_level_hazard_submit" class="btn brand-btn btn-lg pull-right">Submit</button>
						<!-- <button type="Submit" name="field_level_hazard" value="field_level_hazard_save" class="btn brand-btn btn-lg pull-right">Save</button> -->
					<?php //} else { ?>
						<!-- <button type="submit" name="view_manual" value="view_manual" class="btn brand-btn btn-lg pull-right">Submit</button> -->
					<?php //} ?>
				</div>
            </div>

        <?php } else { ?>

        <div class="panel-group" id="accordion2">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Headings<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_abi" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php include ('manual_basic_field.php'); ?>
                    </div>
                </div>
            </div>

            <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_content" >Content<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_content" class="panel-collapse collapse">
                    <div class="panel-body">
                          <div class="form-group">
                            <label for="first_name[]" class="col-sm-4 control-label">Detail:</label>
                            <div class="col-sm-8">
                              <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Document".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_docs" >Document Upload<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_docs" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('manual_document_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_link" >Add Link<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_link" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('manual_link_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_video" >Upload Video<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_video" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('manual_video_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php //if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { ?>

            <!--
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_hazard" >Field Level Hazard Assessment<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_hazard" class="panel-collapse collapse">
                    <div class="panel-body">

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Field Level Hazard Assessment:</label>
                            <div class="col-sm-8">
                                <input type="checkbox" <?php if (strpos($form_name, "form_field_level_risk_assessment") !== FALSE) { echo " checked"; } ?> value="form_field_level_risk_assessment" style="height: 20px; width: 20px;" name="form_name[]">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Daily:</label>
                            <div class="col-sm-8">
                                <input type="checkbox" <?php if (strpos($form_name, "daily_fill_up") !== FALSE) { echo " checked"; } ?> value="daily_fill_up" style="height: 20px; width: 20px;" name="form_name[]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <?php //} ?>

            <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >Assign to Staff<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_staff" class="panel-collapse collapse">
                    <div class="panel-body">

                            <div class="form-group clearfix completion_date">
                                <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
                                <div class="col-sm-8">
                                    <select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
                                        <?php
                                        $query = mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 order by first_name");
                                        while($row = mysqli_fetch_array($query)) {
											if ( !empty ( $assign_staff ) ) { ?>
												<option <?php if (strpos(','.$assign_staff.',', ','.$row['contactid'].',') !== FALSE) { echo ' selected="selected"'; } ?> value="<?php echo $row['contactid']; ?>"><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option><?php
											} else { ?>
												<option selected="selected" value="<?= $row['contactid']; ?>"><?= decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']); ?></option><?php
											}
										}
                                        ?>
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Review Deadline".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_deadline" >Review Deadline<span class="glyphicon glyphicon-minus"></span></a>
                    </h4>
                </div>

                <div id="collapse_deadline" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Review Deadline:</label>
                            <div class="col-sm-8">
                                <input name="deadline" type="text" class="datepicker" value="<?php echo $deadline; ?>"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!-- Configure Email -->
            <?php if ( strpos ( $value_config, ',' . "Configure Email" . ',' ) !== FALSE ) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_email">Configure Email<span class="glyphicon glyphicon-minus"></span></a>
                        </h4>
                    </div>

                    <div id="collapse_email" class="panel-collapse collapse">
                        <div class="panel-body">
							<h4>Email on Assignment of Form</h4>
                            <div class="form-group clearfix">
                                <label for="email_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
                                <div class="col-sm-8"><input class="form-control" name="email_subject" type="text" value="<?= $email_subject; ?>"></div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="email_message" class="col-sm-4 control-label text-right">Email Message:</label>
                                <div class="col-sm-8"><textarea name="email_message"><?= html_entity_decode($email_message); ?></textarea></div>
                            </div>
							<h4>Email on Submission</h4>
                            <div class="form-group clearfix">
                                <label for="completed_recipient" class="col-sm-4 control-label text-right">Email Recipient:</label>
                                <div class="col-sm-8">
									<select class="chosen-select-deselect form-control" name="completed_recipient"><option></option>
										<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != ''")) as $contact) { ?>
											<option <?= $contact['contactid'] == $completed_recipient ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'].' ('.$contact['email_address'].')' ?></option>
										<?php } ?>
									</select>
								</div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="completed_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
                                <div class="col-sm-8"><input class="form-control" name="completed_subject" type="text" value="<?= $completed_subject; ?>"></div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="completed_message" class="col-sm-4 control-label text-right">Email Message:</label>
                                <div class="col-sm-8"><textarea name="completed_message"><?= html_entity_decode($completed_message); ?></textarea></div>
                            </div>
							<h4>Email on Approval</h4>
                            <div class="form-group clearfix">
                                <label for="approval_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
                                <div class="col-sm-8"><input class="form-control" name="approval_subject" type="text" value="<?= $approval_subject; ?>"></div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="approval_message" class="col-sm-4 control-label text-right">Email Message:</label>
                                <div class="col-sm-8"><textarea name="approval_message"><?= html_entity_decode($approval_message); ?></textarea></div>
                            </div>
							<h4>Email on Rejection</h4>
                            <div class="form-group clearfix">
                                <label for="rejected_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
                                <div class="col-sm-8"><input class="form-control" name="rejected_subject" type="text" value="<?= $rejected_subject; ?>"></div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="rejected_message" class="col-sm-4 control-label text-right">Email Message:</label>
                                <div class="col-sm-8"><textarea name="rejected_message"><?= html_entity_decode($rejected_message); ?></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- Permissions by Position -->
            <?php if ( strpos ( $value_config, ',' . "Permissions by Position" . ',' ) !== FALSE ) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_permissions_pos">Permissions by Position<span class="glyphicon glyphicon-minus"></span></a>
                        </h4>
                    </div>

                    <div id="collapse_permissions_pos" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group clearfix">
                                <label for="permissions_position" class="col-sm-4 control-label text-right">Permissions by Position:</label>
                                <div class="col-sm-8">
                                <select data-placeholder="Choose Positions..." id="permissions_position" name="permissions_position[]" class="chosen-select-deselect form-control" width="380" multiple>
                                    <?php
                                        $query = "SELECT DISTINCT `position` FROM `contacts` WHERE `deleted` = 0";
                                        $result = mysqli_query($dbc, $query);

                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="'.$row['position'].'" '.(strpos(','.$permissions_position.',', ','.$row['position'].',') !== FALSE ? 'selected' : '').'>'.$row['position'].'</option>';
                                        }
                                    ?>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>

            <div class="form-group">
              <div class="col-sm-4">
                  <p><span class="hp-red"><em>Required Fields *</em></span></p>
              </div>
              <div class="col-sm-8"></div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your HR form."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a href="<?= $from_url ?>" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
					<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click Submit to finalize your HR form."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</div>
            </div>
        <?php } ?>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
