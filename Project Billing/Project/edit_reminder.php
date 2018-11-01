<?php
/*
Project Reminder
*/
include_once('../include.php');
$rookconnect = get_software_name();
if(isset($_POST['save_reminder'])) {
	$reminderid = intval($_POST['reminderid']);
	$contacts = implode(',',$_POST['staff']);
	$projectid = (empty($_GET['project_id']) ? 'N/A' : $_GET['project_id']);
	$reminder_date = filter_var($_POST['reminder_date'],FILTER_SANITIZE_STRING);
	$reminder_time = filter_var($_POST['reminder_time'],FILTER_SANITIZE_STRING);
    $sender = filter_var($_POST['sender'],FILTER_SANITIZE_STRING);
	$subject = filter_var($_POST['subject'],FILTER_SANITIZE_STRING);
    $body = filter_var(htmlentities($_POST['body']),FILTER_SANITIZE_STRING);

	if($reminderid > 0) {
		$sql = "UPDATE `reminders` SET `contactid`='$contacts', `reminder_date`='$reminder_date', `reminder_time`='$reminder_time',
			`subject`='$subject', `body`='$body', `sender`='$sender' WHERE `reminderid`='$reminderid'";
	}
	else {
		$sql = "INSERT INTO `reminders` (`contactid`, `reminder_type`, `reminder_date`, `reminder_time`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
			VALUES ('$contacts', 'PROJECT".$projectid."', '$reminder_date', '$reminder_time', '$subject', '$body', '$sender', 'projects', '$projectid')";
	}
	$result = mysqli_query($dbc, $sql);
	exit("<script>window.location.replace('projects.php?edit=".$projectid."&tab=reminders');</script>");
}
?>
</head>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised();
error_reporting(0);
$reminderid = '';
$projectid = (empty($_GET['project_id']) ? 'N/A' : $_GET['project_id']);
$chosen_staff = [];
$reminder_date = date('Y-m-d', strtotime('7 days'));
$reminder_time = '08:00';
$subject = 'Reminder regarding Project #'.$projectid;
$project_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$body = 'You have received a reminder about a project. Please review the project.<br /><br />
	<a href="projects.php?edit='.$projectid.'">Project #'.$projectid.': '.$project_info['project_name'].'</a>';
$sender = get_email($dbc, $_SESSION['contactid']);
if(!empty($_GET['reminderid'])) {
	$reminderid = $_GET['reminderid'];
	$reminder = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid`='".intval($reminderid)."'"));
	$chosen_staff = explode(',',$reminder['contactid']);
	$reminder_date = $reminder['reminder_date'];
	$reminder_time = substr($reminder['reminder_time'],0,5);
	$subject = $reminder['subject'];
	$body = html_entity_decode($reminder['body']);
	$sender = $reminder['sender'];
}
?>
<div class="container">
	<form method="post" action="" class="form-horizontal">
		<input type="hidden" name="reminderid" value="<?php echo $reminderid; ?>">
		<div class="form-group">
			<label class="col-sm-4 control-label">Staff Receiving Reminder:</label>
			<div class="col-sm-8">
				<select name="staff[]" data-placeholder="Select Staff" multiple class="chosen-select-deselect" class="form-control"><option></option>
					<option value="select_all_staff">Remind All Staff</option>
					<?php $staff = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0 ORDER BY `last_name`, `first_name`"));
					foreach($staff as $contact) {
						echo '<option'.(in_array($contact['contactid'],$chosen_staff) ? ' selected' : '').' value="'.$row['contactid'].'">'.$contact['first_name'].' '.$contact['last_name']."</option>\n";
					} ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Reminder Date:</label>
			<div class="col-sm-8">
				<input type="text" class="datepicker" name="reminder_date" class="form-control" value="<?php echo $reminder_date; ?>" style="width:100%">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Reminder Time:</label>
			<div class="col-sm-8">
				<input type="text" class="timepicker" name="reminder_time" class="form-control" value="<?php echo $reminder_time; ?>" style="width:100%">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Sending Email Address:</label>
			<div class="col-sm-8">
				<input type="text" name="sender" class="form-control" value="<?php echo $sender; ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Subject:</label>
			<div class="col-sm-8">
				<input type="text" name="subject" class="form-control" value="<?php echo $subject; ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Body:</label>
			<div class="col-sm-8">
				<textarea name="body" class="form-control"><?php echo $body; ?></textarea>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-12 clearfix">
				<span class="popover-examples list-inline pull-left" style="margin-top:12px;"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Clicking this will discard this Reminder."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="projects.php?edit=<?php echo $projectid; ?>&tab=reminders" class="btn brand-btn btn-lg pull-left">Back</a>
				<button type="submit" name="save_reminder" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples list-inline pull-right" style="margin-top:12px;"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to save this Reminder."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			</div>
		</div>
	</form>
</div>
<script>
$(document).ready(function() {
	$('[name="staff[]"]').change(function() {
		$(this).find('[value=select_all_staff]:selected').each(function() {
			$('[name="staff[]"] option').attr('selected',true);
			$(this).removeAttr('selected')
		});
		$(this).trigger('change.select2');
	});
});
</script>

<?php include_once('../footer.php'); ?>
