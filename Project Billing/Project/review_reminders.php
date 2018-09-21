<?php
$search_from = '';
$search_to = '';
$clause = '';
if(!empty($_POST['search_from'])) {
	$search_from = $_POST['search_from'];
	$clause .= " AND reminder_date >= '$search_from'";
}
if(!empty($_POST['search_to'])) {
	$search_to = $_POST['search_to'];
	$clause .= " AND reminder_date <= '$search_to'";
}
?>
<form method="post" action="">
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<label for="search_from" class="control-label">Search From Date:</label>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="search_from" class="form-control datepicker" value="<?php echo $search_from; ?>">
	</div>
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<label for="search_from" class="control-label">Search To Date:</label>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="search_to" class="form-control datepicker" value="<?php echo $search_to; ?>">
	</div>
	<div class="col-sm-4 col-xs-12 col-lg-3 pad-top pull-xs-right">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered From - To Dates."><img src="../img/info.png" width="20"></a></span>
		<button type="submit" name="search_contacts_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
		<span class="popover-examples list-inline"><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all Project Reminders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="" class="btn brand-btn mobile-block">Display All</a>
	</div>
</form>
<?php
if(vuaed_visible_function($dbc, 'project') == 1) {
	echo '<a href="add_reminder.php?project_id='.$projectid.'" class="btn brand-btn mobile-block pull-right">Add Reminder</a>';
	echo '<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Reminder."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
} ?>
<div class="clearfix"></div>
<?php $rowsPerPage = 25;
$pageNum = (empty($_GET['page']) ? 1 : $_GET['page']);
$offset = $rowsPerPage * ($pageNum - 1);
$reminders = mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminder_type` = 'PROJECT".$projectid."' AND `deleted`=0 $clause LIMIT $offset, $rowsPerPage");
$sql_num = "SELECT COUNT(*) numrows FROM `reminders` WHERE `reminder_type` = 'PROJECT".$projectid."' AND `deleted`=0 $clause";
if(mysqli_num_rows($reminders) == 0)
{
	echo "<h1>No Reminders Found</h1>";
}
else {
	display_pagination($dbc, $sql_num, $pageNum, $rowsPerPage); ?>
	<table class="table table-bordered">
		<tr>
			<th>Staff Members</th>
			<th>Reminder Date</th>
			<th>Subject</th>
			<th>Sent</th>
			<th>Function</th>
		</tr>
		<?php while($reminder = mysqli_fetch_array($reminders)) { ?>
			<tr>
				<td data-title="Staff"><?php $staff = explode(',',$reminder['contactid']);
					foreach($staff as $person) {
							echo get_staff($dbc, $person)."<br />\n";
					} ?></td>
				<td data-title="Date"><?php echo $reminder['reminder_date']." (".$reminder['reminder_time'].")"; ?></td>
				<td data-title="Subject"><?php echo $reminder['subject']; ?></td>
				<td data-title="Sent"><?php echo ($reminder['sent'] == 1 ? 'Sent' : 'Not Sent'); ?></td>
				<td data-title=""><?php if(vuaed_visible_function($dbc, 'Staff')) { echo '<a href="add_reminder.php?reminderid='.$reminder['reminderid'].'&project_id='.$projectid.'">Edit</a>'; } ?></td>
			</tr>
		<?php } ?>
	</table>
<?php display_pagination($dbc, $sql_num, $pageNum, $rowsPerPage); 
} ?>