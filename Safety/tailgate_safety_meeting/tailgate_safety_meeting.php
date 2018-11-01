<?php
/*
Add	Sheet
*/
include ('../database_connection.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
?>
<style>
.form-control {
    width: 40%;
    display: inline;
}
</style>
<script type="text/javascript">
	$(document).ready(function(){
        $("#form1").submit(function( event ) {
            var jobid = $("#jobid").val();
            var contactid = $("input[name=contactid]").val();
            var job_location = $("input[name=location]").val();
            if (contactid == '' || job_location == '') {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
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
</script>
</head>
<body>

<?php
$today_date = date('Y-m-d');
$contactid = $_SESSION['contactid'];
$location = '';
$project_number = '';
$supervisor = '';
$item_discussed = '';
$fields = '';
$desc = '';
$all_task='';
if(!empty($_GET['formid'])) {
    $formid = $_GET['formid'];

    echo '<input type="hidden" name="fieldlevelriskid" value="'.$formid.'">';

    $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_tailgate_safety_meeting WHERE fieldlevelriskid='$formid'"));

    $today_date = $get_field_level['today_date'];
    $project_number = $get_field_level['project_number'];
    $contactid = $get_field_level['contactid'];
    $location = $get_field_level['location'];
    $supervisor = $get_field_level['supervisor'];
    $item_discussed = $get_field_level['item_discussed'];

    $desc = $get_field_level['desc'];
    $all_task = $get_field_level['all_task'];
    $fields = explode('**FFM**', $get_field_level['fields']);

}
?>

<?php
//$form_config = ','.get_config($dbc, 'safety_field_level_risk_assessment').',';
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$form'"));
$form_config = ','.$get_field_config['fields'].',';
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

                <?php if (strpos($form_config, ','."fields1".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Date/Time:</label>
                    <div class="col-sm-8">
                        <input type="text" name="today_date" value="<?php echo $today_date; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

				<?php if (strpos($form_config, ','."fields2".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Project/Job Number:</label>
                    <div class="col-sm-8">
                        <input name="project_number" value="<?php echo $project_number; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

                <?php if (strpos($form_config, ','."fields3".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Location of Work:</label>
                    <div class="col-sm-8">
                        <input name="location" value="<?php echo $location; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

				<?php if (strpos($form_config, ','."fields4".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Supervisor:</label>
                    <div class="col-sm-8">
                        <input name="supervisor" value="<?php echo $supervisor; ?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

				<?php if (strpos($form_config, ','."fields6".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Meeting Leader:</label>
                    <div class="col-sm-8">
                        <input type="text" name="fields_0" value="<?php echo $fields[0]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
				<?php if (strpos($form_config, ','."fields7".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Work Site:</label>
                    <div class="col-sm-8">
                    <input type="text" name="fields_1" value="<?php echo $fields[1]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
				<?php if (strpos($form_config, ','."fields8".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Brief Work Description:</label>
                    <div class="col-sm-8">
                    <input type="text" name="fields_2" value="<?php echo $fields[2]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
				<?php if (strpos($form_config, ','."fields9".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">SWP #:</label>
                    <div class="col-sm-8">
                    <input type="text" name="fields_3" value="<?php echo $fields[3]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <?php if (strpos($form_config, ','."fields5".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info1" >
                    Item Discussed<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info1" class="panel-collapse collapse">
            <div class="panel-body">

                  <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Item Discussed:</label>
                    <div class="col-sm-8">
                      <textarea name="item_discussed" rows="5" cols="50" class="form-control"><?php echo $item_discussed; ?></textarea>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($form_config, ','."fields10".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info15" >
                    Items To Check In Support Of Work Plan<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info15" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Safe Work Practices & Procedures reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[4] == 'Yes') { echo " checked"; } ?>  name="fields_4" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[4] == 'No') { echo " checked"; } ?>  name="fields_4" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[4] == 'N/A') { echo " checked"; } ?>  name="fields_4" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_5" value="<?php echo $fields[5]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Permits obtained & reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[6] == 'Yes') { echo " checked"; } ?>  name="fields_6" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[6] == 'No') { echo " checked"; } ?>  name="fields_6" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[6] == 'N/A') { echo " checked"; } ?>  name="fields_6" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_7" value="<?php echo $fields[7]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Housekeeping requirements reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[8] == 'Yes') { echo " checked"; } ?>  name="fields_8" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[8] == 'No') { echo " checked"; } ?>  name="fields_8" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[8] == 'N/A') { echo " checked"; } ?>  name="fields_8" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_9" value="<?php echo $fields[9]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Isolation required (Locked & Tagged)</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[10] == 'Yes') { echo " checked"; } ?>  name="fields_10" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[10] == 'No') { echo " checked"; } ?>  name="fields_10" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[10] == 'N/A') { echo " checked"; } ?>  name="fields_10" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_11" value="<?php echo $fields[11]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Overhead hazards identified</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[12] == 'Yes') { echo " checked"; } ?>  name="fields_12" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[12] == 'No') { echo " checked"; } ?>  name="fields_12" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[12] == 'N/A') { echo " checked"; } ?>  name="fields_12" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_13" value="<?php echo $fields[13]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Hoisting requirements reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[14] == 'Yes') { echo " checked"; } ?>  name="fields_14" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[14] == 'No') { echo " checked"; } ?>  name="fields_14" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[14] == 'N/A') { echo " checked"; } ?>  name="fields_14" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_15" value="<?php echo $fields[15]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Barriers & signage reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[16] == 'Yes') { echo " checked"; } ?>  name="fields_16" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[16] == 'No') { echo " checked"; } ?>  name="fields_16" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[16] == 'N/A') { echo " checked"; } ?>  name="fields_16" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_17" value="<?php echo $fields[17]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">MSDS reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[18] == 'Yes') { echo " checked"; } ?>  name="fields_18" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[18] == 'No') { echo " checked"; } ?>  name="fields_18" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[18] == 'N/A') { echo " checked"; } ?>  name="fields_18" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_19" value="<?php echo $fields[19]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Respiratory protection reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[20] == 'Yes') { echo " checked"; } ?>  name="fields_20" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[20] == 'No') { echo " checked"; } ?>  name="fields_20" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[20] == 'N/A') { echo " checked"; } ?>  name="fields_20" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_21" value="<?php echo $fields[21]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">New worker training required</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[22] == 'Yes') { echo " checked"; } ?>  name="fields_22" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[22] == 'No') { echo " checked"; } ?>  name="fields_22" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[22] == 'N/A') { echo " checked"; } ?>  name="fields_22" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_23" value="<?php echo $fields[23]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Other PPE requirements reviewed</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[24] == 'Yes') { echo " checked"; } ?>  name="fields_24" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[24] == 'No') { echo " checked"; } ?>  name="fields_24" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[24] == 'N/A') { echo " checked"; } ?>  name="fields_24" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_25" value="<?php echo $fields[25]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Proper tools available / good condition</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[26] == 'Yes') { echo " checked"; } ?>  name="fields_26" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[26] == 'No') { echo " checked"; } ?>  name="fields_26" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[26] == 'N/A') { echo " checked"; } ?>  name="fields_26" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_27" value="<?php echo $fields[27]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Extreme weather conditions a factor</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[28] == 'Yes') { echo " checked"; } ?>  name="fields_28" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[28] == 'No') { echo " checked"; } ?>  name="fields_28" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[28] == 'N/A') { echo " checked"; } ?>  name="fields_28" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_29" value="<?php echo $fields[29]; ?>" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Environmental impacts / waste disposal</label>
                    <div class="col-sm-8">
                        <input type="radio" <?php if ($fields[30] == 'Yes') { echo " checked"; } ?>  name="fields_30" value="Yes">Yes&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[30] == 'No') { echo " checked"; } ?>  name="fields_30" value="No">No&nbsp;&nbsp;
                        <input type="radio" <?php if ($fields[30] == 'N/A') { echo " checked"; } ?>  name="fields_30" value="N/A">N/A&nbsp;&nbsp;
                        &nbsp;&nbsp;<input type="text" name="fields_31" value="<?php echo $fields[31]; ?>" class="form-control" />
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php } ?>


    <?php if (strpos($form_config, ','."fields11".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info16" >
                    Comments<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info16" class="panel-collapse collapse">
            <div class="panel-body">

                  <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Comments:</label>
                    <div class="col-sm-8">
                      <textarea name="desc" rows="5" cols="50" class="form-control"><?php echo $desc; ?></textarea>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($form_config, ','."fields12".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info17" >
                    Pre-Job Discussion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info17" class="panel-collapse collapse">
            <div class="panel-body">

               <?php
                $all_task_each = explode('**##**',$all_task);

                $total_count = mb_substr_count($all_task,'**##**');
                if($total_count > 0) {
                    echo "<table class='table table-bordered'>";
                    echo "<tr class='hidden-xs hidden-sm'>
                    <th>Priority</th>
                    <th>".TASK_TILE."</th>
                    <th>Hazards</th>
                    <th>Controls</th>
					<th>Risk Level</th>";
                }
                for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                    $task_item = explode('**',$all_task_each[$client_loop]);
                    $task = $task_item[0];
                    $hazard = $task_item[1];
                    $level = $task_item[2];
                    $plan = $task_item[3];
					$plan1 = $task_item[4];
                    if($task != '') {
                        echo '<tr>';
                        echo '<td data-title="Email">' . $task . '</td>';
                        echo '<td data-title="Email">' . $hazard . '</td>';
                        echo '<td data-title="Email">' . $level . '</td>';
                        echo '<td data-title="Email">' . $plan . '</td>';
						echo '<td data-title="Email">' . $plan1 . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
                ?>
                <div class="additional_hazard clearfix">
                    <div class="row">
                        <div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p>Priority</p>
                            <input type="text" name="task[]" class="task_list"/>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p><?= TASK_TILE ?></p>
                            <input type="text" name="hazard[]" class="task_list"/>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p>Hazards</p>
                            <input type="text" name="hazard1[]" class="task_list"/>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p>Controls</p>
                            <input type="text" name="hazard_plan[]" class="task_list"/>
                        </div>
						<div class="col-md-2 col-sm-6 col-xs-6 padded">
                            <p>Risk Level</p>
                            <select name="hazard_plan1[]" class="task_list">
                                <option value="High">High</option>
                                <option value="Med">Med</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="add_here_new_hazard"></div>
                <div class="form-group triple-gapped clearfix">
                    <div class="col-sm-offset-4 col-sm-8">
                        <button id="add_row_hazard" class="btn brand-btn pull-left">Add More</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php } ?>


    <?php if (strpos($form_config, ','."fields13".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info18" >
                    Other Discussion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_info18" class="panel-collapse collapse">
            <div class="panel-body">

				<?php if (strpos($form_config, ','."fields13".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Location of Emergency Assembly Area:</label>
                    <div class="col-sm-8">
                    <input type="text" name="fields_32" value="<?php echo $fields[32]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
				<?php if (strpos($form_config, ','."fields14".',') !== FALSE) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Emergency Response Plan Reviewed:</label>
                    <div class="col-sm-8">
                    <input type="text" name="fields_33" value="<?php echo $fields[33]; ?>" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

                <?php if (strpos($form_config, ','."fields15".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Have all personnel received orientation to the work area</label>
                    <div class="col-sm-8"><input type="checkbox"  <?php if ($fields[34] == 'Yes') { echo " checked"; } ?>  name="fields_34" value="Yes">
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
    <?php } ?>

    <?php if(!empty($_GET['formid'])) {
    	$sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$formid' AND safetyid='$safetyid'");
        $sa_inc=  0;
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_sa = $row_sa['assign_staff'];
            $assign_staff_id = $row_sa['safetyattid'];
            $assign_staff_done = $row_sa['done'];
            ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sa<?php echo $sa_inc;?>" >
                    <?php echo $assign_staff_sa; ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_sa<?php echo $sa_inc;?>" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                if($assign_staff_done == 0) { ?>

                <?php if (strpos($assign_staff_sa, 'Extra') !== false) { ?>
                   <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Name:</label>
                    <div class="col-sm-8">
                        <input name="assign_staff_<?php echo $assign_staff_id;?>" type="text" class="form-control" />
                    </div>
                  </div>
                <?php } ?>

               <div class="form-group">
                <label for="business_street" class="col-sm-4 control-label">Employer/Initial:</label>
                <div class="col-sm-8">
                    <input name="staffcheck_<?php echo $assign_staff_id;?>[]" type="text" class="form-control" />
                </div>
              </div>

            <?php $output_name = 'sign_'.$assign_staff_id;
            include('../phpsign/sign_multiple.php'); ?>

            <?php } else {
                echo '<img src="tailgate_safety_meeting/download/safety_'.$assign_staff_id.'.png">';
            } ?>

            </div>
        </div>
    </div>
    <?php $sa_inc++;
        }
    } ?>

</div>