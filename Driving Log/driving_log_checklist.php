<style>
.checklist-checkbox {
    width: 20%;
    text-align: center;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    $('[name="submit_checklist"]').on('click', function() {
        if ($('[name="checklist_status"]').val() == 'Done') {
            var safety = '';
            for(safety = 1; safety<=38; safety+=2) {
                if (!$("input[name='safety"+safety+"']:checked").val()) {
                    alert("Please make sure you have checked off all Presafety Inspection fields.");
                    return false;
                }
            }
            if ($("input[name=begin_odo_kms]").val() == '') {
                alert("Please make sure you have filled out the Beginning Odometer Kilometres field.");
                return false;
            }
            if ($("input[name=location_of_presafety]").val() == '') {
                alert("Please make sure you have filled out the Location of Pre-Trip Inspection field.");
                return false;
            }
            if (!$("#accept_policy").is(":checked")) {
                alert("Please make sure you have checked off the policy section for the Safety Inspection.");
                return false;
            }
            if (!$("#accept_policy2").is(":checked")) {
                alert("Please make sure you have checked off the policy section for the Safety Inspection.");
                return false;
            }
            startDrivingStatus();
        } else {
            var safety = '';
            for(safety = 2; safety<=38; safety+=2) {
                if (!$("input[name='safety"+safety+"']:checked").val()) {
                    alert("Please make sure you've checked off all Post Safety Inspections.");
                    return false;
                }
            }
            if ($("input[name=final_odo_kms]").val() == '') {
                alert("Please make sure you have filled out the Final Kilometres field.");
                return false;
            }
            if ($("input[name=location_of_postsafety]").val() == '') {
                alert("Please make sure you have filled out the Location of Pre-Trip Inspection field.");
                return false;
            }
            if (!$("#accept_policy").is(":checked")) {
                alert("Please make sure you have checked off the Policy of Safety Inspection.");
                return false;
            }
        }
    });
});
function chooseVehicle(page_query) {
    if ($('[name="vehicleid"]').val() == '0') {
        alert ('Please make sure you have selected a vehicle.');
    } else if ($('[name="current_vehicleid"]').val() == $('[name="vehicleid"]').val() && $('[name="current_trailerid"]').val() == $('[name="trailerid"]').val() && $('[name="checklist_status"]').val() == 'Not Done') {
        startDrivingStatus();
        $('#active_status').val('driving_timer');
        window.location.href = page_query;
    } else {
        $('#safety_checklist').show();
        $('#choose_vehicle').hide();
    }
}
function backChooseVehicle() {
    $('#safety_checklist').hide();
    $('#choose_vehicle').show();
}
function startDrivingStatus() {
    var dl_comment = '';
    var url_hash = '#'+$('#active_status').val();
    var activeTimer = url_hash;

    if(activeTimer == '#off_duty_timer'){
        if($('#dl_off_comment').val() != '') {
            dl_comment = $('#dl_off_comment').val();
        } else {
            dl_comment = 'Off-duty time';
        }
    }
    else if(activeTimer == '#on_duty_timer'){
        if($('#dl_on_comment').val() != '') {
            dl_comment = $('#dl_on_comment').val();
        } else {
            dl_comment = 'On-duty time';
        }
    }
    else if(activeTimer == '#sleeper_berth_timer'){
        if($('#dl_sleep_comment').val() != '') {
            dl_comment = $('#dl_sleep_comment').val();
        } else {
            dl_comment = 'Sleeper time';
        }
    }
    else if(activeTimer == '#driving_timer'){
        if($('#dl_driving_comment').val() != '') {
            dl_comment = $('#dl_driving_comment').val();
        } else {
            dl_comment = 'Driving time';
        }
    }
    $('.driving').find('li').addClass('active blue');

    $('.dl_comment').hide();
    $('#dl_driving_comment').show();
    $('#dl_driving_comment_i').show();
    $('.off_duty').parent('div').removeAttr('style');
    $('.sleeper_berth').parent('div').removeAttr('style');
    $('.driving').parent('div').attr('style', 'pointer-events: none');
    $('.on_duty').parent('div').removeAttr('style');

    $('.off_duty').find('li').removeClass('active blue');
    $('.sleeper_berth').find('li').removeClass('active blue');
    $('.on_duty').find('li').removeClass('active blue');

    $('.end_of_day').show();
    $('.amendments').show();
    var url_hash = '#'+$('#active_status').val();
    parent.location.hash = '';

    document.location.hash = "driving_timer";
    $('#driving_timer').parent('text').show();
    $(url_hash).parent('text').hide();

    hasTimer = true;

    var timer_value = $(url_hash).text();
    console.log(timer_value);
    $(url_hash).timer('remove');
    //$('#driving_timer').timer('resume');
    $('#driving_timer').timer({
        seconds: 0 //Specify start time in seconds
    });
    var timer_name = url_hash.substring(1, url_hash.length);
    var drivinglogid = $('#drivinglogid').val();
    dl_comment = encodeURIComponent(dl_comment);
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "driving_log_ajax_all.php?fill=drivinglog&drivinglogid="+drivinglogid+"&timer_name="+timer_name+"&time="+timer_value+"&current_timer=driving_timer&dl_comment="+dl_comment,
        dataType: "html",   //expect html to be returned
        success: function(response) {
            $('.dl_comment').val('');
        }
    });
}
</script>

