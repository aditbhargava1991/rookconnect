<?php
/*
Add	Sheet
*/
include ('../database_connection.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
?>
<script type="text/javascript">
	$(document).ready(function(){

        $("#form1").submit(function( event ) {
            var jobid = $("#jobid").val();
            var contactid = $("input[name=contactid]").val();
            var job_location = $("input[name=location]").val();
            if (contactid == '' || job_location == '') {
                alert("Please make sure you have filled in all of the required fields.");
                return false;
            }
        });

		var inc = 1;
        $('#add_row_hazard').on( 'click', function () {
            $(".hide_show_service").show();
            var clone = $('.additional_hazard').clone();
            clone.find('.task_list').val('');
            clone.removeClass("additional_hazard");
            $('#add_here_new_hazard').append(clone);
            inc++;
            return false;
        });
	});
$(document).on('change', 'select[name="jobid"]', function() { changeJob(this); });

function changeJob(sel) {
    var proValue = sel.value;
    var proId = sel.id;
    var arr = proId.split('_');

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "ajax_all.php?from=flha&name="+proValue,
        dataType: "html",   //expect html to be returned
        success: function(response){
            $("#location").val(response);
        }
    });
}
</script>
</head>
<body>

Check all hazards that may be present during the task(s)

<?php
$today_date = date('Y-m-d');
$jobid = '';
$contactid = '';
$location = '';
$assessment_option = '';
$working_alone = '';
$all_task = '';
$job_complete = '';
$worker_name = '';
$foreman_name = '';

    $form_by = $_SESSION['contactid'];
    $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM form_field_level_risk_assessment WHERE manualtypeid='$manualtypeid' AND contactid='$form_by' AND DATE(today_date) = CURDATE()"));

    if($get_field_level['fieldlevelriskid'] != '') {
        $today_date = $get_field_level['today_date'];
        $jobid = $get_field_level['jobid'];
        $contactid = $get_field_level['contactid'];
        $location = $get_field_level['location'];
        $assessment_option = $get_field_level['assessment_option'];
        $working_alone = $get_field_level['working_alone'];
        $all_task = $get_field_level['all_task'];
        $job_complete = $get_field_level['job_complete'];
        $worker_name = $get_field_level['worker_name'];
        $foreman_name = $get_field_level['foreman_name'];
    }
?>

