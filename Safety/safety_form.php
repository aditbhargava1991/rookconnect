<?php
/*
Add Vendor
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
include('../Safety/field_list.php');

if (isset($_POST['add_manual'])) {

    $safety_main_site_tabs = filter_var($_POST['safety_main_site_tabs'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='safety_main_site_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$safety_main_site_tabs' WHERE name='safety_main_site_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('safety_main_site_tabs', '$safety_main_site_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $pdf_header = filter_var(htmlentities($_POST['pdf_header']),FILTER_SANITIZE_STRING);
    $pdf_footer = filter_var(htmlentities($_POST['pdf_footer']),FILTER_SANITIZE_STRING);

    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $form_name = filter_var($_POST['form_name'],FILTER_SANITIZE_STRING);
    $fields = implode(',',$_POST['fields']);

    $max_section = filter_var($_POST['max_section'],FILTER_SANITIZE_STRING);
    $max_subsection = filter_var($_POST['max_subsection'],FILTER_SANITIZE_STRING);
    $max_thirdsection = filter_var($_POST['max_thirdsection'],FILTER_SANITIZE_STRING);

    $pdf_logo = htmlspecialchars($_FILES["pdf_logo"]["name"], ENT_QUOTES);

    if (strpos(','.$fields.',', ','.'Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading'.',') === false) {
        $fields = $fields.',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigsafetyid) AS fieldconfigsafetyid FROM field_config_safety WHERE tab='$tab_field' AND form='$form_name'"));
    if($get_field_config['fieldconfigsafetyid'] > 0) {
		if($pdf_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $pdf_logo;
		}
		move_uploaded_file($_FILES["pdf_logo"]["tmp_name"],"download/" . $logo_update);

        $query_update_employee = "UPDATE `field_config_safety` SET `pdf_header` = '$pdf_header', `pdf_footer` = '$pdf_footer', `fields` = '$fields', max_section = '$max_section', max_subsection = '$max_subsection', max_thirdsection = '$max_thirdsection', pdf_logo = '$logo_update' WHERE `tab`='$tab_field' AND `form`='$form_name'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["pdf_logo"]["tmp_name"], "download/" . $_FILES["pdf_logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `field_config_safety` (`pdf_header`, `pdf_footer`, `tab`, `form`, `fields`, `max_section`, `max_subsection`, `max_thirdsection`, `pdf_logo`) VALUES ('$pdf_header', '$pdf_footer', '$tab_field', '$form_name', '$fields', '$max_section', '$max_subsection', '$max_thirdsection', '$pdf_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    include ('save_form.php');
}

if((!empty($_GET['safetyid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $safetyid = $_GET['safetyid'];
    $category = get_safety($dbc, $safetyid, 'category');
    $tab = get_safety($dbc, $safetyid, 'tab');

    $query = mysqli_query($dbc,"DELETE FROM safety WHERE safetyid='$safetyid'");
    echo '<script type="text/javascript"> window.location.replace("index.php?tab='.$tab.'&category='.$category.'"); </script>';
}

if((empty($_GET['safetyid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $uploadid = $_GET['uploadid'];
    $query = mysqli_query($dbc,"DELETE FROM safety_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    $safetyid = $_GET['safetyid'];
    echo '<script type="text/javascript"> window.location.replace("index.php?safetyid='.$safetyid.'&type='.$type.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $safetyid = $_POST['safetyid'];

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
        if($type == 'safety') {
            $column = 'manual_safety_email';
        }

        $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	safetyid='$safetyid'"));

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
	$query_insert_row = "INSERT INTO `safety_staff` (`safetyid`, `staffid`) SELECT '$safetyid', '$staffid' FROM (SELECT COUNT(*) rows FROM `safety_staff` WHERE `safetyid`='$safetyid' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
	mysqli_query($dbc, $query_insert_row);

  $before_change = '';
  $history = "Safety staff entry has been added. <br />";
  add_update_history($dbc, 'safety_history', $history, '', $before_change);

    $query_update_ticket = "UPDATE `safety_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `safetyid` = '$safetyid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

	$return_url = 'index.php?tab='.($get_manual['tab'] == 'manual' ? 'manuals' : $get_manual['tab']);
	if(!empty($_GET['return_url'])) {
		$return_url = urldecode($_GET['return_url']);
	}
    echo '<script type="text/javascript"> window.location.replace("'.$return_url.'"); </script>';
}

if (isset($_POST['field_level_hazard'])) {

    $field_level_hazard = $_POST['field_level_hazard'];
    $safetyid = $_POST['safetyid'];

    $form_name_save = get_safety($dbc, $safetyid, 'form');

    if($form_name_save == 'Field Level Hazard Assessment') {
        include ('field_level_hazard_assessment/save_field_level_hazard_assessment.php');
    }
    if($form_name_save == 'Hydrera Site Specific Pre Job Safety Meeting Hazard Assessment') {
        include ('hydrera_site_specificpre_job/save_hydrera_site_specificpre_job.php');
    }

    if($form_name_save == 'Weekly Safety Meeting') {
        include ('weekly_safety_meeting/save_weekly_safety_meeting.php');
    }

    if($form_name_save == 'Tailgate Safety Meeting') {
        include ('tailgate_safety_meeting/save_tailgate_safety_meeting.php');
    }

    if($form_name_save == 'Toolbox Safety Meeting') {
        include ('toolbox_safety_meeting/save_toolbox_safety_meeting.php');
    }

    if($form_name_save == 'Daily Equipment Inspection Checklist') {
        include ('daily_equipment_inspection_checklist/save_daily_equipment_inspection_checklist.php');
    }

    if($form_name_save == 'AVS Hazard Identification') {
        include ('avs_hazard_identification/save_avs_hazard_identification.php');
    }

    if($form_name_save == 'AVS Near Miss') {
        include ('near_miss_report/save_near_miss_report.php');
    }

    if($form_name_save == 'Incident Investigation Report') {
        include ('incident_investigation_report/save_incident_investigation_report.php');
    }
    if($form_name_save == 'Follow Up Incident Report') {
        include ('follow_up_incident_report/save_follow_up_incident_report.php');
    }
    if($form_name_save == 'Pre Job Hazard Assessment') {
        include ('pre_job_hazard_assessment/save_pre_job_hazard_assessment.php');
    }
    if($form_name_save == 'Monthly Site Safety Inspections') {
        include ('monthly_site_safety_inspection/save_monthly_site_safety_inspection.php');
    }
    if($form_name_save == 'Monthly Office Safety Inspections') {
        include ('monthly_office_safety_inspection/save_monthly_office_safety_inspection.php');
    }
    if($form_name_save == 'Monthly Health and Safety Summary') {
        include ('monthly_health_and_safety_summary/save_monthly_health_and_safety_summary.php');
    }
    if($form_name_save == 'Trailer Inspection Checklist') {
        include ('trailer_inspection_checklist/save_trailer_inspection_checklist.php');
    }
    if($form_name_save == 'Employee Misconduct Form') {
        include ('employee_misconduct_form/save_employee_misconduct_form.php');
    }
    if($form_name_save == 'Site Inspection Hazard Assessment') {
        include ('site_inspection_hazard_assessment/save_site_inspection_hazard_assessment.php');
    }

    if($form_name_save == 'Weekly Planned Inspection Checklist') {
        include ('weekly_planned_inspection_checklist/save_weekly_planned_inspection_checklist.php');
    }
    if($form_name_save == 'Equipment Inspection Checklist') {
        include ('equipment_inspection_checklist/save_equipment_inspection_checklist.php');
    }
    if($form_name_save == 'Employee Equipment Training Record') {
        include ('employee_equipment_training_record/save_employee_equipment_training_record.php');
    }
    if($form_name_save == 'Vehicle Inspection Checklist') {
        include ('vehicle_inspection_checklist/save_vehicle_inspection_checklist.php');
    }
    if($form_name_save == 'Safety Meeting Minutes') {
        include ('safety_meeting_minutes/save_safety_meeting_minutes.php');
    }
    if($form_name_save == 'Vehicle Damage Report') {
        include ('vehicle_damage_report/save_vehicle_damage_report.php');
    }
    if($form_name_save == 'Fall Protection Plan') {
        include ('fall_protection_plan/save_fall_protection_plan.php');
    }
    if($form_name_save == 'Spill Incident Report') {
        include ('spill_incident_report/save_spill_incident_report.php');
    }
    if($form_name_save == 'General Site Safety Inspection') {
        include ('general_site_safety_inspection/save_general_site_safety_inspection.php');
    }
    if($form_name_save == 'Confined Space Entry Permit') {
        include ('confined_space_entry_permit/save_confined_space_entry_permit.php');
    }
    if($form_name_save == 'Lanyards Inspection Checklist Log') {
        include ('lanyards_inspection_checklist_log/save_lanyards_inspection_checklist_log.php');
    }
    if($form_name_save == 'On The Job Training Record') {
        include ('on_the_job_training_record/save_on_the_job_training_record.php');
    }
    if($form_name_save == 'General Office Safety Inspection') {
        include ('general_office_safety_inspection/save_general_office_safety_inspection.php');
    }
    if($form_name_save == 'Full Body Harness Inspection Checklist Log') {
        include ('full_body_harness_inspection_checklist_log/save_full_body_harness_inspection_checklist_log.php');
    }
    if($form_name_save == 'Confined Space Pre Entry Checklist') {
        include ('confined_space_entry_pre_entry_checklist/save_confined_space_entry_pre_entry_checklist.php');
    }
    if($form_name_save == 'Confined Space Entry Log') {
        include ('confined_space_entry_log/save_confined_space_entry_log.php');
    }
    if($form_name_save == 'Emergency Response Transportation Plan') {
        include ('emergency_response_transportation_plan/save_emergency_response_transportation_plan.php');
    }

    if($form_name_save == 'Hazard Id Report') {
        include ('hazard_id_report/save_hazard_id_report.php');
    }
    if($form_name_save == 'Dangerous Goods Shipping Document') {
        include ('dangerous_goods_shipping_document/save_dangerous_goods_shipping_document.php');
    }
    if($form_name_save == 'Safe Work Permit') {
        include ('safe_work_permit/save_safe_work_permit.php');
    }
    if($form_name_save == 'Journey Management - Trip Tracking Form') {
        include ('journey_management_trip_tracking/save_journey_management_trip_tracking.php');
    }
    if($form_name_save == 'Motor Vehicle Accident Form') {
        include ('incident_reports/save_incident_reports.php');
    }

    $safety_db = array_search($form_name_save,$safety_table_list);
    if(!empty($safety_db)) {
        $safety_projectid = $_POST['safety_projectid'];
        $safety_siteid = $_POST['safety_siteid'];
        $safety_ticketid = $_POST['safety_ticketid'];
        $safety_clientid = $_POST['safety_clientid'];
        if($form_name_save == 'Motor Vehicle Accident Form') {
            mysqli_query($dbc, "UPDATE `$safety_db` SET `safety_projectid` = '$safety_projectid', `safety_siteid` = '$safety_siteid', `safety_ticketid` = '$safety_ticketid', `safety_clientid` = '$safety_clientid' WHERE `incidentreportid` = '$incidentreportid'");
        } else {
            mysqli_query($dbc, "UPDATE `$safety_db` SET `safety_projectid` = '$safety_projectid', `safety_siteid` = '$safety_siteid', `safety_ticketid` = '$safety_ticketid', `safety_clientid` = '$safety_clientid' WHERE `fieldlevelriskid` = '$fieldlevelriskid'");
        }
    }

    if($form_name_save == 'Manual') {
        $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

        $safetyid = $_POST['safetyid'];

        $type = $_POST['type'];

        $signature = sigJsonToImage($_POST['output']);
        imagepng($signature, 'download/sign_'.$safetyid.'_'.$_SESSION['contactid'].'.png');

        include_once('manual_pdf.php');

        if($comment != '') {
            $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	safetyid='$safetyid'"));

            //Mail

            $to = get_config($dbc, 'safety_manual_completed_email');
            if(!empty($to)) {
                $manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading`, `heading_number`, `sub_heading`, `sub_heading_number`, `third_heading`, `third_heading_number` FROM `manuals` WHERE safetyid='$safetyid'"));
                $heading = $manual['third_heading'] != '' ? $manual['third_heading_number'].' '.$manual['third_heading'] : ($manual['sub_heading'] != '' ? $manual['sub_heading_number'].' '.$manual['sub_heading'] : $manual['heading_number'].' '.$manual['heading']);
                $subject = get_config($dbc, 'safety_manual_subject_completed');
                $body = html_entity_decode(str_replace(['[COMMENT]'],[($comment == '' ? '' : 'Comment: '.$comment)],get_config($dbc, 'safety_manual_body_completed')));
                try {
                    send_email('', $to, '', '', $subject, $body, $pdf_path);
                } catch(Exception $e) { }
            }
            //Mail
        }

        $staffid = $_SESSION['contactid'];
        $today_date = date('Y-m-d H:i:s');
        // Insert a row if it isn't already there
        $query_insert_row = "INSERT INTO `safety_staff` (`safetyid`, `staffid`) SELECT '$safetyid', '$staffid' FROM (SELECT COUNT(*) rows FROM `safety_staff` WHERE `safetyid`='$safetyid' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
        mysqli_query($dbc, $query_insert_row);

        $before_change = '';
        $history = "Safety staff entry has been added. <br />";
        add_update_history($dbc, 'safety_history', $history, '', $before_change);
        
        $query_update_ticket = "UPDATE `safety_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `safetyid` = '$safetyid' AND staffid='$staffid' AND done=0";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $return_url = 'index.php?tab=manuals';
        if(!empty($_GET['return_url'])) {
            $return_url = urldecode($_GET['return_url']);
        }
        echo '<script type="text/javascript"> window.top.location.reload(); window.location.replace("'.$return_url.'"); </script>';

    }

}
?>
<script type="text/javascript">
$(document).ready(function() {

	$("#tab_field").change(function() {
        window.location = '?safetyid=<?= $_GET['safetyid'] ?>&action=edit&tab='+this.value+'&form='+$('#form_name').val();
	});

	$("#form_name").change(function() {
        var tab = $("#tab_field").val();
        window.location = '?safetyid=<?= $_GET['safetyid'] ?>&action=edit&tab='+tab+'&form='+this.value;
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

    $(".selecctall").change(function(){
      $(".field_config input:checkbox").prop('checked', $(this).prop("checked"));
    });
});
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
			data: { heading_number: heading_number, sub_heading: current_sub, category: $('#tab_field').val(), max_section: max_sub },
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
		var current_sub = $('#third_heading_number').val();
		var max_sub = $('#max_subsection').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "manual_ajax_all.php?fill=third_heading_number",
			data: { sub_heading_number: sub_heading_number, third_heading: current_sub, category: $('#tab_field').val(), max_section: max_sub },
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
<script src="<?php echo WEBSITE_URL; ?>/js/jquery.simplecolorpicker.js"></script>
			<link rel="stylesheet" href="<?php echo WEBSITE_URL; ?>/css/jquery.simplecolorpicker.css">
<?php
checkAuthorised('safety');
if(!empty($_GET['safetyid']) && $_GET['action'] != 'edit') {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety WHERE safetyid='".$_GET['safetyid']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
}
?>
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="main-screen form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : 'style="background-color:white;padding:0;"' ?>>
    <?php
        $category = '';
        $heading = '';
        $sub_heading = '';
        $description = '';
        $assign_staff = '';
        $assign_sites = '';
        $assign_work_orders = '';
        $deadline = '';
        $action = '';
        $marker = '';
        $heading_number = '';
        $sub_heading_number = '';
        $third_heading_number = '';
        $third_heading = '';
        $form_name = '';

        $tab = $_GET['tab'];
        $form = $_GET['form'];
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$form'"));
        $pdf_logo = $get_field_config['pdf_logo'];
        $pdf_header = $get_field_config['pdf_header'];
        $pdf_footer = $get_field_config['pdf_footer'];
        $value_config = empty($get_field_config['fields']) ? ',Staff,Configure Email,Topic (Sub Tab),field1,field2,field3,field4,field5,field6,field7,field8,field9,field10,field11,field12,field13,field14,field15,field16,field17,field18,field19,field20,field21,field22,field23,field24,field25,field26,field27,field28,field29,field30,field31,field32,field33,field34,field35,field36,field37,field38,field39,field40,field41,field42,field43,field44,field45,field46,field47,field48,field49,field50,field51,field52,field53,field54,field55,field56,field57,field58,field59,field60,field61,field62,field63,field64,field65,field66,field67,field68,field69,field70,Section #,Section Heading,Sub Section #,Sub Section Heading,' : ','.$get_field_config['fields'].',';
        $sections = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`heading_number`) + 5 `headings`, MAX(CONVERT(REPLACE(`sub_heading_number`,CONCAT(`heading_number`,'.'),''),UNSIGNED INTEGER)) + 5 `sub_headings`, MAX(CONVERT(REPLACE(`third_heading_number`,CONCAT(`sub_heading_number`,'.'),''),UNSIGNED INTEGER)) + 5 `third_headings` FROM `safety`"));
        $max_section = ($sections['headings'] > 0 ? $sections['headings'] : 20);
        $max_subsection = ($sections['sub_headings'] > 0 ? $sections['sub_headings'] : 20);
        $max_thirdsection = ($sections['third_headings'] > 0 ? $sections['third_heaings'] : 20);
        $user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `form_id` FROM `user_forms` WHERE `form_id` = '$form'"))['form_id'];

        if($form == 'Manual') {
            $value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form IN ('Manual','')"))['fields'].',';
            if($form == 'Manual') {
                $value_config = ','.get_config($dbc, 'safety_manuals_fields').',';
            }
            if(',,' == $value_config) {
                $value_config = ',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,Third Tier Section #,Third Tier Heading,Detail,Document,Link,Videos,Signature box,Comments,Review Deadline,Configure Email,Staff,';
            }
            $value_config .= ',Detail,';
        } else if(',,' == $value_config) {
            $value_config = ',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,Staff,';
        }
        if(!empty($_GET['safetyid'])) {

            $safetyid = $_GET['safetyid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety WHERE safetyid='$safetyid'"));

            $tab = $get_contact['tab'];
            $form = $get_contact['form'];
            $heading_number = $get_contact['heading_number'];
            $sub_heading_number = $get_contact['sub_heading_number'];
            $category = $get_contact['category'];
            $heading = $get_contact['heading'];
            $sub_heading = $get_contact['sub_heading'];
            $description = $get_contact['description'];
            $marker = $get_contact['marker'];
            $assign_staff = $get_contact['assign_staff'];
            $assign_sites = $get_contact['assign_sites'];
            $assign_work_orders = $get_contact['assign_work_orders'];
            $deadline = $get_contact['deadline'];
            $email_subject = $get_contact['email_subject'];
            $email_message = $get_contact['email_message'];
            $third_heading_number = $get_contact['third_heading_number'];
            $third_heading = $get_contact['third_heading'];
            $form_name = $get_contact['form_name'];
            $action = $_GET['action'];

            $user_form_id = $get_contact['user_form_id'];

            if ($user_form_id > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$user_form_id'"));
            } else {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND (form='$form' OR ('$form' IN ('','Manual') AND `form` IN ('','Manual')))"));
            }
            $value_config = ','.$get_field_config['fields'].',';
            if($form == 'Manual') {
                $value_config = ','.get_config($dbc, 'safety_manuals_fields').',';
                if(',,' == $value_config) {
                    $value_config = ',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,Third Tier Section #,Third Tier Heading,Detail,Document,Link,Videos,Signature box,Comments,Review Deadline,Configure Email,Staff,';
                }
                $value_config .= ',Detail,';
            } else if($value_config == ',,') {
                $value_config = ',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading,Detail,Document,Staff,';
            }

            $fields = $value_config; ?>
        <input type="hidden" id="safetyid" name="safetyid" value="<?php echo $safetyid ?>" />
        <?php   }
        ?>
        <div class="scale-to-fill standard-body has-main-screen" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>

            <div class="standard-body-title">
        		<h2 <?= $user_form_layout != 'Sidebar' ? '' : 'style="display:none;"' ?>><?= $get_contact['category'] != '' ? '<b>'.$get_contact['category'].'</b><br />' : '' ?>
        		<?= $get_contact['heading_number'].' '.$get_contact['heading'].'<br />' ?>
        		<?= $get_contact['sub_heading_number'] != '' ? $get_contact['sub_heading_number'].' '.$get_contact['sub_heading'].'<br />' : '' ?>
        		<?= $get_contact['third_heading_number'] != '' ? $get_contact['third_heading_number'].' '.$get_contact['third_heading'].'<br />' : '' ?></h2>
            </div>
        <?php
            $category_safety = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM safety WHERE deleted=0 AND tab='$tab' LIMIT 1"));
            $manual_category = $category_safety['category'];
            if($manual_category == '') {
               $manual_category = 0;
            }
        ?>

            <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
            <input type="hidden" id="max_subsection" name="max_subsection" value="<?php echo $max_subsection ?>" />
            <input type="hidden" id="sub_heading_value" name="sub_heading_value" value="<?php echo $sub_heading_number ?>" />
            <input type="hidden" id="third_heading_value" name="third_heading_value" value="<?php echo $third_heading_number ?>" />

            <div class="standard-dashboard-body-content <?= $user_form_layout != 'Sidebar' ? 'pad-10' : '' ?>">

                <?php if($action == 'view') {
                  if(!empty($_GET['formid']) && $user_form_layout != 'Sidebar') {
                    $is_user_form = get_safety($dbc, $_GET['safetyid'], 'user_form_id');
                    if($is_user_form > 0) {
                        $form_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '".$_GET['formid']."'"));
                        echo '<div class="gap-bottom"><small>Created by '.get_contact($dbc, $form_details['contactid']).' on '.$form_details['today_date'].'</small></div>';
                    } else {
                        $safety_db = array_search($form,$safety_table_list);
                        $form_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `$safety_db` WHERE `".($form == 'Motor Vehicle Accident Form' ? 'incidentreportid' : 'fieldlevelriskid')."` = '".$_GET['formid']."'"));
                        echo '<div class="gap-bottom"><small>Created by '.get_contact($dbc, ($form == 'Motor Vehicle Accident Form' ? $form_details['safety_contactid'] : $form_details['contactid'])).' on '.$form_details['today_date'].'</small></div>';
                    }
                  }
                ?>
                    <script type="text/javascript">
                    $(document).ready(function() {
                        var geninfo = '';
                        $('#collapse_geninfor .panel-body').contents().each(function() {
                            if(this.tagName != 'SCRIPT') {
                                geninfo += $(this).text();
                            }
                        });
                        if(geninfo.trim() == '') {
                            $('#collapse_geninfor').closest('.panel').remove();
                        }
                    });
                    </script>
                    <?php if($user_form_layout == 'Sidebar') {
                        include('user_forms_sidebar.php'); ?>
                        <div id="user_form_div_safety_0" class="tab-section">
                        <h4>General Information</h4>
                    <?php } else { ?>
            			<div class="panel-group block-panels" id="accordion2">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_geninfor" >General Information<span class="glyphicon glyphicon-minus"></span></a>
                                </h4>
                            </div>

                            <div id="collapse_geninfor" class="panel-collapse collapse in">
                                <div class="panel-body">
                    <?php } ?>
                                <?php include ('manual_basic_field.php'); ?>

                    <?php if (strpos($value_config, ','."Detail".',') !== FALSE || $form == '') { ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php $fix = ' ';
        					$offending = '&lt;p&gt;&amp;nbsp;&lt;/p&gt;';
        						while (strpos($description,$offending.$offending.$offending.$offending) !== FALSE) {
        						  $description = str_replace($offending.$offending.$offending.$offending,$fix,$description);
        						}
        					$description = html_entity_decode($description);
        					echo $description; ?>
                        </div>
                    </div>
                    <?php } ?>

        			<ul>
                    <?php if (strpos($value_config, ','."Document".',') !== FALSE) { ?>
                        <?php include ('manual_document_field.php'); ?>
                    <?php } ?>

                    <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>
                        <?php include ('manual_link_field.php'); ?>
                    <?php } ?>

                    <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>
                        <?php include ('manual_video_field.php'); ?>
                    <?php } ?>
        			</ul>

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

                    <?php if($user_form_layout == 'Sidebar') { ?>
            			</div>
                        <hr>
                    <?php } else { ?>
                        </div>
                        </div>
                        </div>
                    <?php } ?>

                    <?php if ((($tab == 'Toolbox' || $tab == 'Tailgate')) && empty($_GET['formid'])) { ?>
                        <?php include ('safety_attendance.php'); ?>
                    <?php } ?>

                    <?php if ($user_form_id > 0) {
                        include ('user_forms.php');
                    } else {
                        if ($form == 'Field Level Hazard Assessment') { ?>
                            <?php include ('field_level_hazard_assessment/field_level_hazard_assessment.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Hydrera Site Specific Pre Job Safety Meeting Hazard Assessment') { ?>
                            <?php include ('hydrera_site_specificpre_job/hydrera_site_specificpre_job.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Weekly Safety Meeting') { ?>
                            <?php include ('weekly_safety_meeting/weekly_safety_meeting.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Tailgate Safety Meeting') { ?>
                            <?php include ('tailgate_safety_meeting/tailgate_safety_meeting.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Toolbox Safety Meeting') { ?>
                            <?php include ('toolbox_safety_meeting/toolbox_safety_meeting.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Daily Equipment Inspection Checklist') { ?>
                            <?php include ('daily_equipment_inspection_checklist/daily_equipment_inspection_checklist.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'AVS Hazard Identification') { ?>
                            <?php include ('avs_hazard_identification/avs_hazard_identification.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'AVS Near Miss') { ?>
                            <?php include ('near_miss_report/near_miss_report.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Incident Investigation Report') { ?>
                            <?php include ('incident_investigation_report/incident_investigation_report.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Follow Up Incident Report') { ?>
                            <?php include ('follow_up_incident_report/follow_up_incident_report.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Pre Job Hazard Assessment') { ?>
                            <?php include ('pre_job_hazard_assessment/pre_job_hazard_assessment.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Monthly Site Safety Inspections') { ?>
                            <?php include ('monthly_site_safety_inspection/monthly_site_safety_inspection.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Monthly Office Safety Inspections') { ?>
                            <?php include ('monthly_office_safety_inspection/monthly_office_safety_inspection.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Monthly Health and Safety Summary') { ?>
                            <?php include ('monthly_health_and_safety_summary/monthly_health_and_safety_summary.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Trailer Inspection Checklist') { ?>
                            <?php include ('trailer_inspection_checklist/trailer_inspection_checklist.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Employee Misconduct Form') { ?>
                            <?php include ('employee_misconduct_form/employee_misconduct_form.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Site Inspection Hazard Assessment') { ?>
                            <?php include ('site_inspection_hazard_assessment/site_inspection_hazard_assessment.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Weekly Planned Inspection Checklist') { ?>
                            <?php include ('weekly_planned_inspection_checklist/weekly_planned_inspection_checklist.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Equipment Inspection Checklist') { ?>
                            <?php include ('equipment_inspection_checklist/equipment_inspection_checklist.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Employee Equipment Training Record') { ?>
                            <?php include ('employee_equipment_training_record/employee_equipment_training_record.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Vehicle Inspection Checklist') { ?>
                            <?php include ('vehicle_inspection_checklist/vehicle_inspection_checklist.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Safety Meeting Minutes') { ?>
                            <?php include ('safety_meeting_minutes/safety_meeting_minutes.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Vehicle Damage Report') { ?>
                            <?php include ('vehicle_damage_report/vehicle_damage_report.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Fall Protection Plan') { ?>
                            <?php include ('fall_protection_plan/fall_protection_plan.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Spill Incident Report') { ?>
                            <?php include ('spill_incident_report/spill_incident_report.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'General Site Safety Inspection') { ?>
                            <?php include ('general_site_safety_inspection/general_site_safety_inspection.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Confined Space Entry Permit') { ?>
                            <?php include ('confined_space_entry_permit/confined_space_entry_permit.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Lanyards Inspection Checklist Log') { ?>
                            <?php include ('lanyards_inspection_checklist_log/lanyards_inspection_checklist_log.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'On The Job Training Record') { ?>
                            <?php include ('on_the_job_training_record/on_the_job_training_record.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'General Office Safety Inspection') { ?>
                            <?php include ('general_office_safety_inspection/general_office_safety_inspection.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Full Body Harness Inspection Checklist Log') { ?>
                            <?php include ('full_body_harness_inspection_checklist_log/full_body_harness_inspection_checklist_log.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Confined Space Pre Entry Checklist') { ?>
                            <?php include ('confined_space_entry_pre_entry_checklist/confined_space_entry_pre_entry_checklist.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Confined Space Entry Log') { ?>
                            <?php include ('confined_space_entry_log/confined_space_entry_log.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Emergency Response Transportation Plan') { ?>
                            <?php include ('emergency_response_transportation_plan/emergency_response_transportation_plan.php'); ?>
                        <?php } ?>

                        <?php if ($form == 'Hazard Id Report') { ?>
                            <?php include ('hazard_id_report/hazard_id_report.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Dangerous Goods Shipping Document') { ?>
                            <?php include ('dangerous_goods_shipping_document/dangerous_goods_shipping_document.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Safe Work Permit') { ?>
                            <?php include ('safe_work_permit/safe_work_permit.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Journey Management - Trip Tracking Form') { ?>
                            <?php include ('journey_management_trip_tracking/journey_management_trip_tracking.php'); ?>
                        <?php } ?>
                        <?php if ($form == 'Motor Vehicle Accident Form') { ?>
                            <?php include ('incident_reports/incident_reports.php'); ?>
                        <?php }
                        } ?>
                        <div class="form-group pad-top">
            				<button type="submit" name="field_level_hazard" value="field_level_hazard_submit" class="btn brand-btn pull-right">Submit</button>
                        </div>
        			</div>
                    <?php if($user_forms_layout == 'Sidebar') { ?>
                        </div>
                    </div>
                    <?php } ?>

                <?php } else {

                    if($form == 'Manual') {
                        include('../Safety/safety_form_manual.php');
                    } else {
                        include('../Safety/safety_form_form.php');
                    }

                } ?>
        </div>



		<script type="text/javascript">

			$(document).ready(function() {
				$('select[name="colorpicker"]').simplecolorpicker();
				$('select[name="colorpicker"]').simplecolorpicker('selectColor', '#7bd148');
				$('select[name="colorpicker"]').simplecolorpicker('destroy');

				$('select[name="colorpicker"]').simplecolorpicker({
				  picker: true
				}).on('change', function() {
				  $(document.body).css('background-color', $('select[name="colorpicker"]').val());
				});
			});

</script>

  </div>
</form>
