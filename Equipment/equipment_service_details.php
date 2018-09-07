<?php include_once('../include.php');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['view'],FILTER_SANITIZE_STRING);
$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"));

$unit_number = $get_equipment['unit_number'];
$next_oil_filter_change_date = $get_equipment['next_oil_filter_change_date'];
$next_oil_filter_change = $get_equipment['next_oil_filter_change'];
$next_oil_filter_change_hrs = $get_equipment['next_oil_filter_change_hrs'];
$next_insp_tune_up_date = $get_equipment['next_insp_tune_up_date'];
$next_insp_tune_up = $get_equipment['next_insp_tune_up'];
$next_insp_tune_up_hrs = $get_equipment['next_insp_tune_up_hrs'];
$next_tire_rotation_date = $get_equipment['next_tire_rotation_date'];
$next_tire_rotation = $get_equipment['next_tire_rotation'];
$next_tire_rotation_hrs = $get_equipment['next_tire_rotation_hrs'];
$mileage = $get_equipment['mileage'];
$hours_operated = $get_equipment['hours_operated']; ?>
<div class="block-element" style="padding: 0.5em;">
    <h4>Equipment Unit #<?= $unit_number ?> Service Details</h4>
    <div class="hide-titles-mob">
        <label class="col-sm-3"></label>
        <label class="col-sm-3">Date</label>
        <label class="col-sm-3">Hours</label>
        <label class="col-sm-3">KMs</label>
        <div class="clearfix"></div>
    </div>
    <div class="">
        <label class="col-sm-3">Current Details:</label>
        <label class="col-sm-3"></label>
        <label class="col-sm-3"><label class="show-on-mob">Hours: </label><?= $hours_operated ?></label>
        <label class="col-sm-3"><label class="show-on-mob">KMs: </label><?= $mileage ?></label>
        <div class="clearfix"></div>
    </div>
    <div class="">
        <label class="col-sm-3">Oil Filter Change:</label>
        <label class="col-sm-3"><label class="show-on-mob">Date: </label><?= $next_oil_filter_change_date ?></label>
        <label class="col-sm-3"><label class="show-on-mob">Hours: </label><?= $next_oil_filter_change_hrs ?></label>
        <label class="col-sm-3"><label class="show-on-mob">KMs: </label><?= $next_oil_filter_change ?></label>
        <div class="clearfix"></div>
    </div>
    <div class="">
        <label class="col-sm-3">Inspection &amp; Tune-Up:</label>
        <label class="col-sm-3"><label class="show-on-mob">Date: </label><?= $next_insp_tune_up_date ?></label>
        <label class="col-sm-3"><label class="show-on-mob">Hours: </label><?= $next_insp_tune_up_hrs ?></label>
        <label class="col-sm-3"><label class="show-on-mob">KMs: </label><?= $next_insp_tune_up ?></label>
        <div class="clearfix"></div>
    </div>
    <div class="">
        <label class="col-sm-3">Tire Rotation:</label>
        <label class="col-sm-3"><label class="show-on-mob">Date: </label><?= $next_tire_rotation_date ?></label>
        <label class="col-sm-3"><label class="show-on-mob">Hours: </label><?= $next_tire_rotation_hrs ?></label>
        <label class="col-sm-3"><label class="show-on-mob">KMs: </label><?= $next_tire_rotation ?></label>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>