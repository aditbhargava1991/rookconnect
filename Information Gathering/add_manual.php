<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

if (isset($_POST['add_manual'])) {
    include ('save_manual.php');
}

if((!empty($_GET['infogatheringid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $infogatheringid = $_GET['infogatheringid'];
    $category = get_infogathering($dbc, $infogatheringid, 'category');
    $date_of_archival = date('Y-m-d');

    $query = mysqli_query($dbc,"UPDATE infogathering set deleted = 1, `date_of_archival` = '$date_of_archival' WHERE infogatheringid='$infogatheringid'");
    echo '<script type="text/javascript"> window.location.replace("infogathering.php?category='.$category.'"); </script>';
}

if((empty($_GET['infogatheringid'])) && (!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $uploadid = $_GET['uploadid'];
    $query = mysqli_query($dbc,"DELETE FROM infogathering_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    $infogatheringid = $_GET['infogatheringid'];
    echo '<script type="text/javascript"> window.location.replace("add_manual.php?infogatheringid='.$infogatheringid.'&type='.$type.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $infogatheringid = $_POST['infogatheringid'];

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
        if($type == 'infogathering') {
            $column = 'manual_infogathering_email';
        }

        $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	infogatheringid='$infogatheringid'"));

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
    $query_update_ticket = "UPDATE `infogathering_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `infogatheringid` = '$infogatheringid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?category='.$get_manual['category'].'"); </script>';
}

if (isset($_POST['manual_btn'])) {
    $manual_btn = $_POST['manual_btn'];
    $infogatheringid = $_POST['infogatheringid'];

    $form_name_save = get_infogathering($dbc, $infogatheringid, 'form');
    if(isset($_POST['form_id'])) {
        include ('user_forms.php');
    } else {
        if($form_name_save == 'Client Business Introduction') {
            include ('client_business_introduction/save_client_business_introduction.php');
        }
        if($form_name_save == 'Branding Questionnaire') {
            include ('branding_questionnaire/save_branding_questionnaire.php');
        }
        if($form_name_save == 'Website Information Gathering') {
            include ('website_information_gathering_form/save_website_information_gathering_form.php');
        }
        if($form_name_save == 'Blog') {
            include ('blog/save_blog.php');
        }
        if($form_name_save == 'Marketing Strategies Review') {
            include ('marketing_strategies_review/save_marketing_strategies_review.php');
        }
        if($form_name_save == 'Social Media Info Gathering') {
            include ('social_media_info_gathering/save_social_media_info_gathering.php');
        }
        if($form_name_save == 'Social Media Start Up Questionnaire') {
            include ('social_media_start_up_questionnaire/save_social_media_start_up_questionnaire.php');
        }

        if($form_name_save == 'Business Case Format') {
            include ('business_case_format/save_business_case_format.php');
        }
        if($form_name_save == 'Product-Service Outline') {
            include ('product_service_outline/save_product_service_outline.php');
        }
        if($form_name_save == 'Client Reviews') {
            include ('client_reviews/save_client_reviews.php');
        }
        if($form_name_save == 'SWOT') {
            include ('swot/save_swot.php');
        }
        if($form_name_save == 'GAP Analysis') {
            include ('gap_analysis/save_gap_analysis.php');
        }
        if($form_name_save == 'Lesson Plan') {
            include ('lesson_plan/save_lesson_plan.php');
        }
        if($form_name_save == 'Marketing Plan Information Gathering') {
            include ('marketing_plan_information_gathering/save_marketing_plan_information_gathering.php');
        }
        if($form_name_save == 'Marketing Information') {
            include ('marketing_information/save_marketing_information.php');
        }
        $projectid = $_POST['projectid'];
        $businessid = $_POST['businessid'];
        if(empty($_POST['infopdfid'])) {
            $staffid = $_SESSION['contactid'];
            $staffid_query = ", `staffid` = '$staffid'";
            if(!empty($_POST['salesid'])) {
                $salesid_query = ", `salesid` = '".$_POST['salesid']."'";
            }
        }
        mysqli_query($dbc, "UPDATE `infogathering_pdf` SET `businessid` = '$businessid', `projectid` = '$projectid' $staffid_query $salesid_query WHERE `fieldlevelriskid` = '$fieldlevelriskid' AND `infogatheringid`= '$infogatheringid'");
    }
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_field").change(function() {
        window.location = 'add_manual.php?type=infogathering&tab='+this.value;
	});

	$("#form_name").change(function() {
        var tab = $("#tab_field").val();
        window.location = 'add_manual.php?type=infogathering&tab='+tab+'&form='+this.value;
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

    $('select#businessid').change(function() {
        businessFilter();
    });
    $('select#projectid').change(function() {
        projectFilter();
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
function businessFilter() {
    var option = $('[name=businessid] option:selected');
    if(option.val() > 0) {
        $('[name=projectid] option').hide().filter('[data-business='+option.val()+']').show();
        $('[name=projectid]').trigger('change.select2');
    } else {
        $('[name=projectid] option').show();
        $('[name=projectid]').trigger('change.select2');
    }
}
function projectFilter() {
    var option = $('[name=projectid] option:selected');
    if(option.val() > 0) {
        if(!($('[name=businessid]').val() > 0)) {
            $('[name=businessid]').val(option.data('business')).change().trigger('change.select2');
        }
    }
}
</script>
<?php if(!IFRAME_PAGE) { ?>
    </head>

    <body>
    <?php include_once ('../navigation.php');
}
checkAuthorised('infogathering');
if(!empty($_GET['infogatheringid'])) {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM infogathering WHERE infogatheringid='".$_GET['infogatheringid']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
}
?>
<div class="container" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>
  <div class="row">
    <?php if($user_form_layout == 'Sidebar') { ?>
        <h1 style="margin-top: 0; padding-top: 0;"><a href="infogathering.php?tab=Toolbox">Information gathering</a></h1>
    <?php } else { ?>
    	<h1>Information Gathering</h1>
    	<div class="gap-top double-gap-bottom"><a href="infogathering.php?tab=Toolbox" class="btn config-btn">Back to Dashboard</a></div>
    <?php } ?>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : '' ?>>
        <input type="hidden" name="salesid" value="<?= $_GET['salesid'] ?>">
    <?php
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
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_infogathering WHERE form='$form'"));
        $value_config = ','.$get_field_config['fields'].',';
        $max_section = $get_field_config['max_section'];
        $max_subsection = $get_field_config['max_subsection'];
        $max_thirdsection = $get_field_config['max_thirdsection'];
        $user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `form_id` FROM `user_forms` WHERE `form_id` = '$form'"))['form_id'];
        $user_form = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['name'];

        if(isset($_GET['projectid'])) {
            $projectid = $_GET['projectid'];
        }

        if(!empty($_GET['infogatheringid'])) {

            $infogatheringid = $_GET['infogatheringid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM infogathering WHERE infogatheringid='$infogatheringid'"));

            $form = $get_contact['form'];
            $heading_number = $get_contact['heading_number'];
            $sub_heading_number = $get_contact['sub_heading_number'];
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
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_infogathering WHERE form='$user_form_id'"));
            } else {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_infogathering WHERE form='$form'"));
            }

            $value_config = ','.$get_field_config['fields'].',';
            $max_section = $get_field_config['max_section'];
            $max_subsection = $get_field_config['max_subsection'];
            $max_thirdsection = $get_field_config['max_thirdsection'];

            if(!empty($_GET['formid'])) {
                $info_pdf = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `infogathering_pdf` WHERE `infogatheringid` = '$infogatheringid' AND `fieldlevelriskid` = '".$_GET['formid']."' ORDER BY `infopdfid` DESC"));
                $infopdfid = $info_pdf['infopdfid'];
                $projectid = $info_pdf['projectid'];
                $businessid = $info_pdf['businessid']; ?>
                <input type="hidden" id="infopdfid" name="infopdfid" value="<?= $infopdfid ?>" />
            <?php }
        ?>
        <input type="hidden" id="infogatheringid" name="infogatheringid" value="<?php echo $infogatheringid ?>" />
        <?php   }

        ?>
        <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />

        <?php if($action != 'view') { ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">Form:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Form..." id="form_name" name="form_name" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($form == "Client Business Introduction") { echo " selected"; } ?> value="Client Business Introduction">Client Business Introduction</option>
                  <option <?php if ($form == "Branding Questionnaire") { echo " selected"; } ?> value="Branding Questionnaire">Branding Questionnaire</option>
                  <option <?php if ($form == "Website Information Gathering") { echo " selected"; } ?> value="Website Information Gathering">Website Information Gathering</option>
                  <option <?php if ($form == "Blog") { echo " selected"; } ?> value="Blog">Blog</option>
                  <option <?php if ($form == "Marketing Strategies Review") { echo " selected"; } ?> value="Marketing Strategies Review">Marketing Strategies Review</option>
                  <option <?php if ($form == "Social Media Info Gathering") { echo " selected"; } ?> value="Social Media Info Gathering">Social Media Info Gathering</option>
                  <option <?php if ($form == "Social Media Start Up Questionnaire") { echo " selected"; } ?> value="Social Media Start Up Questionnaire">Social Media Start Up Questionnaire</option>
                  <option <?php if ($form == "Business Case Format") { echo " selected"; } ?> value="Business Case Format">Business Case Format</option>
                  <option <?php if ($form == "Product-Service Outline") { echo " selected"; } ?> value="Product-Service Outline">Product-Service Outline</option>
                  <option <?php if ($form == "Client Reviews") { echo " selected"; } ?> value="Client Reviews">Client Reviews</option>
                  <option <?php if ($form == "SWOT") { echo " selected"; } ?> value="SWOT">SWOT</option>
                  <option <?php if ($form == "GAP Analysis") { echo " selected"; } ?> value="GAP Analysis">GAP Analysis</option>
                  <option <?php if ($form == "Lesson Plan") { echo " selected"; } ?> value="Lesson Plan">Lesson Plan</option>
                  <option <?php if ($form == "Marketing Plan Information Gathering") { echo " selected"; } ?> value="Marketing Plan Information Gathering">Marketing Plan Information Gathering</option>
                  <option <?php if ($form == "Marketing Information") { echo " selected"; } ?> value="Marketing Information">Marketing Information</option>
                  <?php
                  $query = mysqli_query ($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,infogathering,%'");;
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
                include('user_forms_sidebar.php');
            } ?>

            <?php include ('manual_basic_field.php'); ?>

            <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Detail:</label>
                <div class="col-sm-8">
                    <?php echo html_entity_decode($description); ?>
                    <?php //echo $description; ?>
                </div>
            </div>
            <?php } ?>

            <?php if(strpos($value_config, ','."Business".',') !== FALSE) { ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Business:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select a Business..." name="businessid" id="businessid" class="chosen-select-deselect form-control" width="380">
                            <option value=''></option><?php
                            $query = mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Business' AND `deleted`=0 ORDER BY `category`");
                            while($row = mysqli_fetch_array($query)) {
                                if ($businessid== $row['contactid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>

            <?php if(strpos($value_config, ','."Project".',') !== FALSE || !empty($_GET['projectid'])) { ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Project:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="projectid" id="projectid" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 order by project_name");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option data-business='".$row['businessid']."' data-client='".$row['clientid']."' ";
                                echo ($projectid == $row['projectid'] ? 'selected' : ($businessid == 0 || $businessid == $row['businessid'] ? '' : 'style="display:none;"'));
                                echo " value='".$row['projectid']."'>".get_project_label($dbc,$row).'</option>';
                            }
                            ?>
                        </select>
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

            <?php if ($user_form_id > 0) {
                include ('user_forms.php');
            } else { ?>
                <?php if ($form == 'Client Business Introduction') { ?>
                    <?php include ('client_business_introduction/client_business_introduction.php'); ?>
                <?php } ?>

                <?php if ($form == 'Branding Questionnaire') { ?>
                    <?php include ('branding_questionnaire/branding_questionnaire.php'); ?>
                <?php } ?>
                <?php if ($form == 'Website Information Gathering') { ?>
                    <?php include ('website_information_gathering_form/website_information_gathering_form.php'); ?>
                <?php } ?>
                <?php if ($form == 'Blog') { ?>
                    <?php include ('blog/blog.php'); ?>
                <?php } ?>
                <?php if ($form == 'Marketing Strategies Review') { ?>
                    <?php include ('marketing_strategies_review/marketing_strategies_review.php'); ?>
                <?php } ?>
                <?php if ($form == 'Social Media Info Gathering') { ?>
                    <?php include ('social_media_info_gathering/social_media_info_gathering.php'); ?>
                <?php } ?>
                <?php if ($form == 'Social Media Start Up Questionnaire') { ?>
                    <?php include ('social_media_start_up_questionnaire/social_media_start_up_questionnaire.php'); ?>
                <?php }

                if($form == 'Business Case Format') {
                    include ('business_case_format/business_case_format.php');
                }
                if($form == 'Product-Service Outline') {
                    include ('product_service_outline/product_service_outline.php');
                }
                if($form == 'Client Reviews') {
                    include ('client_reviews/client_reviews.php');
                }
                if($form == 'SWOT') {
                    include ('swot/swot.php');
                }
                if($form == 'GAP Analysis') {
                    include ('gap_analysis/gap_analysis.php');
                }
                if($form == 'Lesson Plan') {
                    include ('lesson_plan/lesson_plan.php');
                }
                if($form == 'Marketing Plan Information Gathering') {
                    include ('marketing_plan_information_gathering/marketing_plan_information_gathering.php');
                }
                if($form == 'Marketing Information') {
                    include ('marketing_information/marketing_information.php');
                }
                ?>
            <?php } ?>

            <div class="form-group">
              <div class="col-sm-12">
                <a href="infogathering.php?tab=Toolbox" class="btn brand-btn btn-lg pull-left">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
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
						<?php
						$sender = get_email($dbc, $_SESSION['contactid']);
						$subject = 'Information Gathering Assigned to you for Review';
						$body = 'Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>
							Information Gathering : <a target="_blank" href="'.WEBSITE_URL.'/Staff/add_manual.php?infogatheringid=[INFOID]&type=[MANUALTYPE]&action=view">Click Here</a><br>';
						?>
						<div class="form-group clearfix completion_date">
							<label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
							<div class="col-sm-8">
								<select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
									<?php $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0"),MYSQLI_ASSOC));
									foreach($result as $staff_id) {
										echo '<option '.(strpos(','.$assign_staff.',', ','.$staff_id.',') !== FALSE ? "selected" : "").' value="'.$staff_id.'">'.get_contact($dbc,$staff_id)."</option>\n";
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
              <div class="col-sm-12">
                <a href="infogathering.php?tab=Toolbox" class="btn brand-btn btn-lg pull-left">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				<button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
              </div>
            </div>
        <?php }
        if($user_form_layout == 'Sidebar') { ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    </form>

  </div>
</div>
<?php if(!IFRAME_PAGE) { include ('../footer.php'); } ?>