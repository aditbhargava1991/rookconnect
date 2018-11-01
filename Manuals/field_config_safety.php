<?php
include ('../include.php');
checkAuthorised('manual');
error_reporting(0);

if (isset($_POST['submit'])) {
    $safety = implode(',',$_POST['safety']);

    if (strpos(','.$safety.',', ','.'Section #,Section Heading,Sub Section #,Sub Section Heading'.',') === false) {
        $safety = $safety.',Section #,Section Heading,Sub Section #,Sub Section Heading';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(manualsid) AS manualsid FROM field_config_manuals"));
    if($get_field_config['manualsid'] > 0) {
        $query_update_employee = "UPDATE `field_config_manuals` SET safety = '$safety' WHERE `manualsid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_manuals` (`safety`) VALUES ('$safety')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $manual_policy_pro_email = filter_var($_POST['manual_policy_pro_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='manual_policy_pro_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$manual_policy_pro_email' WHERE name='manual_policy_pro_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('manual_policy_pro_email', '$manual_policy_pro_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $policy_pro_max_section = filter_var($_POST['policy_pro_max_section'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='policy_pro_max_section'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$policy_pro_max_section' WHERE name='policy_pro_max_section'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('policy_pro_max_section', '$policy_pro_max_section')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $policy_pro_max_subsection = filter_var($_POST['policy_pro_max_subsection'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='policy_pro_max_subsection'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$policy_pro_max_subsection' WHERE name='policy_pro_max_subsection'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('policy_pro_max_subsection', '$policy_pro_max_subsection')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $policy_pro_max_thirdsection = filter_var($_POST['policy_pro_max_thirdsection'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='policy_pro_max_thirdsection'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$policy_pro_max_thirdsection' WHERE name='policy_pro_max_thirdsection'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('policy_pro_max_thirdsection', '$policy_pro_max_thirdsection')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Field Level Hazard Assessment

    $form_field_level_risk_assessment = implode(',',$_POST['assessment_option']);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='form_field_level_risk_assessment'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$form_field_level_risk_assessment' WHERE name='form_field_level_risk_assessment'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('form_field_level_risk_assessment', '$form_field_level_risk_assessment')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_safety.php"); </script>';
}
?>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<!--<a href="manual_reporting.php?type=safety" class="btn config-btn">Back</a>-->
<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>

<?php include ('field_config_manual.php'); ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT manual FROM field_config_manuals"));
$value_config = ','.$get_field_config['manual'].',';
?>
<?php if (strpos($value_config, ','."Policies & Procedures".',') !== FALSE) { ?>
<button type="button" class="btn brand-btn mobile-block" >Policies & Procedures</button>
<?php } ?>
<?php if (strpos($value_config, ','."Operations Manual".',') !== FALSE) { ?>
<a href='field_config_operations_manual.php'><button type="button" class="btn brand-btn mobile-block" >Operations Manual</button></a>
<?php } ?>
<?php if (strpos($value_config, ','."Employee Handbook".',') !== FALSE) { ?>
<a href='field_config_emp_handbook.php'><button type="button" class="btn brand-btn mobile-block" >Employee Handbook</button></a>
<?php } ?>
<?php if (strpos($value_config, ','."How to Guide".',') !== FALSE) { ?>
<a href='field_config_guide.php'><button type="button" class="btn brand-btn mobile-block" >How to Guide</button></a>
<?php } ?>
<?php if (strpos($value_config, ','."Safety".',') !== FALSE) { ?>
<a href='field_config_safety.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Safety</button></a>
<?php } ?>

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT safety FROM field_config_manuals"));
$value_config = ','.$get_field_config['safety'].',';
?>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                        Choose Fields for Safety<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field" class="panel-collapse collapse">
                <div class="panel-body">
				<div id='no-more-tables'>
					<table border='2' cellpadding='10' class='table'>
                        <tr>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Topic (Sub Tab)".',') !== FALSE) { echo " checked"; } ?> value="Topic (Sub Tab)" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Topic (Sub Tab)
                            </td>
                            <td>
                                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Section #".',') !== FALSE) { echo " checked"; } ?> value="Section #" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Section #
                            </td>
                            <td>
                                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Section Heading".',') !== FALSE) { echo " checked"; } ?> value="Section Heading" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Section Heading
                            </td>
                            <td>
                                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Sub Section #".',') !== FALSE) { echo " checked"; } ?> value="Sub Section #" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Sub Section #
                            </td>
                            <td>
                                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Sub Section Heading".',') !== FALSE) { echo " checked"; } ?> value="Sub Section Heading" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Sub Section Heading
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Third Tier Section #".',') !== FALSE) { echo " checked"; } ?> value="Third Tier Section #" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Third Tier Section #
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Third Tier Heading".',') !== FALSE) { echo " checked"; } ?> value="Third Tier Heading" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Third Tier Heading
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { echo " checked"; } ?> value="Detail" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Detail
                            </td>

                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Document".',') !== FALSE) { echo " checked"; } ?> value="Document" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Document
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Link".',') !== FALSE) { echo " checked"; } ?> value="Link" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Link
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { echo " checked"; } ?> value="Videos" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Videos
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Signature box".',') !== FALSE) { echo " checked"; } ?> value="Signature box" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Signature box
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { echo " checked"; } ?> value="Comments" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Comments
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Staff
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Review Deadline".',') !== FALSE) { echo " checked"; } ?> value="Review Deadline" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Review Deadline
                            </td>

                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Status
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Form".',') !== FALSE) { echo " checked"; } ?> value="Form" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Form
                            </td>
                        </tr>
                    </table></div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_email" >
                        Send Email on Comment<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_email" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Send Email on Comment:</label>
                    <div class="col-sm-8">
                      <input name="manual_policy_pro_email" value="<?php echo get_config($dbc, 'manual_policy_pro_email'); ?>" type="text" class="form-control">
                    </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_max" >
                        Max Selection<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_max" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Max Section #:<br><em>(add only digits)</em></label>
                    <div class="col-sm-8">
                      <input name="policy_pro_max_section" value="<?php echo get_config($dbc, 'policy_pro_max_section'); ?>" type="text" class="form-control">
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Max Sub Section #:<br><em>(add only digits)</em></label>
                    <div class="col-sm-8">
                      <input name="policy_pro_max_subsection" value="<?php echo get_config($dbc, 'policy_pro_max_subsection'); ?>" type="text" class="form-control">
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Max Third Tier Section #:<br><em>(add only digits)</em></label>
                    <div class="col-sm-8">
                      <input name="policy_pro_max_thirdsection" value="<?php echo get_config($dbc, 'policy_pro_max_thirdsection'); ?>" type="text" class="form-control">
                    </div>
                    </div>

                </div>
            </div>
        </div>


        <?php if (strpos($value_config, ','."Form".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_forms" >
                        Select Form<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_forms" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { echo " checked"; } ?> value="Field Level Hazard Assessment" style="height: 20px; width: 20px;" name="safety[]">&nbsp;&nbsp;Field Level Hazard Assessment


                </div>
            </div>
        </div>


        <?php  } ?>

        <?php if (strpos($value_config, ','."Field Level Hazard Assessment".',') !== FALSE) { ?>

        <?php
        $assessment_option = get_config($dbc, 'form_field_level_risk_assessment');
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_hazard" >
                        Field Level Hazard Assessment<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_hazard" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option122,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option122">Fill Daily

                    <br><br>

                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option118,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option118">Date
                    &nbsp;&nbsp;
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option119,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option119">Job
                    &nbsp;&nbsp;
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option120,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option120">Contact
                    &nbsp;&nbsp;
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option121,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option121">Job Location
                    <br><br>
                    PERMITS/PLANS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox" <?php if (strpos(','.$assessment_option.',', ',assessment_option1,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option1">Hot Work/Cold Work</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option2,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option2">Confined Space</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option3,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option3">Demolition</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option108,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option108">Ground Disturbance</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option4,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option4">Excavation</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option5,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option5">Lockout</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option6,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option6">Critical Lift Plan</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option7,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option7">Fall Protection Plan</li>
                    </ul>

                    PERMIT IDENTIFIED HAZARDS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option8,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option8">Hazards Detailed on Safe Work Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option9,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option9">Hazards on Critical Lift Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option10,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option10">Hazards on Electrical Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option11,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option11">Hazards Identified for Confined Space Entry</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option12,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option12">Hazards on Confined Space Entry Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option13,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option13">Hazards on Hot/Cold Work Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option14,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option14">Hazards on Underground/ Excavation, Permit</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option15,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option15">Hazards on Line Opening Permit</li>
                    </ul>

                    EMERGENCY EQUIPMENT
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option16,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option16">Fire Extinguisher</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option17,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option17">Eyewash/Shower</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option109,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option109">All Conditions Met</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option18,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option18">Extraction Equipment</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option19,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option19">Permit Displayed</li>
                        <li>Alarm#</li>
                    </ul>

                    OVERHEAD OR WORKING AT HEIGHT HAZARDS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option20,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option20">Harness Required/Appropriate Tie-off identified</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option21,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option21">Others Working Overhead/Below</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option22,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option22">Hoisting or moving loads overhead</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option23,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option23">Falls from Height</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option24,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option24">Hoisting or moving Loads Overhead/Around Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option110,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option110">Use of Scaffolds</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option25,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option25"><?= TASK_TILE ?> Require You to Work Above Your Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option26,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option26">Objects / Debris Falling from Above</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option27,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option27">Overhead Power Line</li>
                    </ul>

                    EQUIPMENT HAZARDS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option28,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option28">Operating Power Equipment</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option29,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option29">Operating Motor Vehicle / Heavy Equipment</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option30,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option30">Contact with/contact by</li>
                        <li>Working with:</li>
                        <li>
                            <ul style="list-style-type: none;">
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option31,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option31">Saws</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option32,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option32">Cutting Torch Equipment</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option33,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option33">Hand Tools</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option34,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option34">Grinders</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option35,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option35">Welding Machines</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option36,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option36">Cranes</li>
                            </ul>
                        </li>
                    </ul>

                    WORK ENVIRONMENT HAZARDS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option37,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option37">Weather Conditions</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option38,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option38">Slips or Trips Possible</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option39,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option39">Waste Material Generated Performing Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option40,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option40">Limited Access / Egress</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option41,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option41">Foreign Bodies in Eyes</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option42,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option42">Exposure to Energized Electrical Systems</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option43,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option43">Lighing Levels Too High/Too Low</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option44,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option44">Position of Fingers / Hands - Pinch Points</li>

                        <li>Exposure to:</li>
                        <li>
                            <ul style="list-style-type: none;">
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option45,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option45">Chemicals</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option46,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option46"> Dust/Particulates</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option47,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option47">Extreme Heat/Cold</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option48,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option48">Reactive Chemicals</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option49,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option49">Sharp Objects / Edges</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option50,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option50">Noise</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option51,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option51">Odors</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option52,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option52">Steam</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option53,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option53">Fogging of Monogoggles / Bye Protection</li>
                                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option54,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option54">Flammable gases / Atmospheric hazards</li>
                            </ul>
                        </li>
                    </ul>

                    PERSONAL LIMITATIONS/HAZARDS
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option55,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option55">Procedure Not Available for Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option56,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option56">Confusing Instructions</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option57,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option57">No Training in Procedure / Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option58,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option58">No Training in Tools to be Used</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option59,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option59">First Time Performing This Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option60,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option60">Mental Limitations / Distractions / Loss of Focus</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option61,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option61">Not Physically Able to Perform Task</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option62,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option62">Complacency</li>
                    </ul>

                    WELDING
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option63,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option63">Shields</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option64,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option64">Fire Blankets</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option65,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option65">Fire Extinguisher</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option66,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option66">Cylinder Secured / Secure Connections</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option67,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option67">Cylinder Caps On</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option68,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option68">Flashback Arrestor</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option69,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option69">Combustibles Moved</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option70,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option70">Sparks Contained</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option71,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option71">Ground within 18 inch</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option72,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option72">Fire Watch / Spark Watch</li>
                    </ul>

                    PHYSICAL HAZARDS
                    <ul style="list-style-type: none;">
                        Manual Lifting
                        <li><input type="checkbox" <?php if (strpos(','.$assessment_option.',', ',assessment_option73,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option73">Load Too Heavy / Awkward to Lift</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option74,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option74">Over Reaching</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option75,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option75">Prolonged / Extreme Bending</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option76,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option76">Repetitive Motions</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option77,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option77">Unstable Position</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option78,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option78">Part(s) of Body in Line of Fire</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option79,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option79">Hands Not in Line of Sight</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option80,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option80">Working in Tight Clearances</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option81,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option81">Physical Limitation - Need Assistance</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option82,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option82">Uncontrolled Release of Energy / Force</li>
                        <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option83,') !== FALSE) { echo " checked"; } ?> name="assessment_option[]" value="assessment_option83">Fall Potential</li>
                    </ul>

                    PERSONAL PROTECTIVE EQUIPMENT
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option84,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option84">Work Gloves</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option85,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option85">Chemical Gloves</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option86,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option86">Kevlar Gloves</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option87,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option87">Rain Gear</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option88,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option88">Thermal Suits</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option89,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option89">Rubber Boots</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option90,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option90">Monogoggles/Faceshield</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option91,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option91">Safety Glasses</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option92,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option92">Respiratory Protection</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option93,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option93">Hearing Protection</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option94,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option94">Safety Harness/Lanyard/Lifeline</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option95,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option95">Head Protection</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option96,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option96">Steel-toed Work Boots</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option97,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option97">Hi-Vis Vest</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option98,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option98">Fire Retardant Wear</li>
                    </ul>

                    WALK AROUND/INSPECTION
                    <ul style="list-style-type: none;">
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option99,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option99">Leaks</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option100,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option100">Oil</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option101,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option101">Fuel</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option102,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option102">Tires</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option103,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option103">Lights</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option104,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option104">Windows</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option105,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option105">Hoses</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option106,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option106">Alarms</li>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option107,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option107">Bolts</li>
                    </ul>

                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option111,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option111">Is this worker working alone?
                    <br>
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option112,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option112">Task(s)
                    <br>

                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option113,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option113">Cliean  Up /  Close  Out-  Job Completion
                    <br>

                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option114,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option114">Worker Name
                    &nbsp;&nbsp;
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option115,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option115">Worker Signature
                    <br>
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option116,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option116">Foreman Name &nbsp;&nbsp;
                    <input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option117,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option117">Foreman Signature


                </div>
            </div>
        </div>
        <?php } ?>

    </div>

<?php
    $category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM manuals WHERE deleted=0 AND manual_type='safety' LIMIT 1"));
    $manual_category = $category['category'];
    if($manual_category == '') {
       $manual_category = 0;
    }
?>
<div class="form-group">
    <div class="col-sm-4 clearfix">
        <!--<a href="safety.php?category=<?php //echo $manual_category; ?>" class="btn brand-btn pull-right">Back</a>-->
		<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
    </div>
    <div class="col-sm-8">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>