<?php
    $drivinglogid = $_GET['drivinglogid'];
    if (!empty($_GET['safetyinspectid'])) {
        $safetyinspectid = $_GET['safetyinspectid'];
        $get_sa = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `driving_log_safety_inspect` WHERE `safetyinspectid` = '$safetyinspectid'"));
    } else {
        $get_sa = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `driving_log_safety_inspect` WHERE `drivinglogid` = '$drivinglogid' ORDER BY `safetyinspectid` DESC"));
    }
    $safetyinspectid = $get_sa['safetyinspectid'];
    $safety_inspect_driverid = $get_sa['safety_inspect_driverid'];
    $safety_inspect_vehicleid = $get_sa['safety_inspect_vehicleid'];
    $safety_inspect_trailerid = $get_sa['safety_inspect_trailerid'];
    $inspect_date = $get_sa['inspect_date'];
    $begin_odo_kms = $get_sa['begin_odo_kms'];
    $final_odo_kms = $get_sa['final_odo_kms'];
    $begin_hours = $get_sa['begin_hours'];
    $final_hours = $get_sa['final_hours'];
    $location_of_presafety = $get_sa['location_of_presafety'];
    $location_of_postsafety = $get_sa['location_of_postsafety'];
    $safety1 = $get_sa['safety1'];
    $safety2 = $get_sa['safety2'];
    $safety3 = $get_sa['safety3'];
    $safety4 = $get_sa['safety4'];
    $safety5 = $get_sa['safety5'];
    $safety6 = $get_sa['safety6'];
    $safety7 = $get_sa['safety7'];
    $safety8 = $get_sa['safety8'];
    $safety9 = $get_sa['safety9'];
    $safety10 = $get_sa['safety10'];
    $safety11 = $get_sa['safety11'];
    $safety12 = $get_sa['safety12'];
    $safety13 = $get_sa['safety13'];
    $safety14 = $get_sa['safety14'];
    $safety15 = $get_sa['safety15'];
    $safety16 = $get_sa['safety16'];
    $safety17 = $get_sa['safety17'];
    $safety18 = $get_sa['safety18'];
    $safety19 = $get_sa['safety19'];
    $safety20 = $get_sa['safety20'];
    $safety21 = $get_sa['safety21'];
    $safety22 = $get_sa['safety22'];
    $safety23 = $get_sa['safety23'];
    $safety24 = $get_sa['safety24'];
    $safety25 = $get_sa['safety25'];
    $safety26 = $get_sa['safety26'];
    $safety27 = $get_sa['safety27'];
    $safety28 = $get_sa['safety28'];
    $safety29 = $get_sa['safety29'];
    $safety30 = $get_sa['safety30'];
    $safety31 = $get_sa['safety31'];
    $safety32 = $get_sa['safety32'];
    $safety33 = $get_sa['safety33'];
    $safety34 = $get_sa['safety34'];
    $safety35 = $get_sa['safety35'];
    $safety36 = $get_sa['safety36'];
    $safety37 = $get_sa['safety37'];
    $safety38 = $get_sa['safety38'];
    $repair_note = $get_sa['repair_note'];

    if (!empty($begin_odo_kms) && empty($final_odo_kms)) {
        $checklist_status = 'Not Done';
        $disabled = ' disabled';
    } else {
        $checklist_status = 'Done';
        $disabled2 = ' disabled';
        if (((!empty($begin_odo_kms) && !empty($final_odo_kms)) || (empty($begin_odo_kms) && empty($final_odo_kms))) && empty($_GET['safetyinspectid'])) {
            $safetyinspectid = '';
            $safety_inspect_driverid = '';
            $safety_inspect_vehicleid = '';
            $safety_inspect_trailerid = '';
            $inspect_date = '';
            $begin_odo_kms = '';
            $final_odo_kms = '';
            $location_of_presafety = '';
            $location_of_postsafety = '';
            $safety1 = '';
            $safety2 = '';
            $safety3 = '';
            $safety4 = '';
            $safety5 = '';
            $safety6 = '';
            $safety7 = '';
            $safety8 = '';
            $safety9 = '';
            $safety10 = '';
            $safety11 = '';
            $safety12 = '';
            $safety13 = '';
            $safety14 = '';
            $safety15 = '';
            $safety16 = '';
            $safety17 = '';
            $safety18 = '';
            $safety19 = '';
            $safety20 = '';
            $safety21 = '';
            $safety22 = '';
            $safety23 = '';
            $safety24 = '';
            $safety25 = '';
            $safety26 = '';
            $safety27 = '';
            $safety28 = '';
            $safety29 = '';
            $safety30 = '';
            $safety31 = '';
            $safety32 = '';
            $safety33 = '';
            $safety34 = '';
            $safety35 = '';
            $safety36 = '';
            $safety37 = '';
            $safety38 = '';
            $repair_note = '';
        }
        if (!empty($begin_odo_kms) && !empty($final_odo_kms)) {
            $disabled = ' disabled';
        }
    }