<?php
$form_config = ','.get_config($dbc, 'form_field_level_risk_assessment').',';
?>

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                    Information<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="row">
                    <?php if (strpos($form_config, ','."assessment_option118".',') !== FALSE) { ?>
                    <div class="col-md-4 col-sm-6">
                        <p>Date:</p>
                        <input type="text" name="today_date" value="<?php echo $today_date; ?>"/>
                    </div>
                    <?php } ?>

                    <?php if (strpos($form_config, ','."assessment_option119".',') !== FALSE) { ?>
                    <div class="col-md-4 col-sm-6">
                        <p>Job:</p>
                        <select data-placeholder="Choose a Job..." name="jobid" id="jobid" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php
                            $query = mysqli_query($dbc,"SELECT sectionid FROM bid_section WHERE deleted=0 order by sectionid");
                            while($row = mysqli_fetch_array($query)) {
                                if ($jobid == $row['sectionid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['sectionid']."'>".$row['sectionid'].'</option>';
                            }
                          ?>
                        </select>
                    </div>
                    <?php } ?>

                </div>
                <div class="row">
                    <?php if (strpos($form_config, ','."assessment_option120".',') !== FALSE) { ?>
                    <div class="col-md-4 col-sm-6">
                        <p>Contact#:</p>
                        <input type="text" value="<?php echo $contactid; ?>" name="contactid"/><!--MAKE AUTO FILL TODAYS ON ADD NEW. -->
                    </div>
                    <?php } ?>
                    <?php if (strpos($form_config, ','."assessment_option121".',') !== FALSE) { ?>
                    <div class="col-md-8 col-sm-12">
                        <p>Job Location:</p>
                        <input type="text" value="<?php echo $location; ?>" name="location" id="location"/>
                    </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                    Permits/Plans<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_2" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option1".',') !== FALSE) { ?>
                <li><input type="checkbox" <?php if (strpos(','.$assessment_option.',', ',assessment_option1,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option1">&nbsp;&nbsp;Hot Work/Cold Work</li>
                <?php } ?>

                <?php if (strpos($form_config, ','."assessment_option2".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option2,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option2">&nbsp;&nbsp;Confined Space</li>
                <?php } ?>


                <?php if (strpos($form_config, ','."assessment_option3".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option3,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option3">&nbsp;&nbsp;Demolition</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option108".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option108,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option108">&nbsp;&nbsp;Ground Disturbance</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option4".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option4,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option4">&nbsp;&nbsp;Excavation</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option5".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option5,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option5">&nbsp;&nbsp;Lockout</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option6".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option6,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option6">&nbsp;&nbsp;Critical Lift Plan</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option7".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option7,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option7">&nbsp;&nbsp;Fall Protection Plan</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
                    Permit Identified Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_3" class="panel-collapse collapse">
            <div class="panel-body">
            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option8".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option8,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option8">&nbsp;&nbsp;Hazards Detailed on Safe Work Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option9".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option9,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option9">&nbsp;&nbsp;Hazards on Critical Lift Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option10".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option10,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option10">&nbsp;&nbsp;Hazards on Electrical Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option11".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option11,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option11">&nbsp;&nbsp;Hazards Identified for Confined Space Entry</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option12".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option12,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option12">&nbsp;&nbsp;Hazards on Confined Space Entry Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option13".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option13,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option13">&nbsp;&nbsp;Hazards on Hot/Cold Work Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option14".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option14,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option14">&nbsp;&nbsp;Hazards on Underground/ Excavation, Permit</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option15".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option15,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option15">&nbsp;&nbsp;Hazards on Line Opening Permit</li>
                <?php } ?>
            </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
                    Emergency Equipment<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_4" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option16".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option16,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option16">&nbsp;&nbsp;Fire Extinguisher</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option17".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option17,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option17">&nbsp;&nbsp;Eyewash/Shower</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option109".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option109,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option109">&nbsp;&nbsp;All Conditions Met</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option18".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option18,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option18">&nbsp;&nbsp;Extraction Equipment</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option19".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option19,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option19">&nbsp;&nbsp;Permit Displayed</li>
                <?php } ?>
                <li>Alarm#</li>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
                    Overhead Or Working At Height Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_5" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option20".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option20,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option20">&nbsp;&nbsp;Harness Required/Appropriate Tie-off identified</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option21".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option21,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option21">&nbsp;&nbsp;Others Working Overhead/Below</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option22".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option22,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option22">&nbsp;&nbsp;Hoisting or moving loads overhead</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option23".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option23,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option23">&nbsp;&nbsp;Falls from Height</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option24".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option24,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option24">&nbsp;&nbsp;Hoisting or moving Loads Overhead/Around Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option110".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option110,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option110">&nbsp;&nbsp;Use of Scaffolds</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option25".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option25,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option25">&nbsp;&nbsp;<?= TASK_TILE ?> Require You to Work Above Your Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option26".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option26,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option26">&nbsp;&nbsp;Objects / Debris Falling from Above</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option27".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option27,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option27">&nbsp;&nbsp;Overhead Power Line</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
                    Equipment Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_6" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option28".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option28,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option28">&nbsp;&nbsp;Operating Power Equipment</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option29".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option29,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option29">&nbsp;&nbsp;Operating Motor Vehicle / Heavy Equipment</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option30".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option30,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option30">&nbsp;&nbsp;Contact with/contact by</li>
                <?php } ?>
                <li>Working with:</li>
                <li>
                    <ul style="list-style-type: none;">
                        <?php if (strpos($form_config, ','."assessment_option31".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option31,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option31">&nbsp;&nbsp;Saws</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option32".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option32,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option32">&nbsp;&nbsp;Cutting Torch Equipment</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option33".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option33,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option33">&nbsp;&nbsp;Hand Tools</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option34".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option34,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option34">&nbsp;&nbsp;Grinders</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option35".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option35,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option35">&nbsp;&nbsp;Welding Machines</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option36".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option36,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option36">&nbsp;&nbsp;Cranes</li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
                    Work Environment Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_7" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option37".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option37,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option37">&nbsp;&nbsp;Weather Conditions</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option38".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option38,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option38">&nbsp;&nbsp;Slips or Trips Possible</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option39".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option39,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option39">&nbsp;&nbsp;Waste Material Generated Performing Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option40".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option40,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option40">&nbsp;&nbsp;Limited Access / Egress</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option41".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option41,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option41">&nbsp;&nbsp;Foreign Bodies in Eyes</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option42".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option42,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option42">&nbsp;&nbsp;Exposure to Energized Electrical Systems</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option43".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option43,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option43">&nbsp;&nbsp;Lighing Levels Too High/Too Low</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option44".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option44,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option44">&nbsp;&nbsp;Position of Fingers / Hands - Pinch Points</li>
                <?php } ?>

                <li>Exposure to:</li>
                <li>
                    <ul style="list-style-type: none;">
                        <?php if (strpos($form_config, ','."assessment_option45".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option45,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option45">&nbsp;&nbsp;Chemicals</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option46".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option46,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option46">&nbsp;&nbsp;Dust/Particulates</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option47".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option47,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option47">&nbsp;&nbsp;Extreme Heat/Cold</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option48".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option48,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option48">&nbsp;&nbsp;Reactive Chemicals</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option49".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option49,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option49">&nbsp;&nbsp;Sharp Objects / Edges</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option50".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option50,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option50">&nbsp;&nbsp;Noise</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option51".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option51,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option51">&nbsp;&nbsp;Odors</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option52".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option52,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option52">&nbsp;&nbsp;Steam</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option53".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option53,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option53">&nbsp;&nbsp;Fogging of Monogoggles / Bye Protection</li>
                        <?php } ?>
                        <?php if (strpos($form_config, ','."assessment_option54".',') !== FALSE) { ?>
                        <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option54,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option54">&nbsp;&nbsp;Flammable gases / Atmospheric hazards</li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
                    Personal Limitations/Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_8" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option55".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option55,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option55">&nbsp;&nbsp;Procedure Not Available for Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option56".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option56,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option56">&nbsp;&nbsp;Confusing Instructions</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option57".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option57,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option57">&nbsp;&nbsp;No Training in Procedure / Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option58".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option58,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option58">&nbsp;&nbsp;No Training in Tools to be Used</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option59".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option59,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option59">&nbsp;&nbsp;First Time Performing This Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option60".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option60,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option60">&nbsp;&nbsp;Mental Limitations / Distractions / Loss of Focus</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option61".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option61,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option61">&nbsp;&nbsp;Not Physically Able to Perform Task</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option62".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option62,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option62">&nbsp;&nbsp;Complacency</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                    Welding<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_9" class="panel-collapse collapse">
            <div class="panel-body">
            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option63".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option63,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option63">&nbsp;&nbsp;Shields</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option64".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option64,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option64">&nbsp;&nbsp;Fire Blankets</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option65".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option65,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option65">&nbsp;&nbsp;Fire Extinguisher</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option66".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option66,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option66">&nbsp;&nbsp;Cylinder Secured / Secure Connections</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option67".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option67,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option67">&nbsp;&nbsp;Cylinder Caps On</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option68".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option68,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option68">&nbsp;&nbsp;Flashback Arrestor</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option69".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option69,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option69">&nbsp;&nbsp;Combustibles Moved</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option70".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option70,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option70">&nbsp;&nbsp;Sparks Contained</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option71".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option71,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option71">&nbsp;&nbsp;Ground within 18 inch</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option72".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option72,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option72">&nbsp;&nbsp;Fire Watch / Spark Watch</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
                    Physical Hazards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_10" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                Manual Lifting
                <?php if (strpos($form_config, ','."assessment_option73".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option73,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option73">&nbsp;&nbsp;Load Too Heavy / Awkward to Lift</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option74".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option74,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option74">&nbsp;&nbsp;Over Reaching</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option75".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option75,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option75">&nbsp;&nbsp;Prolonged / Extreme Bending</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option76".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option76,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option76">&nbsp;&nbsp;Repetitive Motions</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option77".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option77,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option77">&nbsp;&nbsp;Unstable Position</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option78".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option78,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option78">&nbsp;&nbsp;Part(s) of Body in Line of Fire</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option79".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option79,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option79">&nbsp;&nbsp;Hands Not in Line of Sight</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option80".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option80,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option80">&nbsp;&nbsp;Working in Tight Clearances</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option81".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option81,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option81">&nbsp;&nbsp;Physical Limitation - Need Assistance</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option82".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option82,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option82">&nbsp;&nbsp;Uncontrolled Release of Energy / Force</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option83".',') !== FALSE) { ?>
                <li><input type="checkbox"   <?php if (strpos(','.$assessment_option.',', ',assessment_option83,') !== FALSE) { echo " checked"; } ?>name="assessment_option[]" value="assessment_option83">&nbsp;&nbsp;Fall Potential</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
                    Personal Protective Equipment<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_11" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option84".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option84,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option84">&nbsp;&nbsp;Work Gloves</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option85".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option85,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option85">&nbsp;&nbsp;Chemical Gloves</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option86".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option86,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option86">&nbsp;&nbsp;Kevlar Gloves</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option87".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option87,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option87">&nbsp;&nbsp;Rain Gear</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option88".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option88,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option88">&nbsp;&nbsp;Thermal Suits</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option89".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option89,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option89">&nbsp;&nbsp;Rubber Boots</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option90".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option90,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option90">&nbsp;&nbsp;Monogoggles/Faceshield</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option91".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option91,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option91">&nbsp;&nbsp;Safety Glasses</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option92".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option92,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option92">&nbsp;&nbsp;Respiratory Protection</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option93".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option93,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option93">&nbsp;&nbsp;Hearing Protection</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option94".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option94,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option94">&nbsp;&nbsp;Safety Harness/Lanyard/Lifeline</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option95".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option95,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option95">&nbsp;&nbsp;Head Protection</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option96".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option96,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option96">&nbsp;&nbsp;Steel-toed Work Boots</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option97".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option97,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option97">&nbsp;&nbsp;Hi-Vis Vest</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option98".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option98,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option98">&nbsp;&nbsp;Fire Retardant Wear</li>
                <?php } ?>
            </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_12" >
                    Walk Around/Inspection<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_12" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <?php if (strpos($form_config, ','."assessment_option99".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option99,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option99">&nbsp;&nbsp;Leaks</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option100".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option100,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option100">&nbsp;&nbsp;Oil</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option101".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option101,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option101">&nbsp;&nbsp;Fuel</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option102".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option102,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option102">&nbsp;&nbsp;Tires</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option103".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option103,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option103">&nbsp;&nbsp;Lights</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option104".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option104,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option104">&nbsp;&nbsp;Windows</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option105".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option105,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option105">&nbsp;&nbsp;Hoses</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option106".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option106,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option106">&nbsp;&nbsp;Alarms</li>
                <?php } ?>
                <?php if (strpos($form_config, ','."assessment_option107".',') !== FALSE) { ?>
                <li><input type="checkbox"  <?php if (strpos(','.$assessment_option.',', ',assessment_option107,') !== FALSE) { echo " checked"; } ?>  name="assessment_option[]" value="assessment_option107">&nbsp;&nbsp;Bolts</li>
                <?php } ?>
            </ul>

            </div>
        </div>
    </div>

    <?php if (strpos($form_config, ','."assessment_option111".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_14" >
                    Is this worker working alone?<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_14" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <li><input type="radio" <?php if ($working_alone == 'Yes') { echo " checked"; } ?> name="working_alone" value="Yes">&nbsp;&nbsp;Yes</li>
                <li><input type="radio" <?php if ($working_alone == 'No') { echo " checked"; } ?> name="working_alone" value="No">&nbsp;&nbsp;No</li>
            </ul>

            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($form_config, ','."assessment_option112".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_13" >
                    Task(s)<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_13" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>
                    <th>Task</th>
                    <th>Hazard</th>
                    <th>Hazard Level</th>
                    <th>Plans To Eliminate/Control Risk</th>
                ";

                $all_task_each = explode('**##**',$all_task);

                $total_count = mb_substr_count($all_task,'**##**');
                for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                    $task_item = explode('**',$all_task_each[$client_loop]);
                    $task = $task_item[0];
                    $hazard = $task_item[1];
                    $level = $task_item[2];
                    $plan = $task_item[3];
                    if($task != '') {
                        echo '<tr>';
                        echo '<td data-title="Email">' . $task . '</td>';
                        echo '<td data-title="Email">' . $hazard . '</td>';
                        echo '<td data-title="Email">' . $level . '</td>';
                        echo '<td data-title="Email">' . $plan . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
                ?>
                <div class="additional_hazard clearfix">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-6 padded">
                            <p>Task(S)</p>
                            <input type="text" name="task[]" class="task_list"/>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-6 padded">
                            <p>Hazards</p>
                            <input type="text" name="hazard[]" class="task_list"/>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p>Hazard Level</p>
                            <select name="hazard_level[]" class="task_list">
                                <option value="1 HIGH">1 HIGH</option>
                                <option value="2 MEDIUM">2 MEDIUM</option>
                                <option value="3 LOW">3 LOW</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6 padded">
                            <p>Plans To  Eliminate/Control Risk</p>
                            <input type="text" name="hazard_plan[]" id="txtControlRisk" class="task_list"/>
                        </div>
                    </div>
                </div>
                <div id="add_here_new_hazard"></div>
                <div class="form-group triple-gapped clearfix">
                    <div class="col-sm-offset-4 col-sm-8">
                        <button id="add_row_hazard" class="btn brand-btn pull-left">Add Hazard</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($form_config, ','."assessment_option113".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_15" >
                    Cliean  Up /  Close  Out-  Job Completion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_15" class="panel-collapse collapse">
            <div class="panel-body">

            <ul style="list-style-type: none;">
                <li><input type="checkbox" <?php if (strpos(','.$job_complete.',', ',job_complete1,') !== FALSE) { echo " checked"; } ?> name="job_complete[]" value="job_complete1">&nbsp;&nbsp;Waste containers sealed, labeled  and dated?</li>
                <li><input type="checkbox" <?php if (strpos(','.$job_complete.',', ',job_complete2,') !== FALSE) { echo " checked"; } ?> name="job_complete[]" value="job_complete2">&nbsp;&nbsp;All tools / equipment removed from Task Location?</li>
                <li><input type="checkbox" <?php if (strpos(','.$job_complete.',', ',job_complete3,') !== FALSE) { echo " checked"; } ?> name="job_complete[]" value="job_complete3">&nbsp;&nbsp;Task area cleaned up at end of job / shift?</li>
            </ul>

            </div>
        </div>
    </div>
    <?php } ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
                    Worker Information<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_16" class="panel-collapse collapse">
            <div class="panel-body">
            All members of the work crew must sign card prior to commencing work at task location.

            <?php if (strpos($form_config, ','."assessment_option114".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Name:</label>
                <div class="col-sm-8">
                  <input name="worker_name" value="<?php echo $worker_name; ?>" type="text" class="form-control">
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($form_config, ','."assessment_option115".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Signature:</label>
                <div class="col-sm-8">
                  <?php include ('../phpsign/sign.php'); ?>
                </div>
              </div>
            <?php } ?>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
                    Foreman Information<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_17" class="panel-collapse collapse">
            <div class="panel-body">

            <?php if (strpos($form_config, ','."assessment_option116".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Name:</label>
                <div class="col-sm-8">
                  <input name="foreman_name" value="<?php echo $foreman_name; ?>" value="" type="text" class="form-control">
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($form_config, ','."assessment_option117".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="company_name" class="col-sm-4 control-label">Signature:</label>
                <div class="col-sm-8">
                  <?php include ('../phpsign/sign2.php'); ?>
                </div>
              </div>
            <?php } ?>

            </div>
        </div>
    </div>

</div>
