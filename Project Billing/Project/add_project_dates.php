<?php
if(!empty($_GET['projectid'])) {
            $projectid = $_GET['projectid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project WHERE projectid='$projectid'"));
            $created_date = $get_contact['created_date'];
            $start_date = $get_contact['start_date'];
            $estimated_completed_date = $get_contact['estimated_completed_date'];
            $completion_date = $get_contact['completion_date'];
            $effective_date = $get_contact['effective_date'];
            $time_clock_start_date = $get_contact['time_clock_start_date'];

}?>

<?php if (strpos($value_config, ','."Dates Project Created Date".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Date <?php echo (PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)); ?> Created:</label>
    <div class="col-sm-8">
        <input name="created_date" value="<?php echo $created_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Dates Project Start Date".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Start Date:</label>
    <div class="col-sm-8">
        <input name="start_date" value="<?php echo $start_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Dates Estimate Completion Date".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Estimated Completion Date:</label>
    <div class="col-sm-8">
        <input name="estimated_completed_date" value="<?php echo $estimated_completed_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Dates Effective Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Effective Date:</label>
    <div class="col-sm-8">
        <input name="effective_date" value="<?php echo $effective_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Dates Time Clock Start Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Time Clock Start Date:</label>
    <div class="col-sm-8">
        <input name="time_clock_start_date" value="<?php echo $time_clock_start_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>