?>

<input type="hidden" name="current_vehicleid" value="<?= $vehicleid ?>">
<input type="hidden" name="current_trailerid" value="<?= $trailerid ?>">
<input type="hidden" name="safetyinspectid" value="<?= $safetyinspectid ?>">
<input type="hidden" name="checklist_status" value="<?= $checklist_status ?>">

<div id="choose_vehicle" class="form-horizontal" style="<?= !empty($_GET['safetyinspectid']) || !empty($_GET['endofday']) ? 'display: none;' : '' ?>height: 100%; padding: 0.5em;">
    <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn hide-titles-mob allow_view_only">Back</a>
    <div class="clearfix"></div><br />
    <div class="form-group">
        <label for="travel_task" class="col-sm-4 control-label">Vehicles:</label>
        <div class="col-sm-8">
            <select name="vehicleid" data-placeholder="Choose a Unit Number..." id="vehicleid" class="vehicler chosen-select-deselect form-control" width="380">
                <option value='0'></option>
                <?php
                $result = mysqli_query($dbc, "SELECT * FROM equipment WHERE category LIKE '%Truck%' OR category LIKE '%Vehicle%'  ");
                while($row = mysqli_fetch_assoc($result)) {
                    if ($vehicleid == $row['equipmentid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." data-hours='".$row['hours_operated']."' data-kms='".$row['mileage']."' value = '".$row['equipmentid']."'>#".$row['unit_number']."</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="travel_task" class="col-sm-4 control-label">Trailer:</label>
        <div class="col-sm-8">
            <select id="trailerid" data-placeholder="Choose a Unit Number..." name="trailerid" class="chosen-select-deselect form-control" width="380">
                <option value='0'></option>
                <?php
                $result = mysqli_query($dbc, "SELECT * FROM equipment WHERE category LIKE '%Trailer%'");
                while($row = mysqli_fetch_assoc($result)) {
                    if ($trailerid == $row['equipmentid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value = '".$row['equipmentid']."'>#".$row['unit_number']."</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <button name="choose_vehicle" class="btn brand-btn mobile-block pull-right" onclick="chooseVehicle('?<?php echo http_build_query($page_query); ?>'); return false;">Next</button>
</div>

<div id="safety_checklist" class="form-horizontal" style="<?= empty($_GET['safetyinspectid']) && empty($_GET['endofday']) ? 'display: none;' : '' ?>padding: 0.5em; height: 100%; overflow-y: auto;">
    <?php if(empty($_GET['safetyinspectid'])) { ?>
    <div class="form-group">    
        <?php if (empty($_GET['endofday'])) { ?>
        <button name="back_choose_vehicles" class="btn brand-btn mobile-block pull-left allow_view_only" onclick="backChooseVehicle(); return false;">Back</button>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
    <?php if (!empty($safetyinspectid) && $checklist_status == 'Not Done') { ?>
    <div class="form-group">
        <label class="control-label" style="color:red;">
            <?php if(!empty($_GET['endofday'])) { ?>
            * Please fill out Post-Trip Checklist before ending your day.
            <?php } else { ?>
            * Please fill out Post-Trip Checklist for previous Vehicle/Trailer before proceeding.
            <?php } ?>
        </label>
    </div>
    <?php } ?>
    <?php } else {
        if (!empty($_GET['timer'])) { ?>
        <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn hide-titles-mob allow_view_only">Back</a>
        <?php } ?>
    <div class="clearfix"></div><br />
    <?php } ?>

    <script>
    $(document).ready(function() {
        $('[name=vehicleid]').change(function() {
            $('.service_details').load('../Equipment/equipment_service_details.php?view='+this.value);
            $('[name=begin_odo_kms]').val($(this).find('option:selected').data('kms'));
            $('[name=begin_hours]').val($(this).find('option:selected').data('hours'));
        });
    });
    </script>
    <div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-8 service_details"><?php if($vehicleid > 0) {
        $_GET['view'] = $equip_id;
        include('../Equipment/equipment_service_details.php');
    } ?></div></div>
    <?php if (empty($_GET['safetyinspectid'])) { ?>
        <?php if ($checklist_status == 'Done') { ?>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Beginning Odometer Kilometres<span class="brand-color">*</span>:</label>
            <div class="col-sm-8">
                <input name="begin_odo_kms" type="text" value="<?php echo $begin_odo_kms; ?>" class="form-control title" />
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Beginning Operating Hours:</label>
            <div class="col-sm-8">
                <input name="begin_hours" type="text" value="<?php echo $begin_hours; ?>" class="form-control title" />
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Location of Pre-Trip Inspection<span class="brand-color">*</span>:</label>
            <div class="col-sm-8">
                <input name="location_of_presafety" type="text" value="<?php echo $location_of_presafety; ?>" class="form-control title" />
            </div>
        </div>
        <?php } else { ?>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Final Odometer Kilometres<span class="brand-color">*</span>:</label>
            <div class="col-sm-8">
                <input name="final_odo_kms" type="text" value="<?php echo $final_odo_kms; ?>" class="form-control title" />
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Final Operating Hours<span class="brand-color">*</span>:</label>
            <div class="col-sm-8">
                <input name="final_hours" type="text" value="<?php echo $final_hours; ?>" class="form-control title" />
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label for="title[]" class="col-sm-4 control-label">Location of Post-Trip Inspection<span class="brand-color">*</span>:</label>
            <div class="col-sm-8">
                <input name="location_of_postsafety" type="text" value="<?php echo $location_of_postsafety; ?>" class="form-control title" />
            </div>
        </div>
        <?php } ?>
    <?php } else { ?>
    <div class="form-group">
        <label for="title[]" class="col-sm-4 control-label">Location of Pre-Trip Inspection<span class="brand-color"></span>:</label>
        <div class="col-sm-8">
            <input name="location_of_presafety" type="text" value="<?php if($location_of_presafety == '') { echo "No location specified"; } else { echo $location_of_presafety; } ?>" class="form-control title" disabled />
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if ($checklist_status == 'Done') { ?>
    <div class="form-group">
        <label for="title[]" class="col-sm-4 control-label">Location of Post-Trip Inspection<span class="brand-color"></span>:</label>
        <div class="col-sm-8">
            <input name="location_of_postsafety" type="text" value="<?php if($location_of_postsafety == '') { echo "No location specified"; } else { echo $location_of_postsafety; } ?>" class="form-control title" disabled />
        </div>
    </div>
    <div class="clearfix"></div>
    <?php } ?>
    <?php } ?>

    <div id="no-more-tables col-sm-12">
        <table class="table table-bordered">
            <tr>
                <th class="checklist-checkbox">Pre-Trip</th>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <th class="checklist-checkbox">Post-Trip</th>
                <?php } ?>
                <th class="checklist-description">General</th>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety1 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety1" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety2 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety2" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">ENGINE OIL WITHIN ACCEPTABLE LIMITS</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety3 == 'Yes') { echo  'checked="checked"';} echo $disabled; ?> name="safety3" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety4 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety4" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TIRE TREAD AND SIDEWALLS SHOW NO DAMAGE</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety5 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety5" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety6 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety6" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TIRE BOLTS CHECKED (HAND TIGHT)</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety7 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety7" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety8 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety8" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TIRE INFLATION</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety9 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety9" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety10 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety10" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">WINDOWS CLEAN INSIDE AND OUTSIDE</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety11 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety11" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety12 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety12" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">HITCH AND PINS (GOOSENECK OR DROP HITCH)</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety13 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety13" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety14 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety14" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description"> HORN</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety15 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety15" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety16 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety16" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">EMERGENCY / INCIDENT REPORTING KITS AVAILABLE</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety17 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety17" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety18 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety18" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">FIRE EXTINGUISHER AVAILABLE</td>
            </tr>
            <tr>
                <tr>
                    <th class="checklist-checkbox">Pre-Trip</th>
                    <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                    <th class="checklist-checkbox">Post-Trip</th>
                    <?php } ?>
                    <th class="checklist-description">Engine On Criteria</th>
                </tr>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety19 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety19" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety20 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety20" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">HEADLIGHTS FUNCTION ON BOTH HI AND LO BEAM</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety21 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety21" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety22 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety22" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TURN SIGNALS FUNCTION</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety23 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety23" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety24 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety24" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">BRAKE LIGHTS FUNCTION INCLUDING TRAILER APPLICABLE</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety25 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety25" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety26 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety26" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">MIRRORS FUNCTION AND ARE CLEAN</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety27 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety27" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety28 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety28" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">STEERING MECHANISM AND FLUID</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety29 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety29" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety30 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety30" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">FLUID LEAKS</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety31 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety31" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety32 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety32" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">ANY NEW DAMAGE NOTED PRIOR TO USING THIS VEHICLE?</td>
            </tr>
            <tr>
                <tr>
                    <th class="checklist-checkbox">Pre-Trip</th>
                    <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                    <th class="checklist-checkbox">Post-Trip</th>
                    <?php } ?>
                    <th class="checklist-description">General</th>
                </tr>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety33 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety33" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety34 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety34" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">ALL TOOLS PRESENT AND FUNCTIONING PROPERLY</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety35 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety35" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety36 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety36" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TRUCK CLEAN INSIDE AND OUTSIDE</td>
            </tr>
            <tr>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety37 == 'Yes') { echo  'checked="checked"'; } echo $disabled; ?> name="safety37" value="Yes"></td>
                <?php if (($checklist_status == 'Not Done' && empty($_GET['safetyinspectid'])) || ($checklist_status == 'Done' && $_GET['safetyinspectid'])) { ?>
                <td class="checklist-checkbox"><input type="checkbox" style="height:20px;width:20px;" <?php if ($safety38 == 'Yes') { echo  'checked="checked"'; } echo $disabled2; ?> name="safety38" value="Yes"></td>
                <?php } ?>
                <td class="checklist-description">TRUCK FILLED WITH FUEL</td>
            </tr>
        </table>
    </div>

    <?php if ($checklist_status == 'Done' && empty($_GET['safetyinspectid'])) { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="site_name" style="font-size: 20px;"><input id="accept_policy2" type="checkbox" style="height: 25px; width: 25px;" name="accept_policy2" value=1>&nbsp; I performed an inspection of the vehicle noted above using the criteria set out in Schedule 1 of Part 2, NSC Standard 13 and as per sections 10(4) and 10(10) of Alberta’s Commercial Vehicle Safety Regulation, (AR 121/2009) and report the following.<span class="brand-color">*</span></label>
        </div>
    </div>
    <?php } ?>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">If anything needs repairing, please specify below:</label>
        <div class="col-sm-8">
            <?php if(!empty($_GET['safetyinspectid'])) {
                echo $repair_note;
            } else { ?>
            <textarea  name="repair_note" rows="5" cols="50" class="form-control"><?php echo $repair_note; ?></textarea>
            <?php } ?>
        </div>
    </div>
    <div class="clearfix"></div>

    <?php if (!empty($_GET['safetyinspectid'])) { ?>
    <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Pre Safety:</label>
        <div class="col-sm-8">
            <img src="download/presafety_<?php echo $get_sa['drivinglogid']; ?>_<?php echo $safetyinspectid; ?>.png" width="200"> 
        </div>
    </div>

    <?php if ($checklist_status == 'Done') { ?>
    <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Post Safety:</label>
        <div class="col-sm-8">
            <img src="download/postsafety_<?php echo $get_sa['drivinglogid']; ?>_<?php echo $safetyinspectid; ?>.png" width="200">
        </div>
    </div>
    <?php } ?>
    <?php } else { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="site_name" style="font-size: 20px;"><input id="accept_policy" type="checkbox" style="height: 25px; width: 25px;" name="accept_policy" value=1>&nbsp; I have personally inspected the vehicle above and have found it to be in the condition listed above, click the box to accept these terms.<span class="brand-color">*</span></label>
        </div>
    </div>

    <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Signature:</label>
        <div class="col-sm-8">
            <?php $output_name = 'dl_checklist_sig';
                echo include ('../phpsign/sign_multiple.php');
            ?>
        </div>
    </div>

    <button type="submit" name="submit_checklist" class="btn brand-btn mobile-block pull-right">Submit</button>
    <?php if (empty($_GET['endofday'])) { ?>
    <button name="back_choose_vehicles" class="btn brand-btn mobile-block pull-right allow_view_only" onclick="backChooseVehicle(); return false;">Back</button>
    <?php } ?>
    <?php } ?>
</div>