<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

if((!empty($_GET['manualtypeid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $manualtypeid = $_GET['manualtypeid'];
    $type = $_GET['type'];
    $category = get_manual($dbc, $manualtypeid, 'category');

    $query = mysqli_query($dbc,"DELETE FROM manuals WHERE manualtypeid='$manualtypeid'");
    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?category='.$category.'"); </script>';
}

if((!empty($_GET['manualtypeid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'status')) {
    $manualtypeid = $_GET['manualtypeid'];
    $type = $_GET['type'];
	$status = $_GET['value'];
    $category = get_manual($dbc, $manualtypeid, 'category');

    $query = mysqli_query($dbc,"UPDATE manuals set status = $status WHERE manualtypeid='$manualtypeid'");
    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?category='.$category.'"); </script>';
}

if((!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $uploadid = $_GET['uploadid'];
    $query = mysqli_query($dbc,"DELETE FROM manuals_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    $manualtypeid = $_GET['manualtypeid'];
    echo '<script type="text/javascript"> window.location.replace("add_manual.php?manualtypeid='.$manualtypeid.'&type='.$type.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $manualtypeid = $_POST['manualtypeid'];

    $type = $_POST['type'];
    $category = $_POST['category'];

	$get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	manualtypeid='$manualtypeid'"));

    $staffid = $_SESSION['contactid'];
    $today_date = date('Y-m-d');
	// Insert a row if it isn't already there
	$query_insert_row = "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`) SELECT '$manualtypeid', '$staffid' FROM (SELECT COUNT(*) rows FROM `manuals_staff` WHERE `manualtypeid`='$manualtypeid' AND `staffid`='$staffid') LOGTABLE WHERE rows=0";
  $before_change = '';
  $history = "Manual Staff entry added. <br />";
  add_update_history($dbc, 'hr_history', $history, '', $before_change);
	mysqli_query($dbc, $query_insert_row);
    $query_update_ticket = "UPDATE `manuals_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `manualtypeid` = '$manualtypeid' AND staffid='$staffid'";
	echo '<script>console.log("'.$query_update_ticket.'");</script>';
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket) or die(mysqli_error($dbc));
	if($result_update_ticket) {
		echo '<script>console.log("Success");</script>';
	} else {
		echo '<script>console.log("Failed: '.mysqli_error($dbc).'");</script>';
	}
	$manual_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`manualstaffid`) id FROM `manuals_staff` WHERE `manualtypeid` = '$manualtypeid' AND staffid='$staffid' AND `today_date`='$today_date'"))['id'];
	imagepng(sigJsonToImage($_POST['output']), 'download/sign_'.$manual_id.'.png');

	$pdf_path = 'download/manual_'.$manualtypeid.'_signed_'.$manual_id.'_'.$today_date.'.pdf';
	include('../Manuals/manual_pdf.php');

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
        if($type == 'safety_manual') {
            $column = 'manual_sm_email';
        }

        //Mail
        $to = get_config($dbc, $column);
        if($type != 'policy_procedures' && $type != 'operations_manual' && $type != 'emp_handbook' && $type != 'guide' && $type != 'safety' && $type != 'safety_manual') {
            $to = mysqli_fetch_array(mysqli_query($dbc, "SELECT `send_email` FROM `field_config_hr_manuals` WHERE `tab` = '$type' AND `category` = '$category'"))['send_email'];
        }
        $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
        $subject = 'Manual Read by '.$user;

        $message = "Topic (Sub Tab) : ".$get_manual['category'].'<br>';
        $message .= "Section Heading : ".$get_manual['heading'].'<br>';
        $message .= "Sub Section Heading : ".$get_manual['sub_heading'].'<br>';
        $message .= "Comment<br/><br/>".$_POST['comment'];
        send_email('', $to, '', '', $subject, $message, $pdf_path);

        //Mail
    }

	$return_url = 'hr.php?tab='.$type.'&category='.$get_manual['category'].'&type=manuals';
    echo '<script type="text/javascript"> window.location.replace("'.$return_url.'"); </script>';
}

if (isset($_POST['field_level_hazard'])) {
    $field_level_hazard = $_POST['field_level_hazard'];
    $manualtypeid = $_POST['manualtypeid'];
    $today_date = $_POST['today_date'];
    $jobid = $_POST['jobid'];
    $contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);

    $contactid = $_SESSION['contactid'];
    $assessment_option = implode(',',$_POST['assessment_option']);
    $working_alone = $_POST['working_alone'];
    $total_task = count($_POST['task']);
    $all_task = '';
    for($i=0; $i<$total_task; $i++) {
        if($_POST['task'][$i] != '') {
            $all_task .= $_POST['task'][$i].'**'.$_POST['hazard'][$i].'**'.$_POST['hazard_level'][$i].'**'.$_POST['hazard_plan'][$i].'**##**';
        }
    }
    $all_task = filter_var($all_task,FILTER_SANITIZE_STRING);
    $job_complete = implode(',',$_POST['job_complete']);
    $worker_name = filter_var($_POST['worker_name'],FILTER_SANITIZE_STRING);
    $foreman_name = filter_var($_POST['foreman_name'],FILTER_SANITIZE_STRING);

    if($worker_name != '') {
        $sign = $_POST['output'];
    }
    if($foreman_name != '') {
        $sign = $_POST['sign2'];
    }
    $img = sigJsonToImage($sign);
    imagepng($img, 'download/flha_'.$today_date.'_'.$manualtypeid.'_'.$contactid.'.png');

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldlevelriskid) AS fieldlevelriskid FROM form_field_level_risk_assessment WHERE manualtypeid='$manualtypeid' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    if($get_config['fieldlevelriskid'] > 0) {
        $query_update_employee = "UPDATE `form_field_level_risk_assessment` SET `assessment_option` = '$assessment_option',`working_alone` = '$working_alone',`all_task` = CONCAT(all_task,'$all_task'), `job_complete` = '$job_complete',`worker_name` = '$worker_name',`foreman_name` = '$foreman_name' WHERE manualtypeid='$manualtypeid' AND contactid='$contactid' AND DATE(today_date) = CURDATE()";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_site = "INSERT INTO `form_field_level_risk_assessment` (`manualtypeid`, `today_date`, `jobid`, `contactid`, `location`, `assessment_option`, `working_alone`, `all_task`, `job_complete`, `worker_name`, `foreman_name`) VALUES	('$manualtypeid', '$today_date', '$jobid', '$contactid', '$location', '$assessment_option', '$working_alone', '$all_task', '$job_complete', '$worker_name', '$foreman_name')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
    }

    $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	manualtypeid='$manualtypeid'"));

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(manualstaffid) AS manualstaffid FROM manuals_staff WHERE manualtypeid='$manualtypeid' AND staffid='$contactid' AND DATE(today_date) = CURDATE()"));
    if($get_config['manualstaffid'] == 0) {
        $query_insert_upload = "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`, `today_date`) VALUES ('$manualtypeid', '$contactid', '$today_date')";
        $before_change = '';
        $history = "Manual Staff entry added. <br />";
        add_update_history($dbc, 'hr_history', $history, '', $before_change);
        $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
    }

    if($field_level_hazard == 'field_level_hazard_save') {
        echo '<script type="text/javascript"> window.location.replace("safety.php?category='.$get_manual['category'].'"); </script>';
    } else {
        include ('../Manuals/field_level_hazard_pdf.php');
        echo field_level_hazard_pdf($dbc,$manualtypeid);
        echo '<script type="text/javascript"> window.location.replace("safety.php?category='.$get_manual['category'].'"); </script>';
    }
}

if (isset($_POST['add_manual'])) {
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
    $sub_heading_number	= filter_var($_POST['sub_heading_number'],FILTER_SANITIZE_STRING);
    $sub_heading		= filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);

    $third_heading			= filter_var($_POST['third_heading'],FILTER_SANITIZE_STRING);
    $third_heading_number	= filter_var($_POST['third_heading_number'],FILTER_SANITIZE_STRING);

    $description	= filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $assign_staff	= ','.implode(',',$_POST['assign_staff']).',';
    $deadline		= filter_var($_POST['deadline'],FILTER_SANITIZE_STRING);

	$email_subject	= filter_var ( $_POST['email_subject'], FILTER_SANITIZE_STRING );
	$email_message	= filter_var ( htmlentities ( $_POST['email_message'] ), FILTER_SANITIZE_STRING );
	$subject	 	= $email_subject;
	$message	 	= $email_message;

	$manual_type	= $_POST['type'];

    $form_name = implode(',',$_POST['form_name']);

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    $last_edited = date('Y-m-d');
    if(empty($_POST['manualtypeid'])) {
        $query_insert_vendor = "INSERT INTO `manuals` (`manual_type`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading`, `third_heading_number`, `form_name`, `description`, `assign_staff`, `deadline`, `email_subject`, `email_message`, `last_edited`) VALUES ('$manual_type', '$category', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$third_heading', '$third_heading_number', '$form_name', '$description', '$assign_staff', '$deadline', '$email_subject', '$email_message', '$last_edited')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $manualtypeid = mysqli_insert_id($dbc);
        $before_change = '';
        $history = "Manual added. <br />";
        add_update_history($dbc, 'hr_history', $history, '', $before_change);

        $url = 'Added';
    } else {
        $manualtypeid = $_POST['manualtypeid'];

        $get_manual = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT description, last_edited FROM manuals WHERE manualtypeid='$manualtypeid'"));
        $db_desc = $get_manual['description'];

        if($db_desc == $description) {
            $last_edited = $get_manual['last_edited'];
        } else {
            $last_edited = date('Y-m-d');
        }

        $before_change = '';

        $query_update_vendor = "UPDATE `manuals` SET `manual_type` = '$manual_type', `category` = '$category', `heading_number` = '$heading_number', `heading` = '$heading', `sub_heading_number` = '$sub_heading_number', `sub_heading` = '$sub_heading', `third_heading_number` = '$third_heading_number', `third_heading` = '$third_heading', `form_name` = '$form_name', `description` = '$description', `assign_staff` = '$assign_staff', `deadline` = '$deadline', `email_subject` = '$email_subject', `email_message` = '$email_message', `last_edited` = '$last_edited' WHERE `manualtypeid` = '$manualtypeid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        $history = 'Manual Updated. <br />';
        add_update_history($dbc, 'hr_history', $history, '', $before_change);

        $url = 'Updated';
    }

    $assign_staff = $_POST['assign_staff'];
    for($i = 0; $i < count($_POST['assign_staff']); $i++) {
        if($assign_staff[$i] != '') {
            $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(manualstaffid) AS total_id FROM manuals_staff WHERE manualtypeid='$manualtypeid' AND staffid='{$assign_staff[$i]}' AND done=0"));
            if($get_staff['total_id'] == 0) {

                //Mail
                $to = get_email($dbc, $assign_staff[$i]);
				$sender = (!empty($_POST['email_sender']) ? $_POST['email_sender'] : '');
				$subject = $_POST['email_subject'];
				$email_body = str_replace(['[MANUALID]','[MANUALTYPE]'], [$manualtypeid,$manual_type], $_POST['email_body']);

				if($to != '') {
					try {
						send_email($sender, $to, '', '', $subject, $email_body, '');
					} catch(Exception $e) {
						echo "<script>alert('Unable to send email. Please try again later.');</script>";
					}
				}

                //Mail

                $query_insert_upload = "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`) VALUES ('$manualtypeid', '{$assign_staff[$i]}')";
                $before_change = '';
                $history = "Manual Staff entry added. <br />";
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
                $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            }
        }
    }
    $last_edited = date('Y-m-d');
    $document = htmlspecialchars($_FILES["document"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'document', '{$document[$i]}')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
            $history = "Manual upload entry added. <br />";
            add_update_history($dbc, 'hr_history', $history, '', $before_change);

            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'manuals', 'last_edited', 'manualtypeid', $manualtypeid);
                $result_update_manual = mysqli_query($dbc, "UPDATE `manuals` SET `last_edited` = '$last_edited' WHERE `manualtypeid` = '$manualtypeid'");
                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    $link = $_POST['link'];
    for($i = 0; $i < count($_POST['link']); $i++) {
        if($link[$i] != '') {
            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'link', '{$link[$i]}')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
            $history = "Manual upload entry added. <br />";
            add_update_history($dbc, 'hr_history', $history, '', $before_change);

            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'manuals', 'last_edited', 'manualtypeid', $manualtypeid);
                $result_update_manual = mysqli_query($dbc, "UPDATE `manuals` SET `last_edited` = '$last_edited' WHERE `manualtypeid` = '$manualtypeid'");
                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

    $video = $_FILES["video"]["name"];
    for($i = 0; $i < count($_FILES['video']['name']); $i++) {
        if($video[$i] != '') {
            move_uploaded_file($_FILES["video"]["tmp_name"][$i], "download/" . $_FILES["video"]["name"][$i]) ;

            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'video', '{$video[$i]}')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            $before_change = '';
            $history = "Manual upload entry added. <br />";
            add_update_history($dbc, 'hr_history', $history, '', $before_change);
            if($url == 'Updated') {
                $before_change = capture_before_change($dbc, 'manuals', 'last_edited', 'manualtypeid', $manualtypeid);
                $result_update_manual = mysqli_query($dbc, "UPDATE `manuals` SET `last_edited` = '$last_edited' WHERE `manualtypeid` = '$manualtypeid'");
                $history = capture_after_change('last_edited', $last_edited);
                add_update_history($dbc, 'hr_history', $history, '', $before_change);
            }
        }
    }

	if(isset($_GET['maintype']))
		$maintype = $_GET['maintype'];

	if(isset($_GET['from_manual']))
		$manual_type = 'manual';
	else
		$manual_type = $_POST['type'];

    $return_url = 'hr.php?tab='.$manual_type.'&category='.$category.'&type=manuals';

    echo '<script type="text/javascript"> window.location.replace("'.$return_url.'"); </script>';

	$manual_type = $_POST['type'];

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {

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

    $('select[name=category]').val($('input[name=category]').val());
    $('select[name=category]').attr('disabled', true);
    $('select[name=category]').trigger('change.select2');
    <?php if ($_GET['action'] != 'config') { ?>
        var category = $('#category').val();
        var type = $('#type').val();
        $.ajax({    //create an ajax request to load_page.php
            type: "POST",
            url: "../Manuals/manual_ajax_all.php?fill=heading_number",
            data: { category: category, type: $('#type').val(), max_section: $('#max_section').val(), manualid: $('[name=manualtypeid]').val() },
            dataType: "html",   //expect html to be returned
            success: function(response){
                $("#heading_number").empty().html(response).change().trigger("change.select2");
            }
        });
    <?php } ?>

} );
function selectTopic(sel, category) {
    var category = sel.value;
    var type = $('#type').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "POST",
		url: "../Manuals/manual_ajax_all.php?fill=heading_number",
		data: { category: sel.value, type: $('#type').val(), max_section: $('#max_section').val(), manualid: $('[name=manualtypeid]').val() },
		dataType: "html",   //expect html to be returned
		success: function(response){
			$("#heading_number").empty().html(response).change().trigger("change.select2");
		}
	});
}
function selectSection(sel) {
    var category = $('#category').val();
    var type = $('#type').val();
	var heading_number = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Manuals/manual_ajax_all.php?fill=section&heading_number="+heading_number+"&category="+category+"&type="+type,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#heading").val(response);
			$("#heading").trigger("change.select2");
		}
	});
	if(heading_number > 0) {
		$.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "../Manuals/manual_ajax_all.php?fill=sub_heading_number",
			data: { heading_number: heading_number, sub_heading: $('#sub_heading_value').val(), category: category, type: type, max_section: $('#max_subsection').val() },
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#sub_heading_number").empty().html(response).change().trigger("change.select2");
			}
		});
	}
	else {
		$("#sub_heading_number").empty().change().trigger("change.select2");
	}
}
function selectSubSection(sel) {
    var category = $('#category').val();
    var type = $('#type').val();
	var sub_heading_number = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "manual_ajax_all.php?fill=subsection&sub_heading_number="+sub_heading_number+"&category="+category+"&type="+type,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#sub_heading").val(response);
		}
	});
	if(sub_heading_number > 0) {
		$.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "manual_ajax_all.php?fill=third_heading_number",
			data: { sub_heading_number: sub_heading_number, third_heading: $('#third_heading_value').val(), category: category, type: type, max_section: $('#max_subsection').val() },
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#third_heading_number").empty().html(response).change().trigger("change.select2");
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

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php

        $manual_type = '';
        $type = '';
        if(!empty($_GET['tab'])) {
            $type = $_GET['tab'];
        } else {
            $type = $_GET['type'];
        }
        if(!empty($_GET['category'])) {
            $manual_type = $_GET['category'];
        }

        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_hr_manuals` WHERE `tab` = '$type' AND `category` = '$manual_type'"));
        $value_config = ','.$get_field_config['fields'].',';
        $max_section = $get_field_config['max_section'];
        $max_subsection = $get_field_config['max_subsection'];
        $max_thirdsection = $get_field_config['max_thirdsection'];

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

        if(!empty($_GET['manualtypeid'])) {

            $manualtypeid = $_GET['manualtypeid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM manuals WHERE manualtypeid='$manualtypeid'"));

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
            $third_heading_number = $get_contact['third_heading_number'];
            $third_heading = $get_contact['third_heading'];
            $form_name = $get_contact['form_name'];
            $action = $_GET['action'];
        ?>
        <input type="hidden" id="manualtypeid" name="manualtypeid" value="<?php echo $manualtypeid ?>" />
        <?php   }
        ?>
        <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
        <input type="hidden" id="category" name="category" value="<?php echo $manual_type ?>">
        <input type="hidden" id="max_section" name="max_section" value="<?php echo $max_section ?>" />
        <input type="hidden" id="max_subsection" name="max_subsection" value="<?php echo $max_subsection ?>" />
        <input type="hidden" id="sub_heading_value" name="sub_heading_value" value="<?php echo $sub_heading_number ?>" />
        <input type="hidden" id="third_heading_value" name="third_heading_value" value="<?php echo $third_heading_number ?>" />

        <h1><?php echo $manual_type ?></h1>

        <?php
            $category_man = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM manuals WHERE deleted=0 AND manualtypeid='$manualtypeid' LIMIT 1"));
            $manual_category = $category_man['category'];
            if($manual_category == '') {
               $manual_category = 0;
            }
        ?>

		<?php
		if(isset($_GET['from_manual'])) {
			$tmp_type = $type;
			$type = 'manual';
		}
		if(isset($_GET['maintype'])) {
			$manual_category .= '&maintype='.$_GET['maintype'];
		}
		?>
		<div class="gap-top triple-gap-bottom">
		<a href="hr.php?tab=<?= empty($_GET['tab']) ? $_GET['type'] : $_GET['tab'] ?>&category=<?= $_GET['category'] ?>&type=manuals" class="btn config-btn">Back to Dashboard</a></div>

		<?php if(isset($_GET['from_manual'])): ?>
			<?php $type = $tmp_type; ?>
		<?php endif; ?>

        <?php if($action == 'view') { ?>
			<div style='background-color: rgba(200,200,200,.9); padding:10px; color:black; width:100%; margin:auto; border:5px outset grey; border-radius:15px;'>
			<a href="?manualtypeid=<?= $manualtypeid ?>&type=<?= $type ?>&action=pdf" class="pull-right">Download as PDF</a><div class="clearfix"></div>
            <?php include ('../Manuals/manual_basic_field.php'); ?>

            <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Details:</label>
                <div class="col-sm-8">
                    <?php echo html_entity_decode($description); ?>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Document".',') !== FALSE) { ?>
                <?php include ('../Manuals/manual_document_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>
                <?php include ('../Manuals/manual_link_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>
                <?php include ('../Manuals/manual_video_field.php'); ?>
            <?php } ?>

            <?php if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { ?>
                <?php include ('../Manuals/field_level_hazard_assessment.php'); ?>
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

            <div class="form-group">
                <div class="col-sm-6">
                    <a href="hr.php?tab=<?= empty($_GET['tab']) ? $_GET['type'] : $_GET['tab'] ?>&category=<?= $_GET['category'] ?>&type=manuals" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
                </div>
              <div class="col-sm-6">
                <?php if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { ?>
                    <button type="submit" name="field_level_hazard" value="field_level_hazard_submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                    <button type="submit" name="field_level_hazard" value="field_level_hazard_save" class="btn brand-btn btn-lg pull-right">Save</button>
                <?php } else { ?>
                    <button type="submit" name="view_manual" value="view_manual" class="btn brand-btn btn-lg pull-right">Submit</button>
                <?php } ?>
              </div>
            </div>
        </div>

        <?php } else if($action == 'pdf') {
			$pdf_path = 'download/manual_'.$manualtypeid.'_export.pdf';
			include ('../Manuals/manual_pdf.php');
			echo "<script>window.location.replace('".$pdf_path."');</script>";
		} else { ?>

        <div class="panel-group" id="accordion2">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Headings<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_abi" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('../Manuals/manual_basic_field.php'); ?>
                    </div>
                </div>
            </div>

            <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_content" >Content<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_content" class="panel-collapse collapse">
                    <div class="panel-body">
                          <div class="form-group">
                            <label for="first_name[]" class="col-sm-4 control-label">Details:</label>
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
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_docs" >Document Upload<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_docs" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('../Manuals/manual_document_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_link" >Add Link<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_link" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('../Manuals/manual_link_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_video" >Upload Video<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_video" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('../Manuals/manual_video_field.php'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_hazard" >Field Level Hazard Assessment<span class="glyphicon glyphicon-plus"></span></a>
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
            <?php } ?>

            <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >Assign to Staff<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_staff" class="panel-collapse collapse">
                    <div class="panel-body">

						<?php
						$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
						$subject = 'Manual Assigned to you for Review';
						$body = '<br /><br />Manual: <a target="_blank" href="'.WEBSITE_URL.'/Manuals/add_manual.php?manualtypeid=[MANUALID]&type=[MANUALTYPE]&action=view">Click Here</a><br />';
						?>
						<div class="form-group clearfix completion_date">
							<label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
							<div class="col-sm-8">
								<select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
									<?php
									$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status = 1 order by first_name");
									//$check_assigned = mysqli_query ( $dbc, "SELECT c.`contactid` FROM `contacts` c INNER JOIN `manuals` m ON (c.`contactid` = " );

									if ( empty ( $_GET['manualtypeid'] ) ) {
										while ( $row = mysqli_fetch_array ( $query ) ) { ?>
											<option selected="selected" value="<?= $row['contactid']; ?>"><?= decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']); ?></option><?php
										}
									} else {
										while($row = mysqli_fetch_array($query)) {
											if ( !empty ( $assign_staff ) ) { ?>
												<option <?php if (strpos(','.$assign_staff.',', ','.$row['contactid'].',') !== FALSE) { echo ' selected="selected"'; } ?> value="<?php echo $row['contactid']; ?>"><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option><?php
											} else { ?>
												<option selected="selected" value="<?= $row['contactid']; ?>"><?= decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']); ?></option><?php
											}
										}
									} ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Sending Email Address:</label>
							<div class="col-sm-8">
								<input type="text" name="email_sender" class="form-control" value="<?php echo $sender; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="email_subject" class="form-control" value="<?php echo $subject; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Body:</label>
							<div class="col-sm-8">
								<textarea name="email_body" class="form-control"><?php echo $body; ?></textarea>
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
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_deadline" >Review Deadline<span class="glyphicon glyphicon-plus"></span></a>
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
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_email">Configure Email<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_email" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group clearfix">
								<label for="email_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
								<div class="col-sm-8"><input class="form-control" name="email_subject" type="text" value="<?= $email_subject; ?>"></div>
							</div>
							<div class="form-group clearfix">
								<label for="email_message" class="col-sm-4 control-label text-right">Email Message:</label>
								<div class="col-sm-8"><textarea name="email_message"><?= html_entity_decode($email_message); ?></textarea></div>
							</div>
						</div>
					</div>
				</div>
            <?php } ?>

        </div>

            <div class="form-group">
				<p><span class="hp-red"><em>Required Fields *</em></span></p>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your manual."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="hr.php?tab=<?= empty($_GET['tab']) ? $_GET['type'] : $_GET['tab'] ?>&category=<?= $_GET['category'] ?>&type=manuals" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
                </div>
				<div class="col-sm-6">
					<button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
					<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to finalize your manual."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</div>
            </div>

			</div>
        <?php } ?>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
