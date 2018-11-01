<?php include_once('../include.php');
$staff = $_SESSION['contactid'];
$start = '';
$end = '';
$hours = '';
$date = '';
$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(!empty($_POST['submit']) && $_GET['src'] == 'checklist') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING) * 3600;
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `checklist_name_time` SET `contactid`='$staff', `timer_date`='$date', `work_time`=SEC_TO_TIME('$hours') WHERE `checklist_time_id`='$id'");
} else if($_GET['src'] == 'checklist') {
	$timer = $dbc->query("SELECT *, TIME_TO_SEC(`work_time`) / 3600 `hours` FROM `checklist_name_time` WHERE `checklist_time_id`='$id'")->fetch_assoc();
	$staff = $timer['contactid'];
	$date = $timer['timer_date'];
	$hours = $timer['hours'];
} else if(!empty($_POST['submit']) && $_GET['src'] == 'tickets') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING) * 3600;
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `ticket_timer` SET `start_time`='$start',`end_time`='$end',`created_by`='$staff',`created_date`='$date',`timer`=SEC_TO_TIME($hours) WHERE `tickettimerid`='$id'");
} else if($_GET['src'] == 'tickets') {
	$timer = $dbc->query("SELECT *, TIME_TO_SEC(`timer`) / 3600 `hours` FROM `ticket_timer` WHERE `tickettimerid`='$id' AND `deleted` = 0")->fetch_assoc();
	$start = $timer['start_time'];
	$end = $timer['end_time'];
	$staff = $timer['created_by'];
	$date = $timer['created_date'];
	$hours = $timer['hours'];
} else if(!empty($_POST['submit']) && $_GET['src'] == 'ticket_attached') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `ticket_attached` SET `item_id`='$staff', `date_stamp`='$date', `hours_tracked`='$hours' WHERE `id`='$id'");
} else if($_GET['src'] == 'ticket_attached') {
	$timer = $dbc->query("SELECT * FROM `ticket_attached` WHERE `id`='$id'")->fetch_assoc();
	$staff = $timer['item_id'];
	$date = $timer['date_stamp'];
	$hours = $timer['hours_tracked'];
} else if(!empty($_POST['submit']) && $_GET['src'] == 'ticket_time_list') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING) * 3600;
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `ticket_time_list` SET `created_by`='$staff', `created_date`='$date', `time_length`=SEC_TO_TIME('$hours') WHERE `id`='$id'");
} else if($_GET['src'] == 'ticket_time_list') {
	$timer = $dbc->query("SELECT *, TIME_TO_SEC(`time_length`) / 3600 `hours` FROM `ticket_time_list` WHERE `id`='$id'")->fetch_assoc();
	$staff = $timer['created_by'];
	$date = $timer['created_date'];
	$hours = $timer['hours'];
} else if(!empty($_POST['submit']) && $_GET['src'] == 'tasklist') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING) * 3600;
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `tasklist` SET `contactid`='$staff', `task_tododate`='$date', `work_time`=SEC_TO_TIME('$hours') WHERE `tasklistid`='$id'");
} else if($_GET['src'] == 'tasklist') {
	$timer = $dbc->query("SELECT *, TIME_TO_SEC(`work_time`) / 3600 `hours` FROM `tasklist` WHERE `tasklistid`='$id'")->fetch_assoc();
	$staff = $timer['contactid'];
	$date = $timer['task_tododate'];
	$hours = $timer['hours'];
} else if(!empty($_POST['submit']) && $_GET['src'] == 'tasklist_time') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$hours = filter_var($_POST['hours'],FILTER_SANITIZE_STRING) * 3600;
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `tasklist_time` SET `contactid`='$staff', `timer_date`='$date', `work_time`=SEC_TO_TIME('$hours') WHERE `time_id`='$id'");
} else if($_GET['src'] == 'tasklist_time') {
	$timer = $dbc->query("SELECT *, TIME_TO_SEC(`work_time`) / 3600 `hours` FROM `tasklist_time` WHERE `time_id`='$id'")->fetch_assoc();
	$staff = $timer['contactid'];
	$date = $timer['timer_date'];
	$hours = $timer['hours'];
} ?>
<h3>Edit Time</h3>
<form action="" method="POST" class="form-horizontal">
	<input type="hidden" name="project" value="<?= $_GET['projectid'] ?>">
	<input type="hidden" name="src" value="<?= $_GET['src'] ?>">
	<input type="hidden" name="id" value="<?= $_GET['id'] ?>">
	<div class="form-group">
		<label class="col-sm-4">Staff:</label>
		<div class="col-sm-8">
			<select name="staff" class="chosen-select-deselect" data-placeholder="Select Staff"><option />
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name, name FROM contacts WHERE (category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0) OR `contactid`='{$staff}'")) as $contact) {
					echo "<option ".($staff == $contact['contactid'] ? 'selected' : '')." value='". $contact['contactid']."'>".$contact['full_name'].'</option>';
				} ?>
			</select>
		</div>
	</div>
	<?php if(in_array($_GET['src'],['tickets'])) { ?>
		<div class="form-group">
			<label class="col-sm-4">Start Time:</label>
			<div class="col-sm-8">
				<input type="text" name="start" value="<?= $start ?>" class="form-control datetimepicker" placeholder="Start Time">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4">End Time:</label>
			<div class="col-sm-8">
				<input type="text" name="end" value="<?= $end ?>" class="form-control datetimepicker" placeholder="End Time">
			</div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label class="col-sm-4">Hours:</label>
		<div class="col-sm-8">
			<input type="number" min="0" name="hours" value="<?= $hours ?>" class="form-control" placeholder="Hours Tracked">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Date:</label>
		<div class="col-sm-8">
			<input type="text" name="date" value="<?= $date ?>" class="form-control datepicker" placeholder="Date">
		</div>
	</div>
	<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
	<button class="btn brand-btn pull-right" name="submit" value="submit">Submit</button>
</form>