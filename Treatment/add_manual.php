<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

mysqli_query($dbc, "UPDATE `field_config_patientform` SET `form`='AB-2 Treatment Plan' WHERE `form`='Treatment Plan'");
if (isset($_POST['add_manual'])) {
    include ('save_manual.php');
}

if((!empty($_GET['patientformid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $patientformid = $_GET['patientformid'];
    $category = get_patientform($dbc, $patientformid, 'category');

    $query = mysqli_query($dbc,"DELETE FROM patientform WHERE patientformid='$patientformid'");
    echo '<script type="text/javascript"> window.location.replace("patientform.php?category='.$category.'"); </script>';
}

if((empty($_GET['patientformid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $uploadid = $_GET['uploadid'];
    $query = mysqli_query($dbc,"DELETE FROM patientform_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    $patientformid = $_GET['patientformid'];
    echo '<script type="text/javascript"> window.location.replace("add_manual.php?patientformid='.$patientformid.'&type='.$type.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $patientformid = $_POST['patientformid'];

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
        if($type == 'patientform') {
            $column = 'manual_patientform_email';
        }

        $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	patientformid='$patientformid'"));

        //Mail
        $to = get_config($dbc, $column);
        $user = $_SESSION['first_name'].' '.$_SESSION['last_name'];
        $subject = 'Manual Read by '.$user;

        $message = "Tab : ".$get_manual['tab'].'<br>';
        $message = "Topic (Sub Tab) : ".$get_manual['category'].'<br>';
        $message .= "Section Heading : ".$get_manual['heading'].'<br>';
        $message .= "Sub Section Heading : ".$get_manual['sub_heading'].'<br>';
        $message .= "Comment<br/><br/>".$_POST['comment'];
        send_email('', $to, '', '', $subject, $message, '');

        //Mail
    }

    $staffid = $_SESSION['contactid'];
    $today_date = date('Y-m-d H:i:s');
    $query_update_ticket = "UPDATE `patientform_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `patientformid` = '$patientformid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?category='.$get_manual['category'].'"); </script>';
}

if (isset($_POST['manual_btn'])) {
    $manual_btn = $_POST['manual_btn'];
    $patientformid = $_POST['patientformid'];

    $form_name_save = get_patientform($dbc, $patientformid, 'form');
    if(isset($_POST['form_id'])) {
        include ('user_forms.php');
    } else {
        if($form_name_save == 'Whiplash Associated Disorders') {
            include ('whiplash_associated_disorders/save_whiplash_associated_disorders.php');
        }
        if($form_name_save == 'WCB Provider Employer Contact') {
            include ('wcb_provider_employer_contact/save_wcb_provider_employer_contact.php');
        }
        if($form_name_save == 'WCB Calendar') {
            include ('wcb_calendar/save_wcb_calendar.php');
        }
        if($form_name_save == 'Spinal Discharge Checklist') {
            include ('spinal_discharge_checklist/save_spinal_discharge_checklist.php');
        }
        if($form_name_save == 'Roland Morris Questionnaire') {
            include ('roland_morris_questionnaire/save_roland_morris_questionnaire.php');
        }
        if($form_name_save == 'Personal Consent Form') {
            include ('personal_consent_form/save_personal_consent_form.php');
        }
        if($form_name_save == 'Neck Disability Questionnaire') {
            include ('neck_disability_questionnaire/save_neck_disability_questionnaire.php');
        }

        if($form_name_save == 'Treatment Notes') {
            include ('treatment_notes/save_treatment_notes.php');
        }
        if($form_name_save == 'Medical History Form') {
            include ('patient_functional_scale/save_patient_functional_scale.php');
        }
        if($form_name_save == 'Massage Treatment Notes') {
            include ('massage_treatment_notes/save_massage_treatment_notes.php');
        }
        if($form_name_save == 'Prescribed Treatment Schedule') {
            include ('prescribed_treatment_schedule/save_prescribed_treatment_schedule.php');
        }

        if($form_name_save == 'AB-1 Initial Claim Form') {
            include ('wcb_ab_forms/save_ab1_initial_claim.php');
        }
        if($form_name_save == 'AB-2 Treatment Plan') {
            include ('treatment_plan/save_treatment_plan.php');
        }
        if($form_name_save == 'AB-3 Progress Report') {
            include ('wcb_ab_forms/save_ab3_progress_report.php');
        }
        if($form_name_save == 'AB-4 Concluding Report') {
            include ('wcb_ab_forms/save_ab4_concluding_report.php');
        }

        if($form_name_save == 'Concluding Report') {
            include ('concluding_report/save_concluding_report.php');
        }
        if($form_name_save == 'Progress Report') {
            include ('progress_report/save_progress_report.php');
        }
        if($form_name_save == 'C040 Employer Report of Injury') {
            include ('injury_report/save_c040_injury_report.php');
        }
    }

    if (!empty($_POST['attach_contact'])) {
        $patientid = $_POST['attach_contact'];
    }
	$injuryid = $_POST['attach_to_injury'];
    $filled_date = $_POST['filled_date'];
    $filled_by_staff = $_POST['filled_by_staff'];
    $ticketid = $_POST['ticketid'];
	$form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`infopdfid`) FROM `patientform_pdf` WHERE `patientid`='$patientid'"))['infopdfid'];
    if (!empty($infopdfid)) {
        $form_id = $infopdfid;
    }
	mysqli_query($dbc, "UPDATE `patientform_pdf` SET `patientid` = '$patientid', `injuryid`='$injuryid', `filled_date` = '$filled_date', `staffid` = '$filled_by_staff', `ticketid` = '$ticketid' WHERE `infopdfid`='$form_id'");
}
?>
<script type="text/javascript">
$(document).ready(function() {

	$("#tab_field").change(function() {
        window.location = 'add_manual.php?type=patientform&tab='+this.value;
	});

	$("#form_name").change(function() {
        var tab = $("#tab_field").val();
        window.location = 'add_manual.php?type=patientform&tab='+tab+'&form='+this.value;
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
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('treatment_charts');
if(!empty($_GET['patientformid'])) {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM patientform WHERE patientformid='".$_GET['patientformid']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
}
?>
<div class="container" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>
  <div class="row">

    <?php if($user_form_layout == 'Sidebar') { ?>
        <h1 style="margin-top: 0; padding-top: 0;"><a href="<?= $_GET['from_url'] != '' ? urldecode($_GET['from_url']) : 'index.php?tab='.$tab_name.'&subtab='.$category ?>">Treatment Charts</a></h1>
    <?php } ?>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : '' ?>>

    <?php
		$tab_name = '';
        $category = '';
        $heading = '';
        $sub_heading = '';
        $description = '';
        $assign_staff = '';
        $deadline = '';
        $action = '';
        $heading_number = '';
        $sub_heading_number = '';
        $third_heading_number = '';
        $third_heading = '';
        $form_name = '';

        $form = $_GET['form'];
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_patientform WHERE form='$form'"));
		if(!empty($get_field_config['fields'])) {
			$value_config = ','.$get_field_config['fields'].',';
		} else {
			$value_config = ',,';
		}
        $max_section = $get_field_config['max_section'];
        $max_subsection = $get_field_config['max_subsection'];
        $max_thirdsection = $get_field_config['max_thirdsection'];
        $user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `form_id` FROM `user_forms` WHERE `form_id` = '$form'"))['form_id'];
        $user_form = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['name'];

        if(!empty($_GET['patientformid'])) {

            $patientformid = $_GET['patientformid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM patientform WHERE patientformid='$patientformid'"));

            $form = $get_contact['form'];
            $heading_number = $get_contact['heading_number'];
            $sub_heading_number = $get_contact['sub_heading_number'];
            $tab_name = $get_contact['tab'];
            $category = $get_contact['category'];
            $heading = $get_contact['heading'];
            $sub_heading = $get_contact['sub_heading'];
            $description = $get_contact['description'];
            $assign_staff = $get_contact['assign_staff'];
            $deadline = $get_contact['deadline'];
            $third_heading_number = $get_contact['third_heading_number'];
            $third_heading = $get_contact['third_heading'];
            $form_name = $get_contact['form_name'];
            $action = $_GET['action'];

            $user_form_id = $get_contact['user_form_id'];
            $user_form = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['name'];

            if ($user_form_id > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_patientform WHERE form='$user_form_id'"));
            } else {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_patientform WHERE form='$form'"));
            }

			if(!empty($get_field_config['fields'])) {
				$value_config = ','.$get_field_config['fields'].',';
				$max_section = $get_field_config['max_section'];
				$max_subsection = $get_field_config['max_subsection'];
				$max_thirdsection = $get_field_config['max_thirdsection'];
                $attach_contact_type = $get_field_config['attach_contact_type'];
			} else {
				$value_config = ',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,';
				$max_section = 10;
				$max_subsection = 10;
				$max_thirdsection = 10;
			}

        ?>
        <input type="hidden" id="patientformid" name="patientformid" value="<?php echo $patientformid ?>" />
        <?php   }
        if(!empty($_GET['ticketid'])) {
            $ticketid = $_GET['ticketid'];
        }
        ?>
        <?php if($user_form_layout != 'Sidebar') { ?>
    		<h1 class="triple-pad-bottom"><?php echo (!empty($user_form) ? $user_form : (empty($form) ? 'Treatment' : $form)); ?></h1>
        <?php } ?>
        <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />

        <?php if($action != 'view') { ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">Form:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Form..." id="form_name" name="form_name" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($form == "Whiplash Associated Disorders") { echo " selected"; } ?> value="Whiplash Associated Disorders">Whiplash Associated Disorders</option>
                  <option <?php if ($form == "WCB Provider Employer Contact") { echo " selected"; } ?> value="WCB Provider Employer Contact">WCB Provider Employer Contact</option>
                  <option <?php if ($form == "WCB Calendar") { echo " selected"; } ?> value="WCB Calendar">WCB Calendar</option>
                  <option <?php if ($form == "Spinal Discharge Checklist") { echo " selected"; } ?> value="Spinal Discharge Checklist">Spinal Discharge Checklist</option>
                  <option <?php if ($form == "Roland Morris Questionnaire") { echo " selected"; } ?> value="Roland Morris Questionnaire">Roland Morris Questionnaire</option>
                  <option <?php if ($form == "Personal Consent Form") { echo " selected"; } ?> value="Personal Consent Form">Personal Consent Form</option>
                  <option <?php if ($form == "Neck Disability Questionnaire") { echo " selected"; } ?> value="Neck Disability Questionnaire">Neck Disability Questionnaire</option>
                  <option <?php if ($form == "Treatment Notes") { echo " selected"; } ?> value="Treatment Notes">Treatment Notes</option>
                  <option <?php if ($form == "Medical History Form") { echo " selected"; } ?> value="Medical History Form">Medical History Form</option>
                  <option <?php if ($form == "Massage Treatment Notes") { echo " selected"; } ?> value="Massage Treatment Notes">Massage Treatment Notes</option>
                  <option <?php if ($form == "Prescribed Treatment Schedule") { echo " selected"; } ?> value="Prescribed Treatment Schedule">Prescribed Treatment Schedule</option>
                  <option <?php if ($form == "AB-1 Initial Claim Form") { echo " selected"; } ?> value="AB-1 Initial Claim Form">AB-1 Initial Claim Form</option>
                  <option <?php if ($form == "AB-2 Treatment Plan") { echo " selected"; } ?> value="AB-2 Treatment Plan">AB-2 Treatment Plan</option>
                  <option <?php if ($form == "AB-3 Progress Report") { echo " selected"; } ?> value="AB-3 Progress Report">AB-3 Progress Report</option>
                  <option <?php if ($form == "AB-4 Concluding Report") { echo " selected"; } ?> value="AB-4 Concluding Report">AB-4 Concluding Report</option>
                  <option <?php if ($form == "Concluding Report") { echo " selected"; } ?> value="Concluding Report">Concluding Report</option>
                  <option <?php if ($form == "Progress Report") { echo " selected"; } ?> value="Progress Report">Progress Report</option>
                  <option <?php if ($form == "Body Targeted Assessment") { echo " selected"; } ?> value="Body Targeted Assessment">Body Targeted Assessment</option>
                  <option <?php if ($form == "General Assessment") { echo " selected"; } ?> value="General Assessment">General Assessment</option>
                  <option <?php if ($form == "General Treatment") { echo " selected"; } ?> value="General Treatment">General Treatment</option>
                  <option <?php if ($form == "General Treatment Plan") { echo " selected"; } ?> value="General Treatment Plan">General Treatment Plan</option>
                  <option <?php if ($form == "General Discharge") { echo " selected"; } ?> value="General Discharge">General Discharge</option>
                  <option <?php if ($form == "C040 Employer Report of Injury") { echo " selected"; } ?> value="C040 Employer Report of Injury">C040 Employer Report of Injury</option>
                  <?php
                  $query = mysqli_query ($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,treatment,%'");;
                  while ($row = mysqli_fetch_array($query)) { ?>
                    <option <?php if ($user_form_id == $row['form_id']) { echo " selected" ; } ?> value="<?php echo $row['form_id']; ?>"><?php echo $row['name']; ?></option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php } ?>

        <?php if($action == 'view') {
        ?>

            <?php if($user_form_layout == 'Sidebar') {
                $global_form = $form;
                include('user_forms_sidebar.php');
                $form = $global_form;
                include ('manual_basic_field.php');
                echo '<hr>';
            } else {
                include ('manual_basic_field.php');
            } ?>

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

            <?php if (strpos($value_config, ','."Signature box".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Signature:</label>
                <div class="col-sm-8">
                  <?php include ('../phpsign/sign.php'); ?>
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Hide Injury".',') === FALSE) { ?>
                <div class="form-group">
                    <label for="fax_number" class="col-sm-4 control-label">Attach this chart to injury:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select an Injury..." name="attach_to_injury" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php $projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
						  $patient_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name` FROM `contacts` LEFT JOIN `project` ON `projectid`='$projectid' WHERE `contacts`.`contactid` IN (SELECT `contactid` FROM `patient_injury` WHERE `deleted`=0) AND `contacts`.`deleted`=0 AND `contacts`.`status`>0 AND (CONCAT(',',`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') OR '$projectid'='')"),MYSQLI_ASSOC));
                          foreach($patient_list as $patient_row_id) {
                              $injury_list = mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE `contactid`='$patient_row_id' ORDER BY `injury_date` DESC");
                              while($injury_row = mysqli_fetch_array($injury_list)) {
                                  echo "<option ".($injury_row == $_GET['injuryid'] ? 'selected' : '')." data-patient='".$injury_row['injuryid']."' value='".$injury_row['injuryid']."'>".$injury_row['injury_name']." - ".$injury_row['injury_date']." (".get_contact($dbc, $patient_row_id, 'category').": ".get_contact($dbc, $patient_row_id).")</option>";
                              }
                          } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>

            <?php if ($user_form_id > 0) {
                include ('user_forms.php');
            } else { ?>
                <?php if ($form == 'Whiplash Associated Disorders') { ?>
                    <?php include ('whiplash_associated_disorders/whiplash_associated_disorders.php'); ?>
                <?php } ?>
                <?php if ($form == 'Body Targeted Assessment') { ?>
                    <?php include ('assessments/body_targeted_assessment.php'); ?>
                <?php } ?>

                <?php if ($form == 'WCB Provider Employer Contact') { ?>
                    <?php include ('wcb_provider_employer_contact/wcb_provider_employer_contact.php'); ?>
                <?php } ?>
                <?php if ($form == 'WCB Calendar') { ?>
                    <?php include ('wcb_calendar/wcb_calendar.php'); ?>
                <?php } ?>
                <?php if ($form == 'Spinal Discharge Checklist') { ?>
                    <?php include ('spinal_discharge_checklist/spinal_discharge_checklist.php'); ?>
                <?php } ?>
                <?php if ($form == 'Roland Morris Questionnaire') { ?>
                    <?php include ('roland_morris_questionnaire/roland_morris_questionnaire.php'); ?>
                <?php } ?>
                <?php if ($form == 'Personal Consent Form') { ?>
                    <?php include ('personal_consent_form/personal_consent_form.php'); ?>
                <?php } ?>
                <?php if ($form == 'Neck Disability Questionnaire') { ?>
                    <?php include ('neck_disability_questionnaire/neck_disability_questionnaire.php'); ?>
                <?php } ?>

                <?php if ($form == 'Treatment Notes') { ?>
                    <?php include ('treatment_notes/treatment_notes.php'); ?>
                <?php } ?>
                <?php if ($form == 'Medical History Form') { ?>
                    <?php include ('patient_functional_scale/patient_functional_scale.php'); ?>
                <?php } ?>
                <?php if ($form == 'Massage Treatment Notes') { ?>
                    <?php include ('massage_treatment_notes/massage_treatment_notes.php'); ?>
                <?php } ?>
                <?php if ($form == 'Prescribed Treatment Schedule') { ?>
                    <?php include ('prescribed_treatment_schedule/prescribed_treatment_schedule.php'); ?>
                <?php } ?>
                <?php if ($form == 'AB-1 Initial Claim Form') { ?>
                    <?php include ('wcb_ab_forms/ab1_initial_claim.php'); ?>
                <?php } ?>
                <?php if ($form == 'AB-2 Treatment Plan') { ?>
                    <?php include ('treatment_plan/treatment_plan.php'); ?>
                <?php } ?>
                <?php if ($form == 'AB-3 Progress Report') { ?>
                    <?php include ('wcb_ab_forms/ab3_progress_report.php'); ?>
                <?php } ?>
                <?php if ($form == 'AB-4 Concluding Report') { ?>
                    <?php include ('wcb_ab_forms/ab4_concluding_report.php'); ?>
                <?php } ?>
                <?php if ($form == 'Concluding Report') { ?>
                    <?php include ('concluding_report/concluding_report.php'); ?>
                <?php } ?>
                <?php if ($form == 'Progress Report') { ?>
                    <?php include ('progress_report/progress_report.php'); ?>
                <?php } ?>
                <?php if ($form == 'General Assessment') { ?>
                    <?php include ('assessments/general_assessment.php'); ?>
                <?php } ?>
                <?php if ($form == 'General Treatment') { ?>
                    <?php include ('general_treatment.php'); ?>
                <?php } ?>
                <?php if ($form == 'General Treatment Plan') { ?>
                    <?php include ('treatment_plan/general_plan.php'); ?>
                <?php } ?>
                <?php if ($form == 'General Discharge') { ?>
                    <?php include ('general_discharge.php'); ?>
                <?php } ?>
                <?php if ($form == 'C040 Employer Report of Injury') { ?>
                    <?php include ('injury_report/c040_injury_report.php'); ?>
                <?php } ?>
            <?php } ?>

            <div class="form-group">
                <div class="col-sm-4 clearfix">
                    <a href="<?= $_GET['from_url'] != '' ? urldecode($_GET['from_url']) : 'index.php?tab='.$tab_name.'&subtab='.$category ?>" class="btn brand-btn pull-right">Back</a>
                </div>
              <div class="col-sm-8">
                <?php //if ($form == 'Field Level Hazard Assessment') { ?>
                    <button type="submit" name="manual_btn" value="submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                    <!-- <button type="submit" name="manual_btn" value="save" class="btn brand-btn btn-lg pull-right">Save</button> -->
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
                                    <select name="assign_staff[]" data-placeholder="Choose a Staff..." class="chosen-select-deselect form-control" multiple width="380">
										  <?php
											$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
											foreach($query as $id) {
												$selected = '';
												$selected = strpos(','.$assign_staff.',', ','.$row['contactid'].',') ? 'selected = "selected"' : '';
												echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
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

        </div>

            <div class="form-group">
              <div class="col-sm-4">
                  <p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
              </div>
              <div class="col-sm-8"></div>
            </div>

            <div class="form-group">
                <div class="col-sm-4 clearfix">
                    <a href="<?= $_GET['from_url'] != '' ? urldecode($_GET['from_url']) : 'index.php?tab='.$tab_name.'&subtab='.$category ?>" class="btn brand-btn pull-right">Back</a>
                </div>
              <div class="col-sm-8">
                <button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
              </div>
            </div>
        <?php } ?>
        <?php if($user_form_layout == 'Sidebar') { ?>
                </div>
            </div>
        <?php } ?>

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
