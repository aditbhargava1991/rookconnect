<?php
/*
Staff Listing
*/
include ('../include.php');
$rookconnect = get_software_name();
if(isset($_POST['save_reminder'])) {
	$reminderid = intval($_POST['reminderid']);
	$contacts = implode(',',$_POST['staff']);
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
		$sql = "INSERT INTO `reminders` (`contactid`, `reminder_type`, `reminder_date`, `reminder_time`, `subject`, `body`, `sender`, `src_table`)
			VALUES ('$contacts', 'STAFF', '$reminder_date', '$reminder_time', '$subject', '$body', '$sender', 'staff_reminders')";
	}
	$result = mysqli_query($dbc, $sql);
	exit("<script>window.location.replace('staff.php?tab=reminders');</script>");
}
?>
</head>
<script type="text/javascript" src="staff.js"></script>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('staff');
error_reporting(0);
$reminderid = '';
$chosen_staff = [];
$reminder_date = '';
$reminder_time = '08:00';
$subject = '';
$body = '';
$sender = get_email($dbc, $_SESSION['contactid']);
if(!empty($_GET['reminderid'])) {
	$reminderid = $_GET['reminderid'];
	if(isset($_GET['status']) && $_GET['status'] == 'archive') {
        $date_of_archival = date('Y-m-d');
		$reminder = mysqli_fetch_array(mysqli_query($dbc, "UPDATE `reminders` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `reminderid` = $reminderid"));

		echo "<script>alert('Reminder has been archived'); window.location = 'staff.php?tab=reminders';</script>";
	} else {
		$reminder = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid`='".intval($reminderid)."'"));
		$chosen_staff = explode(',',$reminder['contactid']);
		$reminder_date = $reminder['reminder_date'];
		$reminder_time = substr($reminder['reminder_time'],0,5);
		$subject = $reminder['subject'];
		$body = html_entity_decode($reminder['body']);
		$sender = $reminder['sender'];
	}
}
?>
<div class="container">
	<div class="row">
		<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
		<div class="main-screen contacts-list">
            <!-- Tile Header -->
            <div class="tile-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="staff.php?tab=active" class="default-color">Staff</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <div class="tile-container">

				<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	                <!-- Sidebar -->
	                <div class="collapsible tile-sidebar set-section-height">
	                	<ul class="sidebar">
	                		<li class="active">Reminder Details</li>
	                	</ul>
	                </div><!-- .tile-sidebar -->

					<!-- Main Screen -->
	                <div class="fill-to-gap tile-content set-section-height" style="padding: 0;">
						<div class="main-screen-details">
							<h4>Reminder</h4>

							<input type="hidden" name="reminderid" value="<?php echo $reminderid; ?>">
							<div class="form-group">
								<label class="col-sm-4 control-label">Staff Receiving Reminder:</label>
								<div class="col-sm-8">
									<select name="staff[]" data-placeholder="Select Staff" multiple class="chosen-select-deselect" class="form-control">
										<option value="select_all_staff">Remind All Staff</option>
										<?php $staff = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category`='Staff' AND `status`=1 AND `deleted`=0"),MYSQLI_ASSOC));
										foreach($staff as $staff_id) {
											echo '<option'.(in_array($staff_id,$chosen_staff) ? ' selected' : '').' value="'.$staff_id.'">'.get_contact($dbc,$staff_id)."</option>\n";
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Reminder Date:</label>
								<div class="col-sm-8">
									<input type="text" class="datepicker form-control" name="reminder_date" value="<?php echo $reminder_date; ?>" style="width:100%">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Reminder Time:</label>
								<div class="col-sm-8">
									<input type="text" class="timepicker form-control" name="reminder_time" value="<?php echo $reminder_time; ?>" style="width:100%">
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

							<button type='submit' name='save_reminder' value='Submit' class="btn brand-btn pull-right">Submit</button>
							<a href='staff.php?tab=reminders' class="btn brand-btn pull-right">Back</a>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
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

<?php include ('../footer.php'); ?>