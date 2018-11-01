<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
}
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0])))); ?>
<div class="clearfix"></div>
<div id="head_dates">
	<h3><?= PROJECT_NOUN ?> Dates</h3>
	<div class="notice double-gap-top double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE: </span>Recorded here is the date the project was created, the date the project was started and the estimated completion date.</div>
		<div class="clearfix"></div>
	</div>
	<?php if (in_array("Dates Project Created Date",$value_config) !== FALSE) { ?>
	<div class="form-group">
		<label class="col-sm-4">Created Date:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<input name="created_date" value="<?php echo (empty($project['projectid']) ? date('Y-m-d') : $project['created_date']); ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="datepicker form-control"></p>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Dates Project Start Date",$value_config) !== FALSE) { ?>
	<div class="form-group">
		<label class="col-sm-4">Start Date:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<input name="start_date" value="<?php echo $project['start_date']; ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="datepicker form-control"></p>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information Followup",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Follow Up Date:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<input type="text" name="followup" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['followup'] ?>" class="datepicker form-control">
			</div>
		</div>
	<?php } ?>

	<?php if (in_array("Dates Deadline",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Deadline:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<input type="text" name="deadline" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['deadline'] ?>" class="datepicker form-control">
			</div>
		</div>
	<?php } ?>

	<?php if (in_array("Dates Estimate Completion Date",$value_config) !== FALSE) { ?>
	<div class="form-group">
		<label class="col-sm-4">Estimated Completion Date:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<input name="estimated_completed_date" value="<?php echo $project['estimated_completed_date']; ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="datepicker form-control"></p>
		</div>
	</div>

        <?php
            $number_of_days = $project['number_of_days'];
            $number_of_days_start_date = $project['number_of_days_start_date'];
        ?>

        <div class="form-group">
            <label class="col-sm-4">Total Number of Days:</label>
            <div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
                <input data-table="project" name="number_of_days" value="<?= $number_of_days; ?>" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="form-control" /></p>
            </div>
        </div>

        <div class="clearfix"></div>

        <?php
        if($number_of_days > 0) { ?>

        <div class="form-group">
            <label class="col-sm-4">Time Line Countdown:</label>
            <div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
                <?php
                    $now = time(); // or your date as well
                    $your_date = strtotime($number_of_days_start_date);
                    $datediff = $now - $your_date;

                    echo round($datediff / (60 * 60 * 24)).'/'.$number_of_days.' Days';
                ?>
            </div>
        </div>
        <?php } ?>

	<?php } ?>

	<?php if (in_array("Dates Effective Date",$value_config) !== FALSE) { ?>
	<div class="form-group">
		<label class="col-sm-4">Effective Date:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<input name="effective_date" value="<?php echo $project['effective_date']; ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="datepicker form-control"></p>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Dates Time Clock Start Date",$value_config) !== FALSE) { ?>
	<div class="form-group">
		<label class="col-sm-4">Time Clock Start Date:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<input name="time_clock_start_date" value="<?php echo $project['time_clock_start_date']; ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="datepicker form-control"></p>
		</div>
	</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